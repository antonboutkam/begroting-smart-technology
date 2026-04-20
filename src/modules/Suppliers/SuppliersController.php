<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Suppliers;

use Roc\SmartTech\Begroting\Classes\BaseController;
use Roc\SmartTech\Begroting\Classes\Repositories\SupplierRepository;
use Roc\SmartTech\Begroting\Classes\Response;
use Roc\SmartTech\Begroting\Classes\Session;

final class SuppliersController extends BaseController
{
    public function run(): Response
    {
        $suppliers = new SupplierRepository($this->db);

        if ($this->request->isPost()) {
            $supplierId = $suppliers->create($this->payload());
            Session::flash('success', 'Leverancier aangemaakt.');
            return $this->redirect('/suppliers/' . $supplierId);
        }

        return $this->render('Suppliers/views/index.twig', [
            'page_title' => 'Leveranciers',
            'suppliers' => $suppliers->all(),
        ]);
    }

    private function payload(): array
    {
        return [
            'name' => trim((string) $this->request->input('name')),
            'description' => trim((string) $this->request->input('description')),
            'vat_rate' => (float) $this->request->input('vat_rate', 21),
            'shipping_excl' => $this->request->input('shipping_excl') !== '' ? (float) $this->request->input('shipping_excl') : null,
            'shipping_incl' => $this->request->input('shipping_incl') !== '' ? (float) $this->request->input('shipping_incl') : null,
        ];
    }
}
