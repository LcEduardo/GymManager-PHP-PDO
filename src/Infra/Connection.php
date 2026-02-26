<?php

namespace App\Infra;

use PDO;
use PDOException;

class Connection 
{
    public static function getConnection(): PDO {
    
        $databasePath = __DIR__ . '/../../gym.sqlite';

        try {
            return  new PDO('sqlite:' . $databasePath);
        }catch(PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
}



