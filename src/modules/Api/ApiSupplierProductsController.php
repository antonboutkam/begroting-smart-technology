<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Api;

use Roc\SmartTech\Begroting\Classes\Repositories\SupplierRepository;
use Roc\SmartTech\Begroting\Classes\Response;

final class ApiSupplierProductsController extends ApiController
{
    public function run(): Response
    {
        if ($response = $this->authorize()) {
            return $response;
        }

        $repository = new SupplierRepository($this->db);

        if ($this->request->method() === 'POST') {
            $payload = array_merge($this->request->input(), $this->request->json());
            $repository->addOrUpdateProductLink([
                'supplier_id' => (int) ($payload['supplier_id'] ?? 0),
                'product_id' => (int) ($payload['product_id'] ?? 0),
                'price_excl' => ($payload['price_excl'] ?? '') !== '' ? (float) $payload['price_excl'] : null,
                'price_incl' => ($payload['price_incl'] ?? '') !== '' ? (float) $payload['price_incl'] : null,
                'package_information' => trim((string) ($payload['package_information'] ?? '')),
            ]);

            return $this->json(['status' => 'saved'], 201);
        }

        return $this->json(['data' => $repository->allSupplierProducts()]);
    }
}
