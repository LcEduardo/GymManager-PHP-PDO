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
                INNER JOIN plans ON users_plans.plan_id = plans.id
                WHERE users_plans.payment_status = 'paid'
                AND users_plans.start_date BETWEEN :firstDay AND :lastDay";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':firstDay', $firstDay, PDO::PARAM_STR);
        $stmt->bindValue(':lastDay',  $lastDay,  PDO::PARAM_STR);
        $stmt->execute();

        return (float) $stmt->fetchColumn();

    }

    public function countExpiringToday(): int {
        $sql = "SELECT COUNT(*) 
                FROM users_plans 
                WHERE end_date = :today 
                AND payment_status != 'paid'";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':today', date('Y-m-d'), PDO::PARAM_STR);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function getAllSubscriptionsWithUsers(string $filtro = 'todos'): array {
        $hoje = date('Y-m-d');
    
        $sql = "SELECT 
                    users.full_name,
                    plans.name        AS plan_name,
                    users_plans.start_date,
                    users_plans.end_date,
                    users_plans.payment_status
                FROM users_plans
                INNER JOIN users ON users_plans.user_id = users.id
                INNER JOIN plans ON users_plans.plan_id = plans.id";
    
        if ($filtro === 'paid') {
            $sql .= " WHERE users_plans.payment_status = 'paid'";
        } elseif ($filtro === 'pending') {

            $sql .= " WHERE users_plans.payment_status != 'paid'
                        AND users_plans.end_date >= :hoje";
        } elseif ($filtro === 'vencido') {

            $sql .= " WHERE users_plans.payment_status != 'paid'
                        AND users_plans.end_date < :hoje";
        }
    
        $sql .= " ORDER BY users_plans.end_date ASC";
    
        $stmt = $this->connection->prepare($sql);
    
        if (in_array($filtro, ['pending', 'vencido'])) {
            $stmt->bindValue(':hoje', $hoje, PDO::PARAM_STR);
        }
    
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}