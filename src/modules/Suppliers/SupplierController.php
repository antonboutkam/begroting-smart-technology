<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Suppliers;

use Roc\SmartTech\Begroting\Classes\BaseController;
use Roc\SmartTech\Begroting\Classes\Repositories\ProductRepository;
use Roc\SmartTech\Begroting\Classes\Repositories\SupplierRepository;
use Roc\SmartTech\Begroting\Classes\Response;
use Roc\SmartTech\Begroting\Classes\Session;
use Roc\SmartTech\Begroting\Classes\UploadHelper;
use RuntimeException;

final class SupplierController extends BaseController
{
    public function run(): Response
    {
        $suppliers = new SupplierRepository($this->db);
        $products = new ProductRepository($this->db);
        $supplierId = (int) $this->routeParam('id');
        $supplier = $suppliers->find($supplierId);

        if ($supplier === null) {
            throw new RuntimeException('Leverancier niet gevonden.');
        }

        if ($this->request->isPost()) {
            $action = (string) $this->request->input('form_action', 'supplier');

            if ($action === 'supplier') {
                $suppliers->update($supplierId, $this->supplierPayload());
                $this->handleUploads($suppliers, $supplierId);
                Session::flash('success', 'Leverancier bijgewerkt.');
            }

            if ($action === 'product_link') {
                $suppliers->addOrUpdateProductLink([
                    'supplier_id' => $supplierId,
                    'product_id' => (int) $this->request->input('product_id'),
                    'price_excl' => $this->request->input('price_excl') !== '' ? (float) $this->request->input('price_excl') : null,
                    'price_incl' => $this->request->input('price_incl') !== '' ? (float) $this->request->input('price_incl') : null,
                    'package_information' => trim((string) $this->request->input('package_information')),
                ]);
                Session::flash('success', 'Productkoppeling opgeslagen.');
            }

            return $this->redirect('/suppliers/' . $supplierId);
        }

        return $this->render('Suppliers/views/detail.twig', [
            'page_title' => 'Leverancier beheren',
            'supplier' => $supplier,
            'products' => $products->all(),
            'supplier_files' => $suppliers->files($supplierId),
            'product_links' => $suppliers->productLinksForSupplier($supplierId),
        ]);
    }

    private function supplierPayload(): array
    {
        return [
            'name' => trim((string) $this->request->input('name')),
            'description' => trim((string) $this->request->input('description')),
            'vat_rate' => (float) $this->request->input('vat_rate', 21),
            'shipping_excl' => $this->request->input('shipping_excl') !== '' ? (float) $this->request->input('shipping_excl') : null,
            'shipping_incl' => $this->request->input('shipping_incl') !== '' ? (float) $this->request->input('shipping_incl') : null,
        ];
    }

    private function handleUploads(SupplierRepository $suppliers, int $supplierId): void
    {
        $document = $this->request->file('supplier_document');
        if (is_array($document) && ($document['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $stored = UploadHelper::save(
                $document,
                $this->config['root_path'] . '/public_html/uploads/suppliers',
                '/uploads/suppliers'
            );
            $suppliers->addFile($supplierId, 'supplier_document', $stored['file_path'], $stored['original_name']);
        }

        foreach ($this->normalizeFiles($this->request->file('catalogues')) as $file) {
            $stored = UploadHelper::save(
                $file,
                $this->config['root_path'] . '/public_html/uploads/suppliers',
                '/uploads/suppliers'
            );
            $suppliers->addFile($supplierId, 'catalogue', $stored['file_path'], $stored['original_name']);
        }
    }

    private function normalizeFiles(?array $files): array
    {
        if ($files === null || !isset($files['name']) || !is_array($files['name'])) {
            return [];
        }

        $result = [];
        foreach ($files['name'] as $index => $name) {
            if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $result[] = [
                'name' => $name,
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];
        }

        return $result;
    }
}
