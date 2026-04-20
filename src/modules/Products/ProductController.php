<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Products;

use Roc\SmartTech\Begroting\Classes\BaseController;
use Roc\SmartTech\Begroting\Classes\Repositories\CategoryRepository;
use Roc\SmartTech\Begroting\Classes\Repositories\ProductRepository;
use Roc\SmartTech\Begroting\Classes\Response;
use Roc\SmartTech\Begroting\Classes\Session;
use Roc\SmartTech\Begroting\Classes\UploadHelper;
use RuntimeException;

final class ProductController extends BaseController
{
    public function run(): Response
    {
        $products = new ProductRepository($this->db);
        $categories = new CategoryRepository($this->db);
        $productId = (int) $this->routeParam('id', 0);
        $isNew = $productId <= 0;
        $popupMode = $this->request->query('popup') === '1';
        $justSaved = $this->request->query('saved') === '1';
        $product = $isNew ? $this->emptyProduct() : $products->find($productId);

        if (!$isNew && $product === null) {
            throw new RuntimeException('Product niet gevonden.');
        }

        if ($this->request->isPost()) {
            if ($isNew) {
                $productId = $products->create($this->payload());
                Session::flash('success', 'Product aangemaakt.');
            } else {
                $products->update($productId, $this->payload());
                Session::flash('success', 'Product bijgewerkt.');
            }

            foreach ($this->normalizeFiles($this->request->file('images')) as $file) {
                $stored = UploadHelper::save(
                    $file,
                    $this->config['root_path'] . '/public_html/uploads/products',
                    '/uploads/products'
                );
                $products->addImage($productId, $stored['file_path'], $stored['original_name']);
            }

            return $this->redirect($this->editorUrl($productId, $popupMode, true));
        }

        return $this->render('Products/views/detail.twig', [
            'page_title' => $isNew ? 'Nieuw product' : 'Product beheren',
            'is_new' => $isNew,
            'product' => $product,
            'categories' => $categories->all(),
            'images' => $isNew ? [] : $products->images($productId),
            'popup_mode' => $popupMode,
            'just_saved' => $justSaved,
            'overview_url' => '/products',
        ]);
    }

    private function payload(): array
    {
        return [
            'name' => trim((string) $this->request->input('name')),
            'description' => trim((string) $this->request->input('description')),
            'goal' => trim((string) $this->request->input('goal')),
            'category_id' => (int) $this->request->input('category_id'),
            'brand' => trim((string) $this->request->input('brand')),
            'priority' => max(0, min(10, (int) $this->request->input('priority', 0))),
            'unit' => trim((string) $this->request->input('unit')),
            'is_asset' => $this->request->input('is_asset') ? 1 : 0,
            'quantity_per_student' => max(0, (float) $this->request->input('quantity_per_student', 1)),
        ];
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

    private function editorUrl(int $productId, bool $popupMode, bool $saved = false): string
    {
        $query = [];
        if ($popupMode) {
            $query['popup'] = '1';
        }

        if ($saved) {
            $query['saved'] = '1';
        }

        $url = '/products/' . $productId;
        if ($query === []) {
            return $url;
        }

        return $url . '?' . http_build_query($query);
    }

    private function emptyProduct(): array
    {
        return [
            'id' => null,
            'name' => '',
            'description' => '',
            'goal' => '',
            'category_id' => null,
            'brand' => '',
            'priority' => 0,
            'unit' => '',
            'is_asset' => 0,
            'quantity_per_student' => 1,
        ];
    }
}
