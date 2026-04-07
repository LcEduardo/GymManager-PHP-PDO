<?php

namespace App\Controller;

use App\Domain\User;
use App\Domain\UserSubscription;
use App\Infra\Connection;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;

class UserController
{
    private UserRepository $userRepository;
    private SubscriptionRepository $subscriptionRepository;
    private PlanRepository $planRepository;

    public function __construct()
    {
        $connection = Connection::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->subscriptionRepository = new SubscriptionRepository($connection);
        $this->planRepository = new PlanRepository($connection);
    }

    public function create(): void
    {
        require dirname(__DIR__, 2) . '/views/users/create.php';
    }

    public function store(): void
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
        $birthDate = filter_input(INPUT_POST, 'birthdate', FILTER_DEFAULT);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
        $planName = filter_input(INPUT_POST, 'plan', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$name || !$email || !$password || !$planName) {
            http_response_code(422);
            echo 'Dados invalidos.';
            return;
        }

        $plan = $this->planRepository->searchPlan($planName);

        if (!$plan) {
            http_response_code(422);
            echo 'Plano invalido.';
            return;
        }

        $user = new User(
            null,
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            date('Y-m-d'),
            $birthDate ?: null,
            $phone ?: null,
            'S'
        );

        $this->userRepository->createUser($user);

        $subscription = new UserSubscription(
            null,
            $user->id(),
            (int) $plan['id'],
            date('Y-m-d'),
            date('Y-m-d', strtotime('+1 month')),
            'pending'
        );

        $result = $this->subscriptionRepository->createSubscription($subscription);

        if (!$result) {
            http_response_code(500);
            echo 'Erro ao criar assinatura.';
            return;
        }

        header('Location: /');
    }

    public function edit(): void
    {
        $userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$userId) {
            http_response_code(404);
            echo 'Usuario nao encontrado.';
            return;
        }

        $user = $this->userRepository->searchUser($userId);

        if (!$user) {
            http_response_code(404);
            echo 'Usuario nao encontrado.';
            return;
        }

        $subscription = $this->subscriptionRepository->findByUserId($user->id());
        $currentPlanId = isset($subscription['plan_id']) ? (int) $subscription['plan_id'] : null;
        $plans = $this->planRepository->getAllPlans();

        require dirname(__DIR__, 2) . '/views/users/edit.php';
    }

    public function update(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $birthDate = filter_input(INPUT_POST, 'birthdate', FILTER_DEFAULT);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
        $planId = filter_input(INPUT_POST, 'plan_id', FILTER_VALIDATE_INT);

        if (!$id || !$name || !$email || !$planId) {
            http_response_code(422);
            echo 'Dados invalidos.';
            return;
        }

        $user = $this->userRepository->searchUser($id);

        if (!$user) {
            http_response_code(404);
            echo 'Usuario nao encontrado.';
            return;
        }

        $plan = $this->planRepository->findById($planId);

        if (!$plan) {
            http_response_code(422);
            echo 'Plano invalido.';
            return;
        }

        $updatedUser = new User(
            $user->id(),
            $name,
            $email,
            $user->password(),
            $user->createdAt(),
            $birthDate ?: null,
            $phone ?: null,
            $user->status()
        );

        $this->userRepository->updateUser($updatedUser);
        $this->subscriptionRepository->updatePlan($id, $plan->getId());

        header('Location: /');
    }
}
