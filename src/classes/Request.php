<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes;

final class Request
{
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $request,
        private readonly array $files,
        private readonly array $server,
    ) {
    }

    public static function fromGlobals(): self
    {
        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/',
            $_GET,
            $_POST,
            $_FILES,
            $_SERVER
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }

        return $this->query[$key] ?? $default;
    }

    public function input(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->request;
        }

        return $this->request[$key] ?? $default;
    }

    public function file(string $key): array|null
    {
        return $this->files[$key] ?? null;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function json(): array
    {
        $content = file_get_contents('php://input');
        if ($content === false || $content === '') {
            return [];
        }

        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }
}
