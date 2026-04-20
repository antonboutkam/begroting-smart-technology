<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Products;

use Roc\SmartTech\Begroting\Classes\BaseController;
use Roc\SmartTech\Begroting\Classes\Repositories\CategoryRepository;
use Roc\SmartTech\Begroting\Classes\Repositories\ProductRepository;
use Roc\SmartTech\Begroting\Classes\Response;

final class ProductsController extends BaseController
{
    public function run(): Response
    {
        $products = new ProductRepository($this->db);
        $categories = new CategoryRepository($this->db);
        $selectedTopCategoryIds = $this->selectedIntValues('categories');
        $selectedAssetModes = $this->selectedStringValues('asset_modes', ['asset', 'consumable']);
        $selectedPriorityRanges = $this->selectedStringValues('priority_ranges', ['high', 'medium', 'low']);
        $expandedCategoryIds = $categories->descendantIds($selectedTopCategoryIds);
        $search = trim((string) $this->request->query('q', ''));

        return $this->render('Products/views/index.twig', [
            'page_title' => 'Productcatalogus',
            'products' => $products->filter([
                'search' => $search,
                'category_ids' => $expandedCategoryIds,
                'asset_modes' => $selectedAssetModes,
                'priority_ranges' => $selectedPriorityRanges,
            ]),
            'top_categories' => $categories->topLevel(),
            'selected_category_ids' => $selectedTopCategoryIds,
            'selected_asset_modes' => $selectedAssetModes,
            'selected_priority_ranges' => $selectedPriorityRanges,
            'search_query' => $search,
        ]);
    }

    private function selectedIntValues(string $key): array
    {
        $values = (array) $this->request->query($key, []);
        $values = array_values(array_filter(array_map('intval', $values), static fn (int $value): bool => $value > 0));
        sort($values);

        return $values;
    }

    private function selectedStringValues(string $key, array $allowed): array
    {
        $values = array_map('strval', (array) $this->request->query($key, []));
        $values = array_values(array_intersect($allowed, $values));
        $values = array_values(array_unique($values));

        return $values;
    }
}
