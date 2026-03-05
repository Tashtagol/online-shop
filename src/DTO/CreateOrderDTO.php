<?php
namespace DTO;

class CreateOrderDTO
{
    public function __construct(
        private int $userId,
        private string $name,
        private string $email,
        private string $address,
        private string $phone
    )
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->email = $email;
        $this->address = $address;
        $this->phone = $phone;
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
}