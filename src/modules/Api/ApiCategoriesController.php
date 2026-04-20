<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Api;

use Roc\SmartTech\Begroting\Classes\Repositories\CategoryRepository;
use Roc\SmartTech\Begroting\Classes\Response;

final class ApiCategoriesController extends ApiController
{
    public function run(): Response
    {
        if ($response = $this->authorize()) {
            return $response;
        }

        $repository = new CategoryRepository($this->db);

        if ($this->request->method() === 'POST') {
            $payload = array_merge($this->request->input(), $this->request->json());
            $repository->create(
                trim((string) ($payload['name'] ?? '')),
                ($payload['parent_id'] ?? null) !== null ? (int) $payload['parent_id'] : null,
                (int) ($payload['sort_order'] ?? 0)
            );

            return $this->json(['status' => 'created'], 201);
        }

        return $this->json([
            'data' => $repository->all(),
            'tree' => $repository->tree(),
        ]);
    }
}
