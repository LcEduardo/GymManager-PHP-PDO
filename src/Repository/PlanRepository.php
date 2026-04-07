<?php

namespace App\Repository;

use App\Domain\Plan;
use PDO;

class PlanRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function searchPlan(string $type): mixed
    {
        $sqlPlan = "SELECT id FROM plans WHERE name = :name;";
        $stmt = $this->connection->prepare($sqlPlan);
        $stmt->bindValue(':name', $type, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllPlans(): array
    {
        $sql = "SELECT * FROM plans WHERE active = true ORDER BY id ASC;";
        $stmt = $this->connection->query($sql);
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'formatPlan'], $plans);
    }

    public function findById(int $id): ?Plan
    {
        $sql = "SELECT * FROM plans WHERE id = :id LIMIT 1;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$plan) {
            return null;
        }

        return $this->formatPlan($plan);
    }

    private function formatPlan(array $plan): Plan
    {
        return new Plan(
            (int) $plan['id'],
            $plan['name'],
            (float) $plan['price'],
            $plan['description'],
            (bool) $plan['active'],
            (int) ($plan['durantio_days'] ?? $plan['duration_days'])
        );
    }
}
