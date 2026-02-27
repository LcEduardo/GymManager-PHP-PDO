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
            echo "Create Subscription!";
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
}