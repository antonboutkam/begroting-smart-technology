<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    public static function connect(): PDO
    {
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $name = $_ENV['DB_NAME'] ?? '';
        $user = $_ENV['DB_USER'] ?? '';
        $pass = $_ENV['DB_PASS'] ?? '';

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name),
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $exception) {
            throw new RuntimeException('Databaseverbinding mislukt: ' . $exception->getMessage(), 0, $exception);
        }

        return $pdo;
    }
}
