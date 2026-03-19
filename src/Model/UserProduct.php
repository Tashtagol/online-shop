<?php
namespace Model;

use PDO;

class UserProduct extends Model
{
    public static function getUserProduct(int $userId, int $productId)
    {
        // Подготовка запроса
        $stmt = self::getPDO()->prepare("SELECT amount FROM user_product WHERE user_id = :user_id AND product_id = :product_id");

        // Выполнение запроса
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);

        // Получаем результат как объект
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $result ? $result : null;  // Возвращаем объект, если он есть, или null, если нет
    }

    public static function addProductToCart(int $userId, int $productId, int $amount)
    {
        $stmt = self::getPDO()->prepare("INSERT INTO user_product (user_id, product_id, amount)VALUES (:user_id, :product_id, :amount)");

        $stmt->execute(['user_id' => $userId, 'product_id' => $productId, 'amount' => $amount]);
    }

    public static function updateUserProduct(int $amount, int $userId, int $productId)
    {
        // Если количество = 0, удаляем товар из корзины
        if ($amount <= 0) {
            self::removeCartItem($userId, $productId);
        } else {
            // Обновляем количество товара
            $stmt = self::getPDO()->prepare("UPDATE user_product SET amount = :amount WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->execute(['amount' => $amount, 'user_id' => $userId, 'product_id' => $productId]);
        }
    }

    public static function getUserCartItems(int $userId): array
    {
        $stmt = self::getPDO()->prepare("SELECT user_product.product_id,user_product.amount,products.name,products.price,products.viewurl,products.description 
        FROM user_product INNER JOIN products ON user_product.product_id = products.id
        WHERE user_product.user_id= :user_id
        ORDER BY products.name");

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static  function clearCart(int $userId)
    {
        $stmt = self::getPDO()->prepare("DELETE FROM user_product WHERE user_id = :user_id");

        $stmt->execute(['user_id' => $userId]);
    }
    public static function removeCartItem(int $userId, int $productId)
    {
        $stmt = self::getPDO()->prepare("DELETE FROM user_product WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
    }
}