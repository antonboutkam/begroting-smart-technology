<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes\Repositories;

use PDO;

final class UserRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, username, email, display_name, created_at FROM users ORDER BY display_name ASC')->fetchAll();
    }

    public function findByLogin(string $login): ?array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM users WHERE username = :login OR email = :login LIMIT 1'
        );
        $statement->execute(['login' => $login]);

        $user = $statement->fetch();
        return $user ?: null;
    }

    public function create(string $username, string $email, string $displayName, string $password): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO users (username, email, display_name, password_hash)
             VALUES (:username, :email, :display_name, :password_hash)'
        );

        $statement->execute([
            'username' => $username,
            'email' => $email,
            'display_name' => $displayName,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }
}
