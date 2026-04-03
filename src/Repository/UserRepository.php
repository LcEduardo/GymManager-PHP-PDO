<?php

namespace App\Repository;

use App\Domain\User;
use PDO;
use PDOException;
use PDOStatement;

class UserRepository 
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function createUser(User $user)
    {
        try {
            $sql = "INSERT INTO users (full_name, email, password, phone, birth_date, created_at, status) 
                    VALUES (:full_name, :email, :password, :phone, :birth_date, :created_at, :status)
                    RETURNING id";

            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':full_name', $user->fullName(), PDO::PARAM_STR);
            $stmt->bindValue(':email', $user->email(), PDO::PARAM_STR);
            $stmt->bindValue(':password', $user->password(), PDO::PARAM_STR);
            $this->bindNullableString($stmt, ':phone', $user->phone());
            $this->bindNullableString($stmt, ':birth_date', $user->birthDate());
            $stmt->bindValue(':created_at', $user->createdAt(), PDO::PARAM_STR);
            $stmt->bindValue(':status', $user->status(), PDO::PARAM_STR);

            $stmt->execute();
            $id = $stmt->fetchColumn();

            $user->setId((int) $id);

            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function searchUser(int $id): mixed
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->formatUser($user);
    }

    public function formatUser($user): User
    {
        return new User(
            $user['id'],
            $user['full_name'],
            $user['email'],
            $user['password'],
            $user['created_at'],
            $user['birth_date'] ?? null,
            $user['phone'] ?? null,
            $user['status']
        );
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM users;";
        $stmt = $this->connection->query($sql);

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'formatUser'], $users);
    }

    public function updateUser(User $user): void
    {
        $sql = "UPDATE users 
                SET full_name = :full_name, email = :email, password = :password, phone = :phone, birth_date = :birth_date, status = :status 
                WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':full_name', $user->fullName(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->email(), PDO::PARAM_STR);
        $stmt->bindValue(':password', $user->password(), PDO::PARAM_STR);
        $this->bindNullableString($stmt, ':phone', $user->phone());
        $this->bindNullableString($stmt, ':birth_date', $user->birthDate());
        $stmt->bindValue(':status', $user->status(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $user->id(), PDO::PARAM_INT);

        $stmt->execute();
    }

    public function deleteUser(int $id): void
    {
        $sqlPlan = "DELETE FROM users_plans WHERE user_id = :id;";
        $stmtPlan = $this->connection->prepare($sqlPlan);
        $stmtPlan->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtPlan->execute();

        $sql = "DELETE FROM users WHERE id = :id;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function countUsersByStatus(string $status): int
    {
        $sql = "SELECT COUNT(*) FROM users WHERE status = :status;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function usersActivesThisMonth(string $lastDay, string $firstDay): string
    {
        $sql = "SELECT COUNT(*) FROM users WHERE status = 'S' AND created_at BETWEEN :firstDay and :lastDay;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':firstDay', $firstDay, PDO::PARAM_STR);
        $stmt->bindValue(':lastDay', $lastDay, PDO::PARAM_STR);
        $stmt->execute();
        return " ▲ +{$stmt->fetchColumn()} este mês";
    }

    private function bindNullableString(PDOStatement $stmt, string $parameter, ?string $value): void
    {
        if ($value === null || $value === '') {
            $stmt->bindValue($parameter, null, PDO::PARAM_NULL);
            return;
        }

        $stmt->bindValue($parameter, $value, PDO::PARAM_STR);
    }
}
