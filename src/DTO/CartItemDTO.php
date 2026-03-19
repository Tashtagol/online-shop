<?php

namespace DTO;

class CartItemDTO
{
    private int $id;
    private string $name;
    private float $price;
    private int $amount;
    private float $sum;
    private string $viewurl;
    private string $description;

    public function __construct(int $id, string $name, float $price, int $amount, string $viewurl, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->amount = $amount;
        $this->sum = $price * $amount;  // Подсчитываем сумму сразу
        $this->viewurl = $viewurl;
        $this->description = $description;
    }

    // Геттеры для каждого поля
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
    public function getAmount(): int { return $this->amount; }
    public function getSum(): float { return $this->sum; }
    public function getViewUrl(): string { return $this->viewurl; }
    public function getDescription(): string { return $this->description; }
}