<?php

namespace Service;

use DTO\CreateOrderDTO;
use Model\Model;
use Model\Order;
use Model\Product;
use Model\UserProduct;
use Service\CartService;

class OrderService {
    public function __construct(private CartService $cartService) {}

    public function create(CreateOrderDTO $dto): ?Order {
        $items = $this->cartService->getCart($dto->getUserId());
        if (empty($items)) return null;

        $pdo = Model::getPDO();
        try {
            $pdo->beginTransaction();
            $order = Order::create(
                $dto->getUserId(), $dto->getName(), $dto->getEmail(),
                $dto->getAddress(), $dto->getPhone(), $dto->getPayment()
            );
            $order->saveProducts($items); // Принимает массив ProductItemDTO
            UserProduct::clearCart($dto->getUserId());
            $pdo->commit();
            return $order;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}