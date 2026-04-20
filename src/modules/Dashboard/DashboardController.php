<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Dashboard;

use Roc\SmartTech\Begroting\Classes\BaseController;
use Roc\SmartTech\Begroting\Classes\Repositories\CategoryRepository;
use Roc\SmartTech\Begroting\Classes\Repositories\ProductRepository;
use Roc\SmartTech\Begroting\Classes\Repositories\SupplierRepository;
use Roc\SmartTech\Begroting\Classes\Response;

final class DashboardController extends BaseController
{
    public function run(): Response
    {
        $products = new ProductRepository($this->db);
        $suppliers = new SupplierRepository($this->db);
        $categories = new CategoryRepository($this->db);

        return $this->render('Dashboard/views/index.twig', [
            'page_title' => 'Dashboard',
            'stats' => [
                'products' => $products->count(),
                'suppliers' => $suppliers->count(),
                'categories' => count($categories->all()),
            ],
        ]);
    }
}
