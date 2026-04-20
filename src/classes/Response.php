<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes;

final class Response
{
    public function __construct(
        private readonly string $content,
        private readonly int $status = 200,
        private readonly array $headers = [],
    ) {
    }

    public static function redirect(string $location): self
    {
        return new self('', 302, ['Location' => $location]);
    }

    public static function json(array $payload, int $status = 200): self
    {
        return new self(
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}',
            $status,
            ['Content-Type' => 'application/json; charset=utf-8']
        );
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }

        echo $this->content;
    }
}
