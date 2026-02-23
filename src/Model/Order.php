<?php
namespace Model;
use PDO;
use PDOException;

class Order extends Model
{
    private ?int $id = null;
    private int $userId;
    private string $name;
    private string $email;
    private string $address;
    private string $phone;
    private ?string $orderNumber = null;

    public function __construct(
        int $userId,
        string $name,
        string $email,
        string $address,
        string $phone,
        ?int $id = null,
        ?string $orderNumber = null
    ) {
        $this->userId = $userId;
        $this->name = $name;
        $this->email = $email;
        $this->address = $address;
        $this->phone = $phone;
        $this->id = $id;
        $this->orderNumber = $orderNumber;
    }

    // Создание объекта из массива данных
    public static function createFromData(array $data): self
    {
        return new self((int)$data['user_id'], $data['name'], $data['email'], $data['address'], $data['telephone'] ?? '', $data['id'] ?? null, $data['order_number'] ?? null
        );
    }

    // Обновляем текущий объект из массива данных
    public function hydrate(array $data): void
    {
        if (isset($data['id'])) $this->id = (int)$data['id'];
        if (isset($data['order_number'])) $this->orderNumber = $data['order_number'];
        if (isset($data['user_id'])) $this->userId = (int)$data['user_id'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['email'])) $this->email = $data['email'];
        if (isset($data['address'])) $this->address = $data['address'];
        if (isset($data['telephone'])) $this->phone = $data['telephone'];
    }

    // Сохраняем заказ и обновляем объект
    public function saveOrder(): self
    {
        $this->orderNumber = $this->generateOrderNumber();

        $stmt = $this->pdo->prepare("INSERT INTO orders (user_id, name, email, address, telephone, order_number)VALUES (:user_id, :name, :email, :address, :telephone, :order_number)RETURNING id, order_number");

        $stmt->execute([':user_id' => $this->userId, ':name' => $this->name, ':email' => $this->email, ':address' => $this->address, ':telephone' => $this->phone, ':order_number' => $this->orderNumber]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->hydrate($data);

        return $this;
    }

    // Генерация уникального номера заказа
    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $stmt = $this->pdo->prepare("SELECT 1 FROM orders WHERE order_number = ?");
            $stmt->execute([$number]);
        } while ($stmt->fetchColumn());

        return $number;
    }

    // Проверка, принадлежит ли заказ пользователю
    public function orderBelongsToUser(int $orderId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$orderId, $userId]);
        return (bool)$stmt->fetchColumn();
    }

    // Получаем все заказы пользователя в виде объектов
    public static function getAllByUserId(int $userId): array
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM orders WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => self::createFromData($data), $rows);
    }

    // Получаем заказ по ID
    public static function getById(int $orderId): ?self
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $orderId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::createFromData($data) : null;
    }

    // Получаем заказ по номеру
    public static function getByNumber(string $number): ?self
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM orders WHERE order_number = :number");
        $stmt->execute(['number' => $number]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::createFromData($data) : null;
    }

    // Геттеры
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUserId(): int {
        return $this->userId;
    }
    public function getName(): string {
        return $this->name;
    }
    public function getEmail(): string {
        return $this->email;
    }
    public function getAddress(): string {
        return $this->address;
    }
    public function getPhone(): string {
        return $this->phone;
    }
    public function getOrderNumber(): ?string {
        return $this->orderNumber;
    }
}