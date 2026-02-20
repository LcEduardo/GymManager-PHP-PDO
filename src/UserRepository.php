<?php

require_once 'vendor/autoload.php';

use App\Database\User;

class UserRepository 
{
    private PDO $connection;
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function createUser(User $user) {

    }
}