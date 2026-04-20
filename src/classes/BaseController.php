<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes;

use PDO;
use Twig\Environment;

abstract class BaseController
{
    protected Request $request;
    protected array $routeParams = [];
    protected array $config = [];
    protected PDO $db;
    protected Environment $twig;

    public function parse(Request $request, array $routeParams, array $config): void
    {
        $this->request = $request;
        $this->routeParams = $routeParams;
        $this->config = $config;
        $this->db = $config['db'];
        $this->twig = $config['twig'];
    }

    public function isPublic(): bool
    {
        return false;
    }

    abstract public function run(): Response;

    protected function render(string $template, array $context = [], int $status = 200): Response
    {
        $auth = new AuthService(new Repositories\UserRepository($this->db));

        $defaults = [
            'current_user' => $auth->user(),
            'current_path' => $this->request->path(),
            'flashes' => Session::pullFlashes(),
        ];

        return new Response($this->twig->render($template, array_merge($defaults, $context)), $status);
    }

    protected function redirect(string $location): Response
    {
        return Response::redirect($location);
    }

    protected function json(array $payload, int $status = 200): Response
    {
        return Response::json($payload, $status);
    }

    protected function routeParam(string $name, mixed $default = null): mixed
    {
        return $this->routeParams[$name] ?? $default;
    }
}
