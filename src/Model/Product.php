<?php

namespace Model;

use PDO;

class Product extends Model
{
    private int $id;
    private string $name;
    private float $price;
    private string $viewurl;
    private string $description;

    public static function createFromArray(array $data): self
    {
        $obj              = new self();
        $obj->id          = (int)($data['id'] ?? 0);
        $obj->name        = $data['name'] ?? 'Default Name';
        $obj->price       = (float)($data['price'] ?? 0.0);
        $obj->viewurl     = $data['viewurl'] ?? '';
        $obj->description = $data['description'] ?? '';
        return $obj;
    }

    protected static function query(string $sql, array $params = []): array
    {
        try {
            $stmt = self::getPDO()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public static function getAllProducts(): array
    {
        $rows = self::query("SELECT * FROM products");
        return array_map(fn($row) => self::createFromArray($row), $rows);
    }

    public static function getProductById(int $id): ?self
    {
        $rows = self::query("SELECT * FROM products WHERE id = ?", [$id]);
        return isset($rows[0]) ? self::createFromArray($rows[0]) : null;
    }

    public static function getProductsByIds(array $ids): array
    {
        if (empty($ids)) return [];

        $ids = array_values(array_filter($ids, fn($id) => is_numeric($id) && (int)$id > 0));

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $rows = self::query("SELECT * FROM products WHERE id IN ($placeholders)", $ids);

        return array_map(fn($row) => self::createFromArray($row), $rows);
    }

    // Геттеры
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
    public function getViewUrl(): string { return $this->viewurl; }
    public function getDescription(): string { return $this->description; }
}