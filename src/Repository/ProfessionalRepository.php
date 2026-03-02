<?php

namespace App\Repository;

use PDO;
use PDOException;
use App\Domain\Professional;

class ProfessionalRepository 
{
    private PDO $connection;
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function createProfessional(Professional $professional) {

        $sql = "INSERT INTO professionals (name, specialty, phone, email, active) 
        VALUES (:name, :specialty, :phone, :email, :active)";

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':name', $professional->getFullName(), PDO::PARAM_STR);
            $stmt->bindValue(':specialty', $professional->getSpecialty(), PDO::PARAM_STR);
            $stmt->bindValue(':phone', $professional->getPhone(), PDO::PARAM_STR);
            $stmt->bindValue(':email', $professional->getEmail(), PDO::PARAM_STR);
            $stmt->bindValue(':active', $professional->isActive(), PDO::PARAM_BOOL);   
            $stmt->execute();
            echo "Professional created successfully!";
        } catch (PDOException $e) {
            echo "Error creating professional: " . $e->getMessage();
        }


    }
}