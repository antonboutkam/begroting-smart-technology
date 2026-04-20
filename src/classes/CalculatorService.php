<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes;

use PDO;
use Roc\SmartTech\Begroting\Classes\Repositories\CategoryRepository;
use Roc\SmartTech\Begroting\Classes\Repositories\ProductRepository;
use Roc\SmartTech\Begroting\Classes\Repositories\SupplierRepository;

final class CalculatorService
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function calculate(
        int $students,
        int $priority,
        array $selectedSupplierIds,
        array $selectedCategoryIds,
        string $priceMode
    ): array {
        $categoryRepository = new CategoryRepository($this->db);
        $productRepository = new ProductRepository($this->db);
        $supplierRepository = new SupplierRepository($this->db);

        $products = $productRepository->all();
        $supplierProducts = $supplierRepository->allSupplierProducts();
        $expandedCategoryIds = $categoryRepository->descendantIds($selectedCategoryIds);

        $linksByProduct = [];
        foreach ($supplierProducts as $supplierProduct) {
            $linksByProduct[(int) $supplierProduct['product_id']][] = $supplierProduct;
        }

        $rows = [];
        $usedSuppliers = [];
        $subtotal = 0.0;
        $vatTotal = 0.0;

        foreach ($products as $product) {
            if ((int) $product['priority'] < $priority) {
                continue;
            }

            if ($expandedCategoryIds !== [] && !in_array((int) $product['category_id'], $expandedCategoryIds, true)) {
                continue;
            }

            $quantity = (float) $product['quantity_per_student'] * $students;
            if ((int) $product['is_asset'] === 1) {
                $quantity = (float) ceil($quantity);
            } else {
                $quantity = round($quantity, 2);
            }

            $bestSupplier = null;
            foreach ($linksByProduct[(int) $product['id']] ?? [] as $supplierOption) {
                if ($selectedSupplierIds !== [] && !in_array((int) $supplierOption['supplier_id'], $selectedSupplierIds, true)) {
                    continue;
                }

                if ($bestSupplier === null) {
                    $bestSupplier = $supplierOption;
                    continue;
                }

                $currentPrice = (float) ($bestSupplier['price_' . $priceMode] ?? 0);
                $candidatePrice = (float) ($supplierOption['price_' . $priceMode] ?? 0);
                if ($candidatePrice > 0 && ($currentPrice <= 0 || $candidatePrice < $currentPrice)) {
                    $bestSupplier = $supplierOption;
                }
            }

            $unitExcl = $bestSupplier ? (float) ($bestSupplier['price_excl'] ?? 0) : 0.0;
            $unitIncl = $bestSupplier ? (float) ($bestSupplier['price_incl'] ?? 0) : 0.0;
            $rowExcl = round($quantity * $unitExcl, 2);
            $rowIncl = round($quantity * $unitIncl, 2);

            $subtotal += $priceMode === 'incl' ? $rowIncl : $rowExcl;
            $vatTotal += max(0.0, $rowIncl - $rowExcl);

            if ($bestSupplier) {
                $usedSuppliers[(int) $bestSupplier['supplier_id']] = $bestSupplier;
            }

            $rows[] = [
                'product_name' => $product['name'],
                'category_name' => $product['category_name'],
                'quantity' => $quantity,
                'unit' => $product['unit'],
                'supplier_name' => $bestSupplier['supplier_name'] ?? 'Geen leverancier geselecteerd',
                'unit_price_excl' => $unitExcl,
                'unit_price_incl' => $unitIncl,
                'row_price_excl' => $rowExcl,
                'row_price_incl' => $rowIncl,
                'package_information' => $bestSupplier['package_information'] ?? '',
            ];
        }

        $shippingExcl = 0.0;
        $shippingIncl = 0.0;
        foreach ($usedSuppliers as $supplier) {
            $shippingExcl += (float) ($supplier['shipping_excl'] ?? 0);
            $shippingIncl += (float) ($supplier['shipping_incl'] ?? 0);
        }

        return [
            'rows' => $rows,
            'students' => $students,
            'priority' => $priority,
            'price_mode' => $priceMode,
            'subtotal' => round($subtotal, 2),
            'vat_total' => round($vatTotal + ($shippingIncl - $shippingExcl), 2),
            'shipping_excl' => round($shippingExcl, 2),
            'shipping_incl' => round($shippingIncl, 2),
            'grand_total_excl' => round($subtotal + $shippingExcl, 2),
            'grand_total_incl' => round(($priceMode === 'incl' ? $subtotal : ($subtotal + $vatTotal)) + $shippingIncl, 2),
        ];
    }
}
