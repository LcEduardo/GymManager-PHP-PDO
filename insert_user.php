<?php

use App\Database\Connection;

require_once 'vendor/autoload.php';

$pdo = Connection::getConnection();

$sql = "INSERT INTO users (full_name, email, password, phone, created_at, status) 
VALUES ('Lucas Eduardo', 'lucaseduardo@gmail.com', '12345', '19995424123', '2026-02-19', '1')";

$statement = $pdo->prepare($sql);
if ($statement->execute()) {
    echo "Aluno incluido";
}
