<?php

namespace Model;

use PDO;

class UserProduct extends Model
{
    public static function getUserProduct(int $userId, int $productId): ?object
    {
        try {
            $stmt = self::getPDO()->prepare("SELECT amount FROM user_product WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
            return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
        } catch (\PDOException $e) {
            error_log("Error fetching cart product: " . $e->getMessage());
            throw new \Exception("Ошибка при получении товара из корзины.");
        }
    }

    public static function addProductToCart(int $userId, int $productId, int $amount): void
    {
        try {
            $stmt = self::getPDO()->prepare("INSERT INTO user_product (user_id, product_id, amount) VALUES (:user_id, :product_id, :amount)");
            $stmt->execute(['user_id' => $userId, 'product_id' => $productId, 'amount' => $amount]);
        } catch (\PDOException $e) {
            error_log("Error adding product to cart: " . $e->getMessage());
            throw new \Exception("Ошибка при добавлении товара в корзину.");
        }
    }

    public static function updateUserProduct(int $amount, int $userId, int $productId): void
    {
        try {
            if ($amount <= 0) {
                self::removeCartItem($userId, $productId);
            } else {
                $stmt = self::getPDO()->prepare("UPDATE user_product SET amount = :amount WHERE user_id = :user_id AND product_id = :product_id");
                $stmt->execute(['amount' => $amount, 'user_id' => $userId, 'product_id' => $productId]);
            }
        } catch (\PDOException $e) {
            error_log("Error updating cart product: " . $e->getMessage());
            throw new \Exception("Ошибка при обновлении товара в корзине.");
        }
    }

    public static function getUserCartItems(int $userId): array
    {
        try {
            $stmt = self::getPDO()->prepare("SELECT user_product.product_id, user_product.amount,products.name, products.price, products.viewurl, products.description
                FROM user_product
                INNER JOIN products ON user_product.product_id = products.id
                WHERE user_product.user_id = :user_id
                ORDER BY products.name");

            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log("Error fetching cart items: " . $e->getMessage());
            throw new \Exception("Ошибка при получении корзины.");
        }
    }

    public static function clearCart(int $userId): void
    {
        try {
            $stmt = self::getPDO()->prepare("DELETE FROM user_product WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
        } catch (\PDOException $e) {
            error_log("Error clearing cart: " . $e->getMessage());
            throw new \Exception("Ошибка при очистке корзины.");
        }
    }

    public static function removeCartItem(int $userId, int $productId): void
    {
        try {
            $stmt = self::getPDO()->prepare("DELETE FROM user_product WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        } catch (\PDOException $e) {
            error_log("Error removing cart item: " . $e->getMessage());
            throw new \Exception("Ошибка при удалении товара из корзины.");
        }
    }
}