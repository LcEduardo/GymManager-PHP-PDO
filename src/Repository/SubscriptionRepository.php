<?php

namespace App\Repository;

use App\Domain\UserSubscription;
use PDO;
use PDOException;
class SubscriptionRepository 
{
    private PDO $connection;
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function createSubscription(UserSubscription $subscription) {
        $sql = "INSERT INTO users_plans (user_id, plan_id, start_date, end_date, payment_status)
                VALUES (:user_id, :plan_id, :start_date, :end_date, :payment_status);";

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':user_id', $subscription->getUserId(), PDO::PARAM_INT);
            $stmt->bindValue(':plan_id', $subscription->getPlanId(), PDO::PARAM_INT);
            $stmt->bindValue(':start_date', $subscription->getStartDate(), PDO::PARAM_STR);
            $stmt->bindValue(':end_date', $subscription->getEndDate(), PDO::PARAM_STR);
            $stmt->bindValue(':payment_status', $subscription->getPaymentStatus(), PDO::PARAM_STR);
            
            $stmt->execute();
            return true;
        }catch(PDOException $e){
            return false;
        }
    }

    public function countPlan(string $plan): int {
        $sql = "SELECT COUNT(*) FROM users_plans inner JOIN plans ON users_plans.plan_id = plans.id WHERE plans.name = :plan";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':plan', $plan, PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }  

    public function findByUserId(int $userId): ?array {
        $sql = "SELECT * FROM users_plans WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updatePlan(int $userId, int $planId): void {
        $sql = "UPDATE users_plans SET plan_id = :plan_id WHERE user_id = :user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':plan_id', $planId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getMonthlyRevenue(string $firstDay, string $lastDay): float {
        $sql = "SELECT SUM(plans.price)
                FROM users_plans
                INNER JOIN users  ON users_plans.user_id = users.id
                INNER JOIN plans  ON users_plans.plan_id = plans.id
                WHERE users.created_at BETWEEN :firstDay AND :lastDay";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':firstDay', $firstDay, PDO::PARAM_STR);
        $stmt->bindValue(':lastDay',  $lastDay,  PDO::PARAM_STR);
        $stmt->execute();

        return (float) $stmt->fetchColumn();

    }
    
}