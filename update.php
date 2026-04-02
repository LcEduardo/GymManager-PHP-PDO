<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Infra\Connection;
use App\Repository\UserRepository;
use App\Repository\SubscriptionRepository;

$connection = Connection::getConnection();
$userRepository = new UserRepository($connection);
$subscriptionRepository = new SubscriptionRepository($connection);

$id   = filter_input(INPUT_POST, 'id',    FILTER_VALIDATE_INT);
$name  = filter_input(INPUT_POST, 'name',  FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);

if (isset($id, $name, $email, $phone)) {
    $user = $userRepository->searchUser($id);
    $user->setFullName($name);
    $user->setEmail($email);
    $user->setPhone($phone);

    $userRepository->updateUser($user);
    $subscriptionRepository->updatePlan($id, filter_input(INPUT_POST, 'plan_id', FILTER_VALIDATE_INT));

    header('Location: /');
}