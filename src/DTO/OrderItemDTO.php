<?php
namespace DTO;

class OrderItemDTO
{
    public function __construct(
        private int $productId,
        private int $orderId,
        private int $amount,
        private float $price,
        private string $name,
        private string $viewUrl,
        private string $description
    ) {}

    public function getProductId(): int { return $this->productId; }
    public function getOrderId(): int { return $this->orderId; }
    public function getAmount(): int { return $this->amount; }
    public function getPrice(): float { return $this->price; }
    public function getName(): string { return $this->name; }
    public function getViewUrl(): string { return $this->viewUrl; }
    public function getDescription(): string { return $this->description; }
}