<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Api;

use Roc\SmartTech\Begroting\Classes\Repositories\ProductRepository;
use Roc\SmartTech\Begroting\Classes\Response;

final class ApiProductsController extends ApiController
{
    public function run(): Response
    {
        if ($response = $this->authorize()) {
            return $response;
        }

        $repository = new ProductRepository($this->db);

        if ($this->request->method() === 'POST') {
            $payload = array_merge($this->request->input(), $this->request->json());
            $id = $repository->create([
                'name' => trim((string) ($payload['name'] ?? '')),
                'description' => trim((string) ($payload['description'] ?? '')),
                'goal' => trim((string) ($payload['goal'] ?? '')),
                'category_id' => (int) ($payload['category_id'] ?? 0),
                'brand' => trim((string) ($payload['brand'] ?? '')),
                'priority' => max(0, min(10, (int) ($payload['priority'] ?? 0))),
                'unit' => trim((string) ($payload['unit'] ?? '')),
                'is_asset' => !empty($payload['is_asset']) ? 1 : 0,
                'quantity_per_student' => max(0, (float) ($payload['quantity_per_student'] ?? 1)),
            ]);

            return $this->json(['status' => 'created', 'id' => $id], 201);
        }

        return $this->json(['data' => $repository->all()]);
    }
}
