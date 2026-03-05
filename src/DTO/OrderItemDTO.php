<?php

namespace DTO;
use PDO;

class OrderItemDTO
{
    private int $product_id;
    private int $order_id;
    private int $amount;
    private float $price;
    private string $name;
    private string $viewUrl;

    public function __construct(array $data)
    {
        $this->product_id = (int)($data['product_id'] ?? 0);
        $this->order_id = (int)($data['order_id'] ?? 0);
        $this->amount = (int)($data['amount'] ?? 0);
        $this->price = (float)($data['price'] ?? 0);
        $this->name = $data['name'] ?? '';
        $this->viewUrl = $data['view_url'] ?? '';
    }

    // 🔹 Геттеры
    public function getProductId(): int { return $this->product_id; }
    public function getOrderId(): int { return $this->order_id; }
    public function getAmount(): int { return $this->amount; }
    public function getPrice(): float { return $this->price; }
    public function getName(): string { return $this->name; }
    public function getViewUrl(): string { return $this->viewUrl; }
}
