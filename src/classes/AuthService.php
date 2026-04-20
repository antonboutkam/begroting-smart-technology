<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes;

use Roc\SmartTech\Begroting\Classes\Repositories\UserRepository;

final class AuthService
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function attempt(string $login, string $password): bool
    {
        $user = $this->users->findByLogin($login);
        if ($user === null || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        Session::set('user', [
            'id' => (int) $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'display_name' => $user['display_name'],
        ]);

        return true;
    }

    public function check(): bool
    {
        return Session::get('user') !== null;
    }

    public function user(): ?array
    {
        $user = Session::get('user');
        return is_array($user) ? $user : null;
    }

    public function logout(): void
    {
        Session::remove('user');
    }
}
