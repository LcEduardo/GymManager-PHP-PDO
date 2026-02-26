<?php

namespace App\Repository;

require_once 'vendor/autoload.php';

use PDO;
use PDOException;
class SubscriptionRepository 
{
    private PDO $connection;
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    
}