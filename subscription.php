<?php

use App\Domain\UserSubscription;
use App\Infra\Connection;
use App\Repository\SubscriptionRepository;

require_once 'vendor/autoload.php';

$pdo = Connection::getConnection();

$sqlPlan = "SELECT id FROM plans WHERE name = 'Basic'";
$stmt1 = $pdo->query($sqlPlan);
$plan = $stmt1->fetch(PDO::FETCH_ASSOC);

$sqlUser = "SELECT * FROM users WHERE id = 1 ;";
$stmt = $pdo->query($sqlUser);
//var_dump($stmt->fetch(PDO::FETCH_ASSOC));

$user = $stmt->fetch(PDO::FETCH_ASSOC);

//var_dump($user['id']);

$subscription = new UserSubscription(
    id: null,
    user_id: $user['id'],
    plan_id: $plan['id'],
    start_date: '2026-02-01',
    end_date: '2026-02-28',
    payment_status: 'Pendente'
);

// var_dump($subscription);

$subsRepository = new SubscriptionRepository($pdo);
$subsRepository->createSubscription($subscription);