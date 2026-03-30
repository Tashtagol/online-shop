<?php

namespace Service;

use DTO\CartItemDTO;
use DTO\CreateOrderDTO;
use Model\Model;
use Model\Order;
use Model\Product;
use Model\UserProduct;
use Service\CartService;


class OrderService
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function create(CreateOrderDTO $orderDTO) //Избавиться от циклов и мапить методами
    {
        $pdo = Model::getPDO();
        $userProducts = UserProduct::getUserCartItems($orderDTO->getUserId());

        if (empty($userProducts)) {
            return null;
        }
        try {
            $pdo->beginTransaction();
            $order = Order::create(
                $orderDTO->getUserId(),
                $orderDTO->getName(),
                $orderDTO->getEmail(),
                $orderDTO->getAddress(),
                $orderDTO->getPhone(),
                $orderDTO->getPayment()
            );

            $productIds = array_map(fn($item) => $item->product_id, $userProducts);
            $products = Product::getProductsByIds($productIds);

            $orderItems = CartService::createCartItemDTOs($userProducts);

            $order->saveProducts($orderItems);
            UserProduct::clearCart($orderDTO->getUserId());
            $pdo->commit();
            return $order;
        } catch
        (\PDOException $exception) {
            $pdo->rollBack();
            throw $exception;
        }

    }

    public function getUserCartData(int $userId): array
    {
        // Получаем DTO через CartService
        $cartItems = $this->cartService->getCart($userId);

        if (empty($cartItems)) {
            return [
                'items' => [],
                'total' => 0
            ];
        }

        $items = array_map(function (CartItemDTO $item) {
            return [
                'product_id' => $item->getId(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'amount' => $item->getAmount(),
                'sum' => $item->getSum(),
                'viewurl' => $item->getViewUrl(),
                'description' => $item->getDescription(),
            ];
        }, $cartItems);

        return [
            'items' => $items,
            'total' => $this->cartService->getTotal($cartItems)
        ];
    }
}