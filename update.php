<?php

use App\Domain\User;
use App\Infra\Connection;
use App\Repository\UserRepository;
use App\Repository\SubscriptionRepository;

$connection = Connection::getConnection();
$userRepository = new UserRepository($connection);
$subscriptionRepository = new SubscriptionRepository($connection);

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$birthDate = filter_input(INPUT_POST, 'birthdate', FILTER_DEFAULT);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);

if ($id && $name && $email) {
    $user = $userRepository->searchUser($id);

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

    $userRepository->updateUser($updatedUser);
    $subscriptionRepository->updatePlan($id, filter_input(INPUT_POST, 'plan_id', FILTER_VALIDATE_INT));

    header('Location: /');
}
