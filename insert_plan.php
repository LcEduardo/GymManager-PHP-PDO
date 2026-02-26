<?php

require_once 'vendor/autoload.php';

use App\Infra\Connection;
use App\Domain\Plan;

$pdo = Connection::getConnection();

$sql = "INSERT INTO plans (name, durantio_days, description, active, price)
        VALUES (:name, :durantio_days, :description, :active, :price)";

$premiumPlan = new Plan(null, 'Premium', 150.49, 'You can go every day', 1, 360);

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $premiumPlan->getName(), PDO::PARAM_STR);
$stmt->bindValue(':durantio_days', $premiumPlan->getDays(), PDO::PARAM_INT);
$stmt->bindValue(':description', $premiumPlan->getDescription(), PDO::PARAM_STR); 
$stmt->bindValue(':active', $premiumPlan->getActive(), PDO::PARAM_INT);
$stmt->bindValue(':price', $premiumPlan->getPrice(), PDO::PARAM_STR);

if ($stmt->execute()) {
        echo "Plan create";
}
