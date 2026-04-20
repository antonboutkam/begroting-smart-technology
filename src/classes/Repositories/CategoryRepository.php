<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes\Repositories;

use PDO;

final class CategoryRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM categories ORDER BY sort_order ASC, name ASC')->fetchAll();
    }

    public function topLevel(): array
    {
        return $this->db
            ->query('SELECT * FROM categories WHERE parent_id IS NULL ORDER BY sort_order ASC, name ASC')
            ->fetchAll();
    }

    public function create(string $name, ?int $parentId, int $sortOrder): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO categories (name, parent_id, sort_order) VALUES (:name, :parent_id, :sort_order)'
        );
        $statement->execute([
            'name' => $name,
            'parent_id' => $parentId ?: null,
            'sort_order' => $sortOrder,
        ]);
    }

    public function tree(): array
    {
        $items = $this->all();
        $indexed = [];
        foreach ($items as $item) {
            $item['children'] = [];
            $indexed[(int) $item['id']] = $item;
        }

        $tree = [];
        foreach ($indexed as $id => $item) {
            $parentId = $item['parent_id'] !== null ? (int) $item['parent_id'] : null;
            if ($parentId !== null && isset($indexed[$parentId])) {
                $indexed[$parentId]['children'][] = &$indexed[$id];
                continue;
            }

            $tree[] = &$indexed[$id];
        }

        return $tree;
    }

    public function descendantIds(array $selectedIds): array
    {
        if ($selectedIds === []) {
            return [];
        }

        $items = $this->all();
        $childrenByParent = [];
        foreach ($items as $item) {
            $parentId = $item['parent_id'] !== null ? (int) $item['parent_id'] : 0;
            $childrenByParent[$parentId][] = (int) $item['id'];
        }

        $all = [];
        $stack = array_map('intval', $selectedIds);
        while ($stack !== []) {
            $id = array_pop($stack);
            if (in_array($id, $all, true)) {
                continue;
            }

            $all[] = $id;
            foreach ($childrenByParent[$id] ?? [] as $childId) {
                $stack[] = $childId;
            }
        }

        sort($all);
        return $all;
    }
}
