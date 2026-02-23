<?php
namespace Model;
use PDO;
class Product extends Model
{
    private int $id;
    private string $name;
    private float $price;
    private string $vieurl;
    private string $description;


    public function __construct(int $id, string $name, float $price, string $vieurl, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->vieurl = $vieurl;
        $this->description = $description;

    }
    public static function createFromData(array $data): self
    {
        return new self((int)$data['id'], $data['name'], (float)$data['price'], $data['viewurl'] ?? '', $data['description']
        );
    }

    public function getProducts(): array
    {
        try {
            $stmt = self::getPdo()->query("SELECT * FROM products");
            $rows =  $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map (fn($data)=> self::createFromData($data), $rows);
        } catch (PDOException $e) {
            die("Ошибка подключения к БД: " . $e->getMessage());
        }
    }


    public function getProductsByIds(array $ids): array
    {
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = self::getPdo()->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => self::createFromData($data), $rows);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getVieUrl(): string
    {
        return $this->vieurl;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

}

