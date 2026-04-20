<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TemplateRenderer
{
    public static function create(string $modulesPath): Environment
    {
        $loader = new FilesystemLoader($modulesPath);

        return new Environment($loader, [
            'cache' => false,
            'debug' => true,
        ]);
    }
}
