<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Auth;

use Roc\SmartTech\Begroting\Classes\AuthService;
use Roc\SmartTech\Begroting\Classes\BaseController;
use Roc\SmartTech\Begroting\Classes\Repositories\UserRepository;
use Roc\SmartTech\Begroting\Classes\Response;
use Roc\SmartTech\Begroting\Classes\Session;

final class LoginController extends BaseController
{
    public function isPublic(): bool
    {
        return true;
    }

    public function run(): Response
    {
        $auth = new AuthService(new UserRepository($this->db));

        if ($this->request->isPost()) {
            $login = trim((string) $this->request->input('login'));
            $password = (string) $this->request->input('password');

            if ($auth->attempt($login, $password)) {
                Session::flash('success', 'Succesvol ingelogd.');
                return $this->redirect('/dashboard');
            }

            Session::flash('danger', 'Ongeldige inloggegevens.');
            return $this->redirect('/login');
        }

        if ($auth->check()) {
            return $this->redirect('/dashboard');
        }

        return $this->render('Auth/views/login.twig', [
            'page_title' => 'Inloggen',
        ]);
    }
}
