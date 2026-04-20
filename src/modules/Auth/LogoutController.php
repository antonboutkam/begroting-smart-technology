<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Auth;

use Roc\SmartTech\Begroting\Classes\AuthService;
use Roc\SmartTech\Begroting\Classes\BaseController;
use Roc\SmartTech\Begroting\Classes\Repositories\UserRepository;
use Roc\SmartTech\Begroting\Classes\Response;
use Roc\SmartTech\Begroting\Classes\Session;

final class LogoutController extends BaseController
{
    public function run(): Response
    {
        $auth = new AuthService(new UserRepository($this->db));
        $auth->logout();
        Session::flash('success', 'U bent uitgelogd.');

        return $this->redirect('/login');
    }
}
