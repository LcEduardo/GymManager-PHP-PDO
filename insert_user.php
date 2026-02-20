<?php

require_once 'vendor/autoload.php';

use App\Database\Connection;
use App\Database\User;

$pdo = Connection::getConnection();

$userBatman = new User(
    null, 'The Batman', 'thebatman@gmail.com', '09090', '2026-02-20', '1999542413', '1'
);

try {

    $pdo = Connection::getConnection();

    $sql = "INSERT INTO users (full_name, email, password, phone, created_at, status) 
            VALUES (:full_name, :email, :password, :phone, :created_at, :status)";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':full_name', $userBatman->fullName(), PDO::PARAM_STR);
    $stmt->bindValue(':email', $userBatman->email(), PDO::PARAM_STR);
    $stmt->bindValue(':password', $userBatman->password(), PDO::PARAM_STR);
    $stmt->bindValue(':phone', $userBatman->phone(), PDO::PARAM_STR);
    $stmt->bindValue(':created_at', $userBatman->date(), PDO::PARAM_STR);
    $stmt->bindValue(':status', $userBatman->status(), PDO::PARAM_STR);

    $stmt->execute();

    echo "New User Created";
}catch(PDOException $e) {
    $e->getMessage();
}
