<?php

use App\Domain\UserSubscription;
use App\Infra\Connection;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;

require_once 'vendor/autoload.php';

$pdo = Connection::getConnection();

$planRepository = new PlanRepository($pdo);
$plan = $planRepository->searchPlan('Premium');

$userRepository = new UserRepository($pdo);
$user = $userRepository->searchUser(2);
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