<?php

namespace Model;

use DTO\OrderItemDTO;
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
    private ?string $orderDate = null; // Дата заказа
    private array $products = [];
    private string $payment;

    // Создаем и сохраняем заказ
    public static function create(int $userId, string $name, string $email, string $address, string $phone, string $paymentMethod
    ): self
    {
        $orderNumber = self::generateOrderNumber();
        $orderDate = date('Y-m-d H:i:s');

        $stmt = self::getPDO()->prepare("INSERT INTO orders (user_id, name, email, address, telephone, order_number, order_date, payment)  VALUES (:user_id, :name, :email, :address, :telephone, :order_number, :order_date, :payment)  RETURNING id, order_number, order_date");

        $stmt->execute([
            ':user_id'=> $userId,
            ':name' => $name,
            ':email' => $email,
            ':address'=> $address,
            ':telephone' => $phone,
            ':order_number' => $orderNumber,
            ':order_date'=> $orderDate,
            ':payment' => $paymentMethod
        ]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        $order = new self();
        $order->id = (int)$data['id'];
        $order->userId = $userId;
        $order->name = $name;
        $order->email = $email;
        $order->address = $address;
        $order->phone = $phone;
        $order->orderNumber = $data['order_number'];
        $order->orderDate = $data['order_date'];
        $order->payment = $paymentMethod;

        return $order;
    }

    // Генерация уникального номера заказа
    private static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $stmt = self::getPDO()->prepare("SELECT 1 FROM orders WHERE order_number = ?");
            $stmt->execute([$number]);
        } while ($stmt->fetchColumn());

        return $number;
    }

    // Проверка, принадлежит ли заказ пользователю
    public function orderBelongsToUser(int $userId): bool
    {
        $stmt = self::getPDO()->prepare("SELECT 1 FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$this->id, $userId]);

        return (bool) $stmt->fetchColumn();
    }

    // Получаем все заказы пользователя
    public static function getAllByUserId(int $userId): array
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM orders WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn ($data) => self::mapDataToOrder($data), $rows);
    }

    // Получаем заказ по ID
    public static function getById(int $orderId): ?self
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $orderId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::mapDataToOrder($data) : null;
    }

    // Получаем заказ по номеру
    public static function getByNumber(string $number): ?self
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM orders WHERE order_number = :number");
        $stmt->execute(['number' => $number]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::mapDataToOrder($data) : null;
    }

    // Сохраняем товары заказа
    public function saveProducts(array $orderItems): void
    {
        $stmt = self::getPDO()->prepare(
            "INSERT INTO order_products (order_id, product_id, amount, price) 
            VALUES (:order_id, :product_id, :amount, :price)"
        );
        foreach ($orderItems as $item) {
            if ($item instanceof \DTO\CartItemDTO) {
                $stmt->execute([
                    ':order_id' => $this->id,
                    ':product_id' => $item->getId(),
                    ':amount' => $item->getAmount(),
                    ':price' => $item->getPrice()
                ]);
            }
        }
        }

    // Получаем продукты заказа как объекты OrderItemDTO
    public function getOrderProducts(): array
    {
        if ($this->products) {
            return $this->products;
        }

        $stmt = self::getPDO()->prepare("SELECT order_products.order_id, order_products.product_id, order_products.amount, order_products.price, products.name, products.viewurl, products.description
        FROM order_products 
        INNER JOIN products ON order_products.product_id = products.id
        WHERE order_products.order_id = :order_id
        ORDER BY products.id");
        $stmt->execute(['order_id' => $this->id]);
        $orderRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (!$orderRows) return [];

        $result = [];
        foreach ($orderRows as $row) {
            $result[] = new OrderItemDTO(
                productId: $row['product_id'],
                orderId: $row['order_id'],
                amount: $row['amount'],
                price: $row['price'],
                name: $row['name'],
                viewUrl: $row['viewurl'],
                description: $row['description']
            );
        }

        $this->products = $result;
        return $result;
    }

    // Привязка данных из БД к объекту
    private static function mapDataToOrder(array $data): self
    {
        $order = new self();
        $order->id = (int)($data['id'] ?? null);
        $order->userId = (int)($data['user_id'] ?? 0);
        $order->name = $data['name'] ?? '';
        $order->email = $data['email'] ?? '';
        $order->address = $data['address'] ?? '';
        $order->phone = $data['telephone'] ?? '';
        $order->orderNumber = $data['order_number'] ?? null;
        $order->orderDate = $data['order_date'] ?? null;
        return $order;
    }
    public static function checkVerifiedPurchase(int $userId, int $productId): bool
    {
        $stmt = self::getPDO()->prepare("SELECT 1 FROM order_products INNER JOIN orders ON order_products.order_id = orders.id
            WHERE orders.user_id = :user_id AND order_products.product_id = :product_id LIMIT 1");

        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);

        // Если нашли запись, значит продукт был заказан
        return (bool) $stmt->fetchColumn();
    }


    public static function isProductInUserOrder(int $productId, int $userId): bool
    {
        $stmt = self::getPDO()->prepare(
            "SELECT 1 FROM order_products 
            WHERE order_id IN (SELECT id FROM orders WHERE user_id = :user_id) 
            AND product_id = :product_id"
        );
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);

        return $stmt->fetchColumn() !== false;
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
    { return
        $this->name;
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
    public function getOrderDate(): ?string
    {
        return $this->orderDate;
    }

    // Можно вручную установить продукты
    public function setProducts(array $products): void
    {
        $this->products = $products;
    }
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getPayment(): string
    {
        return $this->payment;
    }

}