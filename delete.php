<?php

require 'vendor/autoload.php';

use App\Infra\Connection;
use App\Repository\UserRepository;

$connection = Connection::getConnection();
$repository = new UserRepository($connection);

$repository->deleteUser($_GET['id']);

header('Location:index.php');
?>