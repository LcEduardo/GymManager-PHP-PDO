<?php

use App\Infra\Connection;
use App\Repository\UserRepository;

$connection = Connection::getConnection();
$repository = new UserRepository($connection);

$repository->deleteUser(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT));

header('Location: /');
?>
