<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Infra\Connection;
use App\Repository\UserRepository;

$connection = Connection::getConnection();
$userRepository = new UserRepository($connection);

if (isset($_POST['id'], $_POST['name'], $_POST['email'], $_POST['phone'])) {
    $user = $userRepository->searchUser((int)$_POST['id']);
    $user->setFullName($_POST['name']);
    $user->setEmail($_POST['email']);
    $user->setPhone($_POST['phone']);

    $userRepository->updateUser($user);

    header('Location: index.php');
}
?>