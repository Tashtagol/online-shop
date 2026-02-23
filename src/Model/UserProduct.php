<?php
namespace Model;
use PDO;
class UserProduct extends Model
{
    public function getUserProduct (int $userId, int $productId)
    {
        $stmt = $this->pdo->prepare("SELECT amount FROM user_product WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        return $stmt->fetch();
    }
    public function setUserProduct(int $userId, int $productId, int $amount)
    {
        $stmt = $this->pdo->prepare("INSERT INTO user_product (user_id, product_id, amount) VALUES (:user_id, :product_id, :amount)");
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId, 'amount' => $amount]);
        return $this->pdo->lastInsertId(); // вернёт id вставленной строки, если есть
    }
    public function updateUserProduct(int $amount, int $userId, int $productId)
    {
        $stmt = $this->pdo->prepare("UPDATE user_product SET amount = amount + :amount WHERE user_id = :user_id and product_id = :product_id");
        return $stmt->execute(['amount' => $amount, 'user_id' => $userId, 'product_id' => $productId
        ]);
    }

    public function getUserIdCart(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT product_id, amount FROM user_product WHERE user_id = :userId");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function clearCart(int $userId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM user_product WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
    }
}