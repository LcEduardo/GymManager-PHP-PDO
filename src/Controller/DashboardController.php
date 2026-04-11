<?php

namespace App\Controller;

use App\Infra\Connection;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use PDOException;

class DashboardController
{
    private UserRepository $userRepository;
    private SubscriptionRepository $subscriptionRepository;

    public function __construct()
    {
        $connection = Connection::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->subscriptionRepository = new SubscriptionRepository($connection);
    }

    public function index(): void
    {
        try {
            $users = $this->userRepository->getAllUsers();
            $usersActive = $this->userRepository->countUsersByStatus('S');
            $usersPremium = $this->subscriptionRepository->countPlan('Premium');

            [$firstDay, $lastDay] = $this->getMonthRange();

            $usersActivesThisMonth = $this->userRepository->usersActivesThisMonth($lastDay, $firstDay);
            $monthlyRevenue = $this->subscriptionRepository->getMonthlyRevenue($firstDay, $lastDay);
            $dueSubscriptions = $this->subscriptionRepository->countDueSubscriptions();
        } catch (PDOException $exception) {
            http_response_code(500);
            echo $exception->getMessage();
            return;
        }

        require dirname(__DIR__, 2) . '/views/dashboard/index.php';
    }

    private function getMonthRange(?int $year = null, ?int $month = null): array
    {
        $year = $year ?? (int) date('Y');
        $month = $month ?? (int) date('m');

        $firstDay = date('Y-m-01', strtotime("$year-$month-01"));
        $lastDay = date('Y-m-t', strtotime("$year-$month-01"));

        return [$firstDay, $lastDay];
    }
}
