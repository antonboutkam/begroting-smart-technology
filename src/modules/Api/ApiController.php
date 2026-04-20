<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Api;

use Roc\SmartTech\Begroting\Classes\BaseController;
use Roc\SmartTech\Begroting\Classes\Response;

abstract class ApiController extends BaseController
{
    public function isPublic(): bool
    {
        return true;
    }

    protected function authorize(): ?Response
    {
        $providedKey = (string) $this->request->query('api_key', '');
        $keys = array_values($this->config['api_keys']);

        if ($providedKey === '' || !in_array($providedKey, $keys, true)) {
            return $this->json([
                'error' => 'Ongeldige of ontbrekende api_key.',
            ], 401);
        }

        return null;
    }
}
