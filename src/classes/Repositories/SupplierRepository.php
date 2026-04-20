<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes\Repositories;

use PDO;
use Roc\SmartTech\Begroting\Classes\PriceCalculator;

final class SupplierRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM suppliers ORDER BY name ASC')->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM suppliers')->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM suppliers WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $supplier = $statement->fetch();
        return $supplier ?: null;
    }

    public function create(array $data): int
    {
        $prices = PriceCalculator::fillVatValues(
            $data['shipping_excl'],
            $data['shipping_incl'],
            $data['vat_rate']
        );

        $statement = $this->db->prepare(
            'INSERT INTO suppliers (name, description, vat_rate, shipping_excl, shipping_incl)
             VALUES (:name, :description, :vat_rate, :shipping_excl, :shipping_incl)'
        );
        $statement->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?: null,
            'vat_rate' => $data['vat_rate'],
            'shipping_excl' => $prices['excl'],
            'shipping_incl' => $prices['incl'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $prices = PriceCalculator::fillVatValues(
            $data['shipping_excl'],
            $data['shipping_incl'],
            $data['vat_rate']
        );

        $statement = $this->db->prepare(
            'UPDATE suppliers
             SET name = :name,
                 description = :description,
                 vat_rate = :vat_rate,
                 shipping_excl = :shipping_excl,
                 shipping_incl = :shipping_incl
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?: null,
            'vat_rate' => $data['vat_rate'],
            'shipping_excl' => $prices['excl'],
            'shipping_incl' => $prices['incl'],
        ]);
    }

    public function addFile(int $supplierId, string $fileType, string $filePath, string $originalName): void
    {
        $statement = $this->db->prepare(
            'INSERT INTO supplier_files (supplier_id, file_type, file_path, original_name)
             VALUES (:supplier_id, :file_type, :file_path, :original_name)'
        );
        $statement->execute([
            'supplier_id' => $supplierId,
            'file_type' => $fileType,
            'file_path' => $filePath,
            'original_name' => $originalName,
        ]);
    }

    public function files(int $supplierId): array
    {
        $statement = $this->db->prepare('SELECT * FROM supplier_files WHERE supplier_id = :supplier_id ORDER BY created_at DESC');
        $statement->execute(['supplier_id' => $supplierId]);
        return $statement->fetchAll();
    }

    public function addOrUpdateProductLink(array $data): void
    {
        $supplier = $this->find((int) $data['supplier_id']);
        $vatRate = $supplier ? (float) $supplier['vat_rate'] : 21.0;
        $prices = PriceCalculator::fillVatValues($data['price_excl'], $data['price_incl'], $vatRate);

        $existingStatement = $this->db->prepare(
            'SELECT id FROM supplier_products WHERE supplier_id = :supplier_id AND product_id = :product_id LIMIT 1'
        );
        $existingStatement->execute([
            'supplier_id' => $data['supplier_id'],
            'product_id' => $data['product_id'],
        ]);

        $existingId = $existingStatement->fetchColumn();

        if ($existingId) {
            $statement = $this->db->prepare(
                'UPDATE supplier_products
                 SET price_excl = :price_excl,
                     price_incl = :price_incl,
                     package_information = :package_information
                 WHERE id = :id'
            );
            $statement->execute([
                'id' => $existingId,
                'price_excl' => $prices['excl'],
                'price_incl' => $prices['incl'],
                'package_information' => $data['package_information'] ?: null,
            ]);

            return;
        }

        $statement = $this->db->prepare(
            'INSERT INTO supplier_products
             (supplier_id, product_id, price_excl, price_incl, package_information)
             VALUES
             (:supplier_id, :product_id, :price_excl, :price_incl, :package_information)'
        );
        $statement->execute([
            'supplier_id' => $data['supplier_id'],
            'product_id' => $data['product_id'],
            'price_excl' => $prices['excl'],
            'price_incl' => $prices['incl'],
            'package_information' => $data['package_information'] ?: null,
        ]);
    }

    public function productLinksForSupplier(int $supplierId): array
    {
        $statement = $this->db->prepare(
            'SELECT sp.*, p.name AS product_name
             FROM supplier_products sp
             INNER JOIN products p ON p.id = sp.product_id
             WHERE sp.supplier_id = :supplier_id
             ORDER BY p.name ASC'
        );
        $statement->execute(['supplier_id' => $supplierId]);
        return $statement->fetchAll();
    }

    public function allSupplierProducts(): array
    {
        $sql = <<<SQL
            SELECT
                sp.*,
                s.name AS supplier_name,
                s.vat_rate,
                s.shipping_excl,
                s.shipping_incl,
                p.name AS product_name
            FROM supplier_products sp
            INNER JOIN suppliers s ON s.id = sp.supplier_id
            INNER JOIN products p ON p.id = sp.product_id
            ORDER BY s.name ASC, p.name ASC
        SQL;

        return $this->db->query($sql)->fetchAll();
    }
}
