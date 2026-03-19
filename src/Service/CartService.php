<?php
namespace Service;

use DTO\CartItemDTO;
use Model\UserProduct;

class CartService
{
    public static function createCartItemDTOs(array $products): array
    {
        $result = [];
        foreach ($products as $item) {
            $result[] = new CartItemDTO(
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

    public function getCart(int $userId): array
    {
        $products = UserProduct::getUserCartItems($userId);
        return $this->createCartItemDTOs($products);
    }
    public function calculateSum(float $price, int $amount): float
    {
        return $price * $amount;
    }

    public function calculateCartTotal(array $products): float
    {
        $total = 0;

        // Для каждого товара рассчитываем его сумму и добавляем к общей
        foreach ($products as $product) {
            $total += $this->calculateSum($product->getPrice(), $product->getAmount());
        }

        return $total;
    }

    public function addOrUpdateProductInCart(int $userId, array $data)
    {
        // Получаем данные из массива данных и обрабатываем их
        $productId = intval($data['product_id'] ?? 0);
        $amount = intval($data['amount'] ?? 1);
        $source = $data['source'] ?? 'catalog';

        // Получаем товар из корзины
        $cartProduct = UserProduct::getUserProduct($userId, $productId);

        if ($cartProduct) {
            // Если товар уже есть в корзине, увеличиваем его количество
            $newAmount = ($source === 'catalog') ? $cartProduct->amount + $amount : $amount;

            // Если количество товара <= 0, удаляем товар из корзины
            if ($newAmount <= 0) {
                $this->removeCartItem($userId, $productId);
            } else {
                $newAmount = max(0, $newAmount);  // Устанавливаем минимальное количество = 0
                $this->updateCartItem($userId, $productId, $newAmount);
            }
        } else {
            // Если товара нет в корзине, добавляем его
            $this->addCartItem($userId, $productId, $amount);
        }

        // После обновления/добавления товара, пересчитываем данные корзины
        $products = $this->getCart($userId);
        $total = $this->calculateCartTotal($products);

        $subtotal = 0;
        foreach ($products as $product) {
            if ($product->getId() === $productId) {
                $subtotal = $product->getPrice() * $product->getAmount();  // Считаем стоимость конкретного товара
                break;
            }
        }

        // Подсчитываем общее количество товаров в корзине
        $count = array_sum(array_map(fn($product) => $product->getAmount(), $products));

        return [
            'total' => $total,
            'subtotal' => $subtotal,
            'count' => $count
        ];
    }


    public function addCartItem(int $userId, int $productId, int $amount = 1)
    {
        // Проверяем, есть ли товар в корзине
        $existing = UserProduct::getUserProduct($userId, $productId);

        // Если товар есть, обновляем его количество
        if ($existing) {
            // Обновляем количество
            UserProduct::updateUserProduct($existing->amount + $amount, $userId, $productId);
        } else {
            // Если товара нет, добавляем новый
            UserProduct::addProductToCart($userId, $productId, $amount);
        }

        return true;
    }



    public function updateCartItem(int $userId, int $productId, int $amount)
    {
        if ($amount <= 0) {
            UserProduct::removeCartItem($userId, $productId);
        } else {
            UserProduct::updateUserProduct($amount, $userId, $productId);
        }

        return true;
    }

    public function clearCart(int $userId)
    {
        UserProduct::clearCart($userId);
        return true;
    }
    public function removeCartItem(int $userId, int $productId)
    {
        // Логика удаления товара из корзины
        UserProduct::removeCartItem($userId, $productId);  // Удаляем товар из таблицы user_product
    }
}