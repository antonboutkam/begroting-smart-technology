<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Calculator;

use Roc\SmartTech\Begroting\Classes\BaseController;
use Roc\SmartTech\Begroting\Classes\CalculatorService;
use Roc\SmartTech\Begroting\Classes\Repositories\CategoryRepository;
use Roc\SmartTech\Begroting\Classes\Repositories\SupplierRepository;
use Roc\SmartTech\Begroting\Classes\Response;

final class CalculatorController extends BaseController
{
    public function run(): Response
    {
        $students = max(1, (int) $this->request->query('students', 50));
        $priority = max(0, min(10, (int) $this->request->query('priority', 0)));
        $priceMode = $this->request->query('price_mode', 'incl') === 'excl' ? 'excl' : 'incl';
        $supplierIds = array_map('intval', (array) $this->request->query('suppliers', []));
        $categoryIds = array_map('intval', (array) $this->request->query('categories', []));

        $calculator = new CalculatorService($this->db);
        $supplierRepository = new SupplierRepository($this->db);
        $categoryRepository = new CategoryRepository($this->db);

        return $this->render('Calculator/views/index.twig', [
            'page_title' => 'Calculator',
            'suppliers' => $supplierRepository->all(),
            'category_tree' => $categoryRepository->tree(),
            'selected_supplier_ids' => $supplierIds,
            'selected_category_ids' => $categoryIds,
            'filters' => [
                'students' => $students,
                'priority' => $priority,
                'price_mode' => $priceMode,
            ],
            'result' => $calculator->calculate($students, $priority, $supplierIds, $categoryIds, $priceMode),
        ]);
    }
}
