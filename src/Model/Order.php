<?php

namespace Model;

use PDO;

class Order extends Model
{
    private ?int $id = null;
    private int $userId;
    private string $name;
    private string $email;
    private string $address;
    private string $phone;
    private ?string $orderNumber = null;
    private ?string $orderDate = null; // Поле для даты заказа
    private array $products = [];

    public function __construct(
        int $userId = 0,
        string $name = '',
        string $email = '',
        string $address = '',
        string $phone = '',
        ?int $id = null,
        ?string $orderNumber = null,
        ?string $orderDate = null // Параметр для даты
    ) {
        parent::__construct();

        $this->userId = $userId;
        $this->name = $name;
        $this->email = $email;
        $this->address = $address;
        $this->phone = $phone;
        $this->id = $id;
        $this->orderNumber = $orderNumber;
        $this->orderDate = $orderDate; // Инициализируем дату
    }

    // Геттер для получения даты заказа
    public function getOrderDate(): ?string
    {
        return $this->orderDate;
    }

    // Метод для создания объекта из данных из базы
    public static function createFromData(array $data): self
    {
        return new self(
            (int) ($data['user_id'] ?? 0),
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['address'] ?? '',
            $data['telephone'] ?? '',
            isset($data['id']) ? (int) $data['id'] : null,
            $data['order_number'] ?? null,
            $data['order_date'] ?? null
        );
    }

    // Сохраняем заказ в базе данных
    public function saveOrder(): self
    {
        $this->orderNumber = $this->generateOrderNumber();

        // Получаем текущую дату для поля order_date
        $orderDate = date('Y-m-d H:i:s');

        // Запрос на вставку данных заказа в базу данных
        $stmt = $this->pdo->prepare(
            "INSERT INTO orders (user_id, name, email, address, telephone, order_number, order_date) VALUES (:user_id, :name, :email, :address, :telephone, :order_number, :order_date) RETURNING id, order_number, order_date");

        $stmt->execute([':user_id'  => $this->userId, ':name' => $this->name, ':email'=> $this->email, ':address' => $this->address, ':telephone' => $this->phone, ':order_number' => $this->orderNumber, ':order_date'=> $orderDate]);

        // Получаем данные о заказе, включая id и order_date
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->id = (int) $data['id'];
            $this->orderNumber = $data['order_number'];
            $this->orderDate = $data['order_date']; // Сохраняем дату
        }

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

        return (bool) $stmt->fetchColumn();
    }

    // Получаем все заказы пользователя как объекты
    public function getAllByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn ($data) => self::createFromData($data), $rows);
    }

    // Получаем заказ по ID
    public function getById(int $orderId): ?self
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $orderId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::createFromData($data) : null;
    }

    // Получаем заказ по номеру
    public function getByNumber(string $number): ?self
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE order_number = :number");
        $stmt->execute(['number' => $number]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::createFromData($data) : null;
    }

    // Сохраняем товары заказа в bulk
    public function saveOrderProductsBulk(array $orderItems): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO order_products (order_id, product_id, amount, price) VALUES (:order_id, :product_id, :amount, :price)");

        foreach ($orderItems as $item) {
            $stmt->execute([':order_id'   => $this->id, ':product_id' => $item['product_id'], ':amount'     => $item['amount'], ':price'      => $item['price']]);
        }
    }

    // Получаем продукты заказа как объекты OrderProduct
    public function getOrderProducts(): array
    {
        // 1️⃣ Получаем все строки из order_products
        $stmt = $this->pdo->prepare("SELECT * FROM order_products WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $this->id]);
        $orderRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$orderRows) return [];

        // 2️⃣ Получаем объекты Product по product_id
        $productIds = array_column($orderRows, 'product_id');
        $products = (new Product())->getProductsByIds($productIds);
        // вернёт массив Product объектов с ключом = id

        // 3️⃣ Объединяем данные вручную в OrderModel
        $result = [];
        foreach ($orderRows as $row) {
            $pid = $row['product_id'];
            $product = $products[$pid] ?? null;

            if ($product) {
                $data = ['product_id' => $pid, 'order_id' => $row['order_id'], 'amount' => $row['amount'], 'price' => $row['price'], 'name' => $product->getName(), 'view_url' => $product->getVieUrl(),];

                $result[] = new OrderModel($data);
            }
        }

        return $result;
    }

    // Для фронта: установить продукты с дополнительными полями
    public function setProducts(array $products): void
    {
        $this->products = $products;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    // Геттеры
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }
}