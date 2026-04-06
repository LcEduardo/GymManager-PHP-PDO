<?php

namespace App\Infra;

use PDO;
use PDOException;

class Connection 
{
    public static function getConnection(): PDO {
        self::loadEnvironment();

        self::assertDriverIsAvailable();

        try {
            $host = self::env('DB_HOST', '127.0.0.1');
            $port = self::env('DB_PORT', '5432');
            $database = self::env('DB_DATABASE', 'gym');
            $username = self::env('DB_USERNAME', 'gym_user');
            $password = self::env('DB_PASSWORD', 'gym_password');

            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $host,
                $port,
                $database
            );

            $connection = new PDO($dsn, $username, $password);

            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $connection;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    private static function assertDriverIsAvailable(): void
    {
        $availableDrivers = PDO::getAvailableDrivers();

        if (in_array('pgsql', $availableDrivers, true)) {
            return;
        }

        throw new PDOException(sprintf(
            'PDO driver "pgsql" is not enabled in this PHP installation. Available drivers: %s.',
            $availableDrivers === [] ? 'none' : implode(', ', $availableDrivers)
        ));
    }

    private static function loadEnvironment(): void
    {
        $envPath = __DIR__ . '/../../.env';

        if (!is_file($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            if (
                str_starts_with($value, '"') && str_ends_with($value, '"') ||
                str_starts_with($value, "'") && str_ends_with($value, "'")
            ) {
                $value = substr($value, 1, -1);
            }

            if (getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

    private static function env(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }
}



