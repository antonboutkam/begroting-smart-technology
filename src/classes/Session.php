<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes;

final class Session
{
    public static function start(): void
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $type, string $message): void
    {
        $_SESSION['flashes'][] = ['type' => $type, 'message' => $message];
    }

    public static function pullFlashes(): array
    {
        $flashes = $_SESSION['flashes'] ?? [];
        unset($_SESSION['flashes']);

        return $flashes;
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $_SESSION = [];
    }
}
