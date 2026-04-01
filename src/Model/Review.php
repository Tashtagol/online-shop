<?php

namespace Model;

use PDO;

class Review extends Model
{
    private int $id;
    private int $user_id;
    private int $product_id;
    private int $rating;
    private string $comment;
    private bool $is_verified_purchase;
    private bool $is_edited;
    private string $user_name = '';
    private string $created_date = '';

    public static function fromArray(array $data): self
    {
        $review                       = new self();
        $review->id                   = (int)($data['id'] ?? 0);
        $review->user_id              = (int)$data['user_id'];
        $review->product_id           = (int)$data['product_id'];
        $review->rating               = (int)$data['rating'];
        $review->comment              = $data['comment'];
        $review->is_verified_purchase = (bool)$data['is_verified_purchase'];
        $review->is_edited            = (bool)($data['is_edited'] ?? false);
        $review->user_name            = $data['user_name'] ?? '';
        $review->created_date         = $data['created_date'] ?? '';
        return $review;
    }

    public static function createReview(int $user_id, int $product_id, int $rating, string $comment): void
    {
        try {
            $stmt = self::getPDO()->prepare("
                INSERT INTO reviews (user_id, product_id, rating, comment, is_verified_purchase)
                VALUES (:user_id, :product_id, :rating, :comment, :is_verified_purchase)
            ");
            $stmt->execute(['user_id'=> $user_id, 'product_id' => $product_id, 'rating'=> $rating, 'comment'=> $comment, 'is_verified_purchase' => true,]);
        } catch (\PDOException $e) {
            error_log("PostgreSQL error creating review: " . $e->getMessage());
            throw new \Exception("Ошибка при создании отзыва.");
        }
    }

    public static function getReviewsByProductId(int $productId): array
    {
        try {
            $stmt = self::getPDO()->prepare("SELECT reviews.*, users.name AS user_name FROM reviews INNER JOIN users ON reviews.user_id = users.id WHERE product_id = :product_id ORDER BY reviews.id DESC");
            $stmt->execute(['product_id' => $productId]);
            return array_map(
                fn(array $row) => self::fromArray($row),
                $stmt->fetchAll(PDO::FETCH_ASSOC)
            );
        } catch (\PDOException $e) {
            error_log("Error fetching reviews: " . $e->getMessage());
            throw new \Exception("Ошибка при получении отзывов.");
        }
    }

    public static function getReviewById(int $reviewId): ?self
    {
        try {
            $stmt = self::getPDO()->prepare("SELECT reviews.*, users.name AS user_name FROM reviews INNER JOIN users ON reviews.user_id = users.id WHERE reviews.id = :id");
            $stmt->execute(['id' => $reviewId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? self::fromArray($row) : null;
        } catch (\PDOException $e) {
            error_log("Error fetching review: " . $e->getMessage());
            throw new \Exception("Ошибка при получении отзыва.");
        }
    }

    public static function updateReviewOnce(int $reviewId, int $rating, string $comment): void
    {
        try {
            $stmt = self::getPDO()->prepare("UPDATE reviews SET rating = :rating, comment = :comment, is_edited = TRUE WHERE id = :id AND is_edited = FALSE");
            $stmt->execute(['id' => $reviewId, 'rating' => $rating, 'comment' => $comment]);

            if ($stmt->rowCount() === 0) {
                throw new \Exception("Редактировать отзыв можно только один раз.");
            }
        } catch (\PDOException $e) {
            error_log("Error updating review: " . $e->getMessage());
            throw new \Exception("Ошибка при обновлении отзыва.");
        }
    }

    // Геттеры
    public function getId(): int { return $this->id; }
    public function getUserId(): int { return $this->user_id; }
    public function getProductId(): int { return $this->product_id; }
    public function getRating(): int { return $this->rating; }
    public function getComment(): string { return $this->comment; }
    public function getIsVerifiedPurchase(): bool { return $this->is_verified_purchase; }
    public function isEdited(): bool { return $this->is_edited; }
    public function getUserName(): string { return $this->user_name; }
    public function getCreatedDate(): string { return $this->created_date; }
}