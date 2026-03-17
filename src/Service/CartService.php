<?php
namespace Service;

use Model\Product;
use Model\UserProduct;
use Model\CartItem;

class CartService
{

    public function getCart(int $userId): array
    {
        $products = UserProduct::getUserIdCart($userId);

        $result = [];
        foreach ($products as $item) {
            $result[] = new CartItem(
                $item->product_id,          // ID продукта
                $item->name,                // Название
                $item->price,               // Цена
                $item->amount,              // Количество
                $item->viewurl,             // URL изображения
                $item->description          // Описание
            );
        }

        return $result;
    }

    public function getTotal(array $products): float
    {
        $total = 0;

        // Пересчитываем общую сумму
        foreach ($products as $product) {
            // Проверяем, что есть корректные данные для расчета
            if ($product->getPrice() && $product->getAmount()) {  // Используем методы для доступа к данным
                $total += $product->getPrice() * $product->getAmount();  // Умножаем цену на количество товара
            }
        }

        return $total;
    }

    public function add(int $userId, int $productId, int $amount = 1)
    {
        // Проверяем, есть ли товар в корзине
        $existing = UserProduct::getUserProduct($userId, $productId);

        // Если товар есть, обновляем его количество
        if ($existing) {
            // Обновляем количество
            UserProduct::updateUserProduct($existing->amount + $amount, $userId, $productId);
        } else {
            // Если товара нет, добавляем новый
            UserProduct::setUserProduct($userId, $productId, $amount);
        }

        return true;
    }


    public function update(int $userId, int $productId, int $amount)
    {
        if ($amount <= 0) {
            UserProduct::clearCartItem($userId, $productId);
        } else {
            UserProduct::updateUserProduct($amount, $userId, $productId);
        }

        return true;
    }

    public function clear(int $userId)
    {
        UserProduct::clearCart($userId);
        return true;
    }
    public function remove(int $userId, int $productId)
    {
        // Логика удаления товара из корзины
        UserProduct::clearCartItem($userId, $productId);  // Удаляем товар из таблицы user_product
    }
}