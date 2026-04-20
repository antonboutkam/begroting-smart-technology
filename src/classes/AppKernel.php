<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Throwable;

final class AppKernel
{
    public function __construct(private readonly array $config)
    {
    }

    public function handle(): Response
    {
        $request = Request::fromGlobals();
        $routes = require $this->config['root_path'] . '/config/routes.php';

        $context = new RequestContext();
        $context->setMethod($request->method());
        $context->setPathInfo($request->path());

        $matcher = new UrlMatcher($routes, $context);

        try {
            $parameters = $matcher->match($request->path());
            $controllerClass = $parameters['_controller'];
            unset($parameters['_controller'], $parameters['_route']);

            $controller = new $controllerClass();
            if (!$controller instanceof BaseController) {
                throw new \RuntimeException('Controller is ongeldig.');
            }

            $controller->parse($request, $parameters, $this->config);

            if (!$controller->isPublic()) {
                $auth = new AuthService(new Repositories\UserRepository($this->config['db']));
                if (!$auth->check()) {
                    Session::flash('warning', 'Log eerst in om het systeem te gebruiken.');
                    return Response::redirect('/login');
                }
            }

            return $controller->run();
        } catch (ResourceNotFoundException) {
            return new Response($this->renderErrorPage('Pagina niet gevonden.', 404), 404);
        } catch (MethodNotAllowedException) {
            return new Response($this->renderErrorPage('Methode niet toegestaan.', 405), 405);
        } catch (Throwable $exception) {
            $status = $exception instanceof \RuntimeException ? 500 : 500;
            return new Response($this->renderErrorPage($exception->getMessage(), $status), $status);
        }
    }

    private function renderErrorPage(string $message, int $status): string
    {
        return $this->config['twig']->render('Shared/views/error.twig', [
            'status' => $status,
            'message' => $message,
            'current_user' => Session::get('user'),
            'current_path' => Request::fromGlobals()->path(),
            'flashes' => Session::pullFlashes(),
        ]);
    }
}
