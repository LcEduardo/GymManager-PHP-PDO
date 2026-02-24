<?php

use App\Database\UserSubscription;

require_once 'vendor/autoload.php';

$idPlan = "SELECT id FROM plans WHERE name = 'Basic'";



// criar um objeto user com os dados já cadastrados no database ;

$sqlUser = "SELECT id, full_name, email, password, phone, "

$firstSubscription = new UserSubscription(
    null,
    1,
    $idPlan,
);