<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Domain\Professional;
use App\Infra\Connection;

$pdo = Connection::getConnection();

$profissional = new Professional(
    id: null,
    name: 'Dr. João Silva',
    specialty: 'Cardiologia',
    phone: '(11)98765-4321',
    email: 'joao.silva@hospital.com',
    active: true
); 

$sql = "INSERT INTO professionals (name, specialty, phone, email, active) 
        VALUES (:name, :specialty, :phone, :email, :active)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $profissional->getFullName(), PDO::PARAM_STR);
    $stmt->bindValue(':specialty', $profissional->getSpecialty(), PDO::PARAM_STR);
    $stmt->bindValue(':phone', $profissional->getPhone(), PDO::PARAM_STR);
    $stmt->bindValue(':email', $profissional->getEmail(), PDO::PARAM_STR);
    $stmt->bindValue(':active', $profissional->isActive(), PDO::PARAM_BOOL);   
    $stmt->execute();
    echo "Profissional created successfully!";
} catch (PDOException $e) {
    echo "Error creating profissional: " . $e->getMessage();
}

