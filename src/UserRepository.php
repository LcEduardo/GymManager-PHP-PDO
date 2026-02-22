<?php

namespace App\Database;

require_once 'vendor/autoload.php';

use App\Database\User;
use PDO;
use PDOException;

class UserRepository 
{
    private PDO $connection;
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function createUser(User $user) {

        try {
            $sql = "INSERT INTO users (full_name, email, password, phone, created_at, status) 
                    VALUES (:full_name, :email, :password, :phone, :created_at, :status)";

            $stmt =  $this->connection->prepare($sql);
            $stmt->bindValue(':full_name', $user->fullName(), PDO::PARAM_STR);
            $stmt->bindValue(':email', $user->email(), PDO::PARAM_STR);
            $stmt->bindValue(':password', $user->password(), PDO::PARAM_STR);
            $stmt->bindValue(':phone', $user->phone(), PDO::PARAM_STR);
            $stmt->bindValue(':created_at', $user->date(), PDO::PARAM_STR);
            $stmt->bindValue(':status', $user->status(), PDO::PARAM_STR);

            $stmt->execute();
            echo "New User Created";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
            
    }
}