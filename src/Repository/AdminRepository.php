<?php

namespace App\Repository;

use PDO;

class AdminRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT id, name, email, password FROM adms WHERE email = :email LIMIT 1';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $admin = $stmt->fetch();

        return $admin ?: null;
    }
}
