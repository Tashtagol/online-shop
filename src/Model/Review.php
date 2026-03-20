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
    public static function createReview (int $user_id, int $product_id, int $rating, string $comment, bool $is_verified_purchase)
    {
        $stmt = self::getPDO() -> prepare("INSERT INTO reviews (user_id,product_id,rating,comment,is_verified_purchase) VALUES (:user_id,:product_id,:rating,:comment,:is_verified_purchase)");
        $stmt->execute(['user_id' => $user_id, 'product_id' => $product_id, 'rating' => $rating, 'comment' => $comment, 'is_verified_purchase' => $is_verified_purchase]);
    }

    public static function getReviewsByProductId(int $productId): array
    {
        $stmt = self::getPDO()->prepare("SELECT reviews.*, users.name AS user_name FROM reviews INNER JOIN users ON reviews.user_id = users.id WHERE product_id = :product_id ORDER BY id DESC");
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }




}