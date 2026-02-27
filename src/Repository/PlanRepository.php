<?php

namespace App\Repository;

use PDO;

class PlanRepository 
{

    private PDO $connection;
    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }

    public function searchPlan(string $type): mixed {
        $sqlPlan = "SELECT id FROM plans WHERE name = '$type'";
        $stmt = $this->connection->query($sqlPlan);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}