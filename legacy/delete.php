<?php

// Exemplo legado pre-MVC mantido apenas para estudo da evolucao do projeto.

use App\Infra\Connection;
use App\Repository\UserRepository;

$connection = Connection::getConnection();
$repository = new UserRepository($connection);

$repository->deleteUser(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT));

header('Location: /');
