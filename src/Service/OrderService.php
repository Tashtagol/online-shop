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
        // Получаем все товары из корзины пользователя
        $userProducts = UserProduct::getUserCartItems($userId);

        if (empty($userProducts)) {
            return ['items' => [], 'total' => 0];  // Если корзина пуста, сразу возвращаем пустой массив
        }

        $orderItems = [];
        $total = 0;

        // Перебираем товары в корзине и создаем CartItem для каждого
        foreach ($userProducts as $item) {
            // Создаем объект CartItem
            $cartItem = new CartItemDTO(
                $item->product_id,     // ID товара
                $item->name,           // Название товара
                $item->price,          // Цена товара
                $item->amount,         // Количество товара
                $item->viewurl,        // URL изображения товара
                $item->description     // Описание товара
            );

            // Добавляем CartItem в итоговый массив
            $orderItems[] = [
                'product_id' => $cartItem->getId(),
                'name' => $cartItem->getName(),
                'price' => $cartItem->getPrice(),
                'amount' => $cartItem->getAmount(),
                'sum' => $cartItem->getSum(),
                'viewurl' => $cartItem->getViewUrl(),
                'description' => $cartItem->getDescription(),
            ];

            // Суммируем общую стоимость
            $total += $this->cartService->calculateSum($cartItem->getPrice(), $cartItem->getAmount());
        }

        // Возвращаем итоговые данные
        return [
            'items' => $orderItems,
            'total' => $total
        ];
    }

    public function getProductById(int $productId): ?\Model\Product
    {
        return \Model\Product::getProductById($productId); // Предполагаем, что в Product есть findById }
    }
}