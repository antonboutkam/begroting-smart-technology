<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Api;

use Roc\SmartTech\Begroting\Classes\Repositories\SupplierRepository;
use Roc\SmartTech\Begroting\Classes\Response;

final class ApiSuppliersController extends ApiController
{
    public function run(): Response
    {
        if ($response = $this->authorize()) {
            return $response;
        }

        $repository = new SupplierRepository($this->db);

        if ($this->request->method() === 'POST') {
            $payload = array_merge($this->request->input(), $this->request->json());
            $id = $repository->create([
                'name' => trim((string) ($payload['name'] ?? '')),
                'description' => trim((string) ($payload['description'] ?? '')),
                'vat_rate' => (float) ($payload['vat_rate'] ?? 21),
                'shipping_excl' => ($payload['shipping_excl'] ?? '') !== '' ? (float) $payload['shipping_excl'] : null,
                'shipping_incl' => ($payload['shipping_incl'] ?? '') !== '' ? (float) $payload['shipping_incl'] : null,
            ]);

            return $this->json(['status' => 'created', 'id' => $id], 201);
        }

        return $this->json(['data' => $repository->all()]);
    }
}
