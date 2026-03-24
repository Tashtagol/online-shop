<?php

namespace Model;

use PDO;

class Review extends Model
{
    private int $user_id;
    private int $product_id;
    private int $rating;
    private string $comment;
    private bool $is_verified_purchase;

    // Метод для создания отзыва
    public static function createReview(int $user_id, int $product_id, int $rating, string $comment, bool $is_verified_purchase): void
    {
        try {
            $stmt = self::getPDO()->prepare("
            INSERT INTO reviews (user_id, product_id, rating, comment, is_verified_purchase) 
            VALUES (:user_id, :product_id, :rating, :comment, :is_verified_purchase)
        ");

            $stmt->execute([
                'user_id' => $user_id,
                'product_id' => $product_id,
                'rating' => $rating,
                'comment' => $comment,
                'is_verified_purchase' => $is_verified_purchase
            ]);
        } catch (\PDOException $e) {
            error_log("PostgreSQL error creating review: " . $e->getMessage());
            error_log("Data: user_id=$user_id, product_id=$product_id, rating=$rating, comment=$comment, verified=" . ($is_verified_purchase ? 'true' : 'false'));
            throw new \Exception("Ошибка при создании отзыва.");
        }
    }

    // Метод для получения отзывов по ID продукта
    public static function getReviewsByProductId(int $productId): array
    {
        try {
            $stmt = self::getPDO()->prepare("SELECT reviews.*, users.name AS user_name  FROM reviews INNER JOIN users ON reviews.user_id = users.id WHERE product_id = :product_id ORDER BY id DESC");
            $stmt->execute(['product_id' => $productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error fetching reviews: " . $e->getMessage());
            throw new \Exception("Ошибка при получении отзывов.");
        }
    }

    // Сеттеры для каждого поля (если нужно использовать их)
    public function setUserId(int $user_id): void { $this->user_id = $user_id; }
    public function setProductId(int $product_id): void { $this->product_id = $product_id; }
    public function setRating(int $rating): void { $this->rating = $rating; }
    public function setComment(string $comment): void { $this->comment = $comment; }
    public function setIsVerifiedPurchase(bool $is_verified_purchase): void { $this->is_verified_purchase = $is_verified_purchase; }

    // Геттеры для каждого поля
    public function getUserId(): int { return $this->user_id; }
    public function getProductId(): int { return $this->product_id; }
    public function getRating(): int { return $this->rating; }
    public function getComment(): string { return $this->comment; }
    public function getIsVerifiedPurchase(): bool { return $this->is_verified_purchase; }
}