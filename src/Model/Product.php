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
        $obj = new self();

        $obj->id = isset($data['id']) ? (int)$data['id'] : 0;
        $obj->name = isset($data['name']) && is_string($data['name']) ? $data['name'] : 'Default Name';
        $obj->price = isset($data['price']) && is_numeric($data['price']) ? (float)$data['price'] : 0.0;
        $obj->viewurl = isset($data['viewurl']) && filter_var($data['viewurl'], FILTER_VALIDATE_URL) ? $data['viewurl'] : '';
        $obj->description = isset($data['description']) ? $data['description'] : '';

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

    // Получаем все товары
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

        $ids = array_filter($ids, fn($id) => is_int($id) || ctype_digit($id));

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