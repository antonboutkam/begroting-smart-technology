<?php

use Roc\SmartTech\Begroting\Modules\Api\ApiCategoriesController;
use Roc\SmartTech\Begroting\Modules\Api\ApiDocsController;
use Roc\SmartTech\Begroting\Modules\Api\ApiProductsController;
use Roc\SmartTech\Begroting\Modules\Api\ApiSupplierProductsController;
use Roc\SmartTech\Begroting\Modules\Api\ApiSuppliersController;
use Roc\SmartTech\Begroting\Modules\Auth\LoginController;
use Roc\SmartTech\Begroting\Modules\Auth\LogoutController;
use Roc\SmartTech\Begroting\Modules\Calculator\CalculatorController;
use Roc\SmartTech\Begroting\Modules\Categories\CategoriesController;
use Roc\SmartTech\Begroting\Modules\Dashboard\DashboardController;
use Roc\SmartTech\Begroting\Modules\Products\ProductController;
use Roc\SmartTech\Begroting\Modules\Products\ProductsController;
use Roc\SmartTech\Begroting\Modules\Suppliers\SupplierController;
use Roc\SmartTech\Begroting\Modules\Suppliers\SuppliersController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$routes->add('home', new Route('/', ['_controller' => DashboardController::class]));
$routes->add('login', new Route('/login', ['_controller' => LoginController::class]));
$routes->add('logout', new Route('/logout', ['_controller' => LogoutController::class]));
$routes->add('dashboard', new Route('/dashboard', ['_controller' => DashboardController::class]));

$routes->add('products', new Route('/products', ['_controller' => ProductsController::class]));
$routes->add('product_new', new Route('/products/new', ['_controller' => ProductController::class]));
$routes->add('product', new Route('/products/{id}', ['_controller' => ProductController::class], ['id' => '\d+']));

$routes->add('suppliers', new Route('/suppliers', ['_controller' => SuppliersController::class]));
$routes->add('supplier', new Route('/suppliers/{id}', ['_controller' => SupplierController::class], ['id' => '\d+']));

$routes->add('categories', new Route('/categories', ['_controller' => CategoriesController::class]));
$routes->add('calculator', new Route('/calculator', ['_controller' => CalculatorController::class]));

$routes->add('api_docs', new Route('/api/docs', ['_controller' => ApiDocsController::class], [], [], '', [], ['GET']));
$routes->add('api_products', new Route('/api/products', ['_controller' => ApiProductsController::class], [], [], '', [], ['GET', 'POST']));
$routes->add('api_suppliers', new Route('/api/suppliers', ['_controller' => ApiSuppliersController::class], [], [], '', [], ['GET', 'POST']));
$routes->add('api_categories', new Route('/api/categories', ['_controller' => ApiCategoriesController::class], [], [], '', [], ['GET', 'POST']));
$routes->add('api_supplier_products', new Route('/api/supplier-products', ['_controller' => ApiSupplierProductsController::class], [], [], '', [], ['GET', 'POST']));

return $routes;
