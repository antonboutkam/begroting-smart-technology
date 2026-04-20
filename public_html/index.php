<?php

declare(strict_types=1);

use Roc\SmartTech\Begroting\Classes\AppKernel;
use Roc\SmartTech\Begroting\Classes\Session;

require dirname(__DIR__) . '/vendor/autoload.php';

$config = require dirname(__DIR__) . '/config/bootstrap.php';

Session::start();

$kernel = new AppKernel($config);
$kernel->handle()->send();
