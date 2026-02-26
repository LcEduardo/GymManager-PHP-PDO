<?php

require_once 'vendor/autoload.php';

use App\Infra\Connection;
use App\Domain\User;
use App\Repository\UserRepository;

$pdo = Connection::getConnection();
$c = new UserRepository($pdo);

$user = new User(
    null, 'Alvin', 'alvinoesquilo@gmail.com', '21022026', '2026-02-21', '1999542411', '1'
);

$c->createUser($user);



