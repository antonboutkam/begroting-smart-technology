<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Modules\Categories;

use Roc\SmartTech\Begroting\Classes\BaseController;
use Roc\SmartTech\Begroting\Classes\Repositories\CategoryRepository;
use Roc\SmartTech\Begroting\Classes\Response;
use Roc\SmartTech\Begroting\Classes\Session;

final class CategoriesController extends BaseController
{
    public function run(): Response
    {
        $categories = new CategoryRepository($this->db);

        if ($this->request->isPost()) {
            $categories->create(
                trim((string) $this->request->input('name')),
                $this->request->input('parent_id') !== '' ? (int) $this->request->input('parent_id') : null,
                (int) $this->request->input('sort_order', 0)
            );
            Session::flash('success', 'Categorie opgeslagen.');
            return $this->redirect('/categories');
        }

        return $this->render('Categories/views/index.twig', [
            'page_title' => 'Categoriebeheer',
            'categories' => $categories->all(),
            'category_tree' => $categories->tree(),
        ]);
    }
}
