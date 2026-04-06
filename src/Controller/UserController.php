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
    public function create(): void
    {
        require dirname(__DIR__, 2) . '/views/users/create.php';
    }

    public function store(): void
    {
        $connection = Connection::getConnection();
        $userRepository = new UserRepository($connection);
        $subscriptionRepository = new SubscriptionRepository($connection);
        $planRepository = new PlanRepository($connection);

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

        $plan = $planRepository->searchPlan($planName);

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

        $userRepository->createUser($user);

        $subscription = new UserSubscription(
            null,
            $user->id(),
            (int) $plan['id'],
            date('Y-m-d'),
            date('Y-m-d', strtotime('+1 month')),
            'pending'
        );

        $result = $subscriptionRepository->createSubscription($subscription);

        if (!$result) {
            http_response_code(500);
            echo 'Erro ao criar assinatura.';
            return;
        }

        header('Location: /');
    }
}
