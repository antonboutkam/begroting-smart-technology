<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes\Repositories;

use PDO;

final class ProductRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function all(): array
    {
        return $this->filter([]);
    }

    public function filter(array $filters): array
    {
        $conditions = [];
        $params = [];

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $conditions[] = '(p.name LIKE :search OR p.brand LIKE :search OR p.description LIKE :search OR p.goal LIKE :search OR c.name LIKE :search OR parent.name LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $categoryIds = array_values(array_filter(
            array_map('intval', $filters['category_ids'] ?? []),
            static fn (int $id): bool => $id > 0
        ));
        if ($categoryIds !== []) {
            $placeholders = [];
            foreach ($categoryIds as $index => $categoryId) {
                $placeholder = 'category_' . $index;
                $placeholders[] = ':' . $placeholder;
                $params[$placeholder] = $categoryId;
            }

            $conditions[] = 'p.category_id IN (' . implode(', ', $placeholders) . ')';
        }

        $assetModes = array_values(array_intersect(
            ['asset', 'consumable'],
            array_map('strval', $filters['asset_modes'] ?? [])
        ));
        if ($assetModes === ['asset']) {
            $conditions[] = 'p.is_asset = 1';
        } elseif ($assetModes === ['consumable']) {
            $conditions[] = 'p.is_asset = 0';
        }

        $priorityRanges = array_values(array_intersect(
            ['high', 'medium', 'low'],
            array_map('strval', $filters['priority_ranges'] ?? [])
        ));
        if ($priorityRanges !== []) {
            $priorityConditions = [];
            foreach ($priorityRanges as $priorityRange) {
                if ($priorityRange === 'high') {
                    $priorityConditions[] = 'p.priority BETWEEN 8 AND 10';
                    continue;
                }

                if ($priorityRange === 'medium') {
                    $priorityConditions[] = 'p.priority BETWEEN 4 AND 7';
                    continue;
                }

                if ($priorityRange === 'low') {
                    $priorityConditions[] = 'p.priority BETWEEN 0 AND 3';
                }
            }

            if ($priorityConditions !== []) {
                $conditions[] = '(' . implode(' OR ', $priorityConditions) . ')';
            }
        }

        $sql = <<<SQL
            SELECT
                p.*,
                c.name AS category_name,
                parent.name AS parent_category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN categories parent ON parent.id = c.parent_id
        SQL;

        if ($conditions !== []) {
            $sql .= "\nWHERE " . implode("\n  AND ", $conditions);
        }

        $sql .= "\nORDER BY p.priority DESC, p.name ASC";

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);

        $product = $statement->fetch();
        return $product ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO products
            (name, description, goal, category_id, brand, priority, unit, is_asset, quantity_per_student)
            VALUES
            (:name, :description, :goal, :category_id, :brand, :priority, :unit, :is_asset, :quantity_per_student)'
        );

        $statement->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?: null,
            'goal' => $data['goal'] ?: null,
            'category_id' => $data['category_id'] ?: null,
            'brand' => $data['brand'] ?: null,
            'priority' => $data['priority'],
            'unit' => $data['unit'] ?: null,
            'is_asset' => $data['is_asset'],
            'quantity_per_student' => $data['quantity_per_student'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $statement = $this->db->prepare(
            'UPDATE products
            SET name = :name,
                description = :description,
                goal = :goal,
                category_id = :category_id,
                brand = :brand,
                priority = :priority,
                unit = :unit,
                is_asset = :is_asset,
                quantity_per_student = :quantity_per_student
            WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?: null,
            'goal' => $data['goal'] ?: null,
            'category_id' => $data['category_id'] ?: null,
            'brand' => $data['brand'] ?: null,
            'priority' => $data['priority'],
            'unit' => $data['unit'] ?: null,
            'is_asset' => $data['is_asset'],
            'quantity_per_student' => $data['quantity_per_student'],
        ]);
    }

    public function addImage(int $productId, string $filePath, string $originalName): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO product_images (product_id, file_path, original_name) VALUES (:product_id, :file_path, :original_name)'
        );
        $statement->execute([
            'product_id' => $productId,
            'file_path' => $filePath,
            'original_name' => $originalName,
        ]);
    }

    public function images(int $productId): array
    {
        $statement = $this->db->prepare('SELECT * FROM product_images WHERE product_id = :product_id ORDER BY created_at DESC');
        $statement->execute(['product_id' => $productId]);
        return $statement->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }
}
