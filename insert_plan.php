<?php

require_once 'vendor/autoload.php';

use App\Database\Connection;
use App\Database\Plan;

$pdo = Connection::getConnection();

$sql = "INSERT INTO plans (name, durantio_days, description, active, price)
        VALUES (:name, :durantio_days, :description, :active, :price)";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', 'Basic', PDO::PARAM_STR);
$stmt->bindValue(':durantio_days', 30, PDO::PARAM_INT);
$stmt->bindValue(':description', 'Você pode ir 3 vezes na semana', PDO::PARAM_INT); //passar desc para inglês;
$stmt->bindValue(':active', 1, PDO::PARAM_INT);
$stmt->bindValue(':price', 100, PDO::PARAM_FLOAT);//ver como indico o parametro para decial;
