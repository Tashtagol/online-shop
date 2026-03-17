<?php

namespace Service;

use Model\Model;
use Model\Order;
use Model\Product;
use Model\UserProduct;
use DTO\CreateOrderDTO;
use Model\CartItem;


class OrderService
{

    public function create(CreateOrderDTO $orderDTO)
    {
           $pdo=Model::getPDO();
            $userProducts = UserProduct::getUserIdCart($orderDTO->getUserId());
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

            $orderItems = [];
            foreach ($userProducts as $item) {
                $pid = $item->product_id;

                if (isset($products[$pid])) {
                    $price = $products[$pid]->getPrice();

                    $orderItems[] = ['product_id' => $pid, 'amount' => $item->amount, 'price' => $price];
                }
            }

            $order->saveProducts($orderItems);
            UserProduct::clearCart($orderDTO->getUserId());
            $pdo->commit();
            return $order;
        }
        catch
            (\PDOException $exception) {
                $pdo->rollBack();
                throw $exception;
            }

    }

    public function getCartData(int $userId): array
    {
        // Получаем все товары из корзины пользователя
        $userProducts = UserProduct::getUserIdCart($userId);

        if (empty($userProducts)) {
            return ['items' => [], 'total' => 0];  // Если корзина пуста, сразу возвращаем пустой массив
        }

        $orderItems = [];
        $total = 0;

        // Перебираем товары в корзине и создаем CartItem для каждого
        foreach ($userProducts as $item) {
            // Создаем объект CartItem
            $cartItem = new CartItem(
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
            $total += $cartItem->getSum();
        }

        // Возвращаем итоговые данные
        return [
            'items' => $orderItems,
            'total' => $total
        ];
    }
}