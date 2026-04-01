<?php

namespace DTO;

class ProductItemDTO
{
    public function __construct(
        private int $id,
        private string $name,
        private float $price,
        private int $amount,
        private string $viewUrl,
        private string $description = ''
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
    public function getAmount(): int { return $this->amount; }
    public function getViewUrl(): string { return $this->viewUrl; }
    public function getDescription(): string { return $this->description; }

    // Логика подсчета суммы внутри DTO — это правильно
    public function getSum(): float { return $this->price * $this->amount; }
}