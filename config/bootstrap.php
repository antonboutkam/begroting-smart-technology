<?php

use Roc\SmartTech\Begroting\Classes\Database;
use Roc\SmartTech\Begroting\Classes\Env;
use Roc\SmartTech\Begroting\Classes\TemplateRenderer;

$rootPath = dirname(__DIR__);

Env::load($rootPath . '/.env');

return [
    'root_path' => $rootPath,
    'db' => Database::connect(),
    'twig' => TemplateRenderer::create($rootPath . '/src/modules'),
    'api_keys' => require $rootPath . '/config/api_keys.php',
];
