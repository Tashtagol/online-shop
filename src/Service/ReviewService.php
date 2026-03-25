<?php

namespace Service;

use Model\Review;
use Model\Order;

class ReviewService
{
    // ===== ORCHESTRATOR =====
    public function createReview(int $userId, int $productId, int $rating, string $comment): void
    {
        $this->ensureVerifiedPurchase($userId, $productId);
        $this->ensureNotDuplicate($userId, $productId);
        $this->ensureValidRating($rating);

        Review::createReview($userId, $productId, $rating, $comment, true);
    }

    // ===== 1. Проверка покупки =====
    public function ensureVerifiedPurchase(int $userId, int $productId): void
    {
        if (!Order::checkVerifiedPurchase($userId, $productId)) {
            throw new \Exception("Вы можете оставить отзыв только на купленный товар.");
        }
    }

    // ===== 2. Проверка дубля =====
    public function ensureNotDuplicate(int $userId, int $productId): void
    {
        $reviews = Review::getReviewsByProductId($productId);

        foreach ($reviews as $review) {
            if ($review['user_id'] === $userId) {
                throw new \Exception("Вы уже оставили отзыв");
            }
        }
    }

    // ===== 3. Проверка рейтинга =====
    public function ensureValidRating(int $rating): void
    {
        if ($rating < 1 || $rating > 5) {
            throw new \Exception("Рейтинг должен быть от 1 до 5.");
        }
    }


    public function getReviewsByProductId(int $productId): array
    {
        return Review::getReviewsByProductId($productId);
    }

    public function getProductById(int $productId): ?\Model\Product
    {
        return \Model\Product::getProductById($productId);
    }

    public function getAverageRating(int $productId): float
    {
        $reviews = $this->getReviewsByProductId($productId);

        if (empty($reviews)) {
            return 0;
        }

        $sum = 0;

        foreach ($reviews as $review) {
            $sum += $review['rating'];
        }

        return round($sum / count($reviews), 2);
    }

    public function checkVerifiedPurchase(int $userId, int $productId): bool
    {
        return Order::checkVerifiedPurchase($userId, $productId);
    }
}