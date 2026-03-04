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

    public function __construct(int $id = 0, string $name = '', float $price = 0.0, string $viewurl = '', string $description = ''
    ) {
        parent::__construct();
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->viewurl = $viewurl;
        $this->description = $description;
    }

    public static function createFromData(array $data): self
    {
        return new self((int)$data['id'], $data['name'], (float)$data['price'], $data['viewurl'], $data['description']);
    }

    public function getAllProducts(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM products");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $product = self::createFromData($row);
            $result[$product->getId()] = $product;
        }

        return $result;
    }
    public function getProductById(int $id): ?self
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return self::createFromData($row);
        }

        return null; // возвращаем null если продукта нет
    }

    public function getProductsByIds(array $ids): array
    {
        if (empty($ids)) return [];

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $product = self::createFromData($row);
            $result[$product->getId()] = $product;
        }

        return $result;
    }

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
    public function getVieUrl(): string { return $this->viewurl; }
    public function getDescription(): string { return $this->description; }
}