<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Api;

use Roc\SmartTech\Begroting\Classes\Response;

final class ApiDocsController extends ApiController
{
    public function run(): Response
    {
        return $this->render('Api/views/docs.twig', [
            'page_title' => 'API documentatie',
            'api_keys' => array_keys($this->config['api_keys']),
            'endpoints' => [
                [
                    'path' => '/api/products',
                    'methods' => 'GET, POST',
                    'description' => 'Haalt producten op of voegt een product toe.',
                    'sample' => '{"name":"Soldeerbout","priority":8,"quantity_per_student":0.125,"is_asset":1}',
                ],
                [
                    'path' => '/api/suppliers',
                    'methods' => 'GET, POST',
                    'description' => 'Haalt leveranciers op of voegt een leverancier toe.',
                    'sample' => '{"name":"Conrad","vat_rate":21,"shipping_excl":9.95}',
                ],
                [
                    'path' => '/api/categories',
                    'methods' => 'GET, POST',
                    'description' => 'Haalt categorieen op of maakt een categorie aan.',
                    'sample' => '{"name":"Elektronica","parent_id":null,"sort_order":10}',
                ],
                [
                    'path' => '/api/supplier-products',
                    'methods' => 'GET, POST',
                    'description' => 'Haalt product-leverancierkoppelingen op of slaat prijzen op.',
                    'sample' => '{"supplier_id":1,"product_id":2,"price_excl":14.95,"package_information":"Set van 10"}',
                ],
            ],
        ]);
    }
}
