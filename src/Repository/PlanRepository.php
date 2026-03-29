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
        $sqlPlan = "SELECT id FROM plans WHERE name = ':name;";
        $stmt = $this->connection->prepare($sqlPlan);
        $stmt->bindValue(':name', $type, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}