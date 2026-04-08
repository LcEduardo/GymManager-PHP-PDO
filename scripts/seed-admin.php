<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infra\Connection;

$connection = Connection::getConnection();

$sql = <<<SQL
    INSERT INTO adms (name, email, password)
    VALUES (:name, :email, :password)
    ON CONFLICT (email) DO NOTHING
SQL;

$statement = $connection->prepare($sql);
$statement->bindValue(':name', 'Administrador Principal', PDO::PARAM_STR);
$statement->bindValue(':email', 'admin@gymmanager.local', PDO::PARAM_STR);
$statement->bindValue(':password', password_hash('admin123', PASSWORD_ARGON2ID), PDO::PARAM_STR);
$statement->execute();

echo "Administrador inicial processado com sucesso." . PHP_EOL;
