<?php

namespace Service;

use Model\Order;
use Model\Review;

class ReviewService
{
    public function createReview(int $userId, int $productId, int $rating, string $comment): void
    {
        $this->ensureVerifiedPurchase($userId, $productId);
        $this->ensureNotDuplicate($userId, $productId);
        $this->ensureValidRating($rating);
        Review::createReview($userId, $productId, $rating, $comment, true);
    }

    public function updateReviewOnce(int $reviewId, int $rating, string $comment): void
    {
        $this->ensureValidRating($rating);
        Review::updateReviewOnce($reviewId, $rating, $comment);
    }

    public function getAverageRating(int $productId): float
    {
        $reviews = Review::getReviewsByProductId($productId);
        if (empty($reviews)) return 0.0;
        $sum = array_sum(array_map(fn(Review $r) => $r->getRating(), $reviews));
        return round($sum / count($reviews), 2);
    }

    private function ensureVerifiedPurchase(int $userId, int $productId): void
    {
        if (!Order::checkVerifiedPurchase($userId, $productId)) {
            throw new \Exception("Отзыв доступен только после покупки.");
        }
    }

    private function ensureNotDuplicate(int $userId, int $productId): void
    {
        foreach (Review::getReviewsByProductId($productId) as $review) {
            if ($review->getUserId() === $userId) {
                throw new \Exception("Вы уже оставили отзыв к этому товару.");
            }
        }
    }

    private function ensureValidRating(int $rating): void
    {
        if ($rating < 1 || $rating > 5) {
            throw new \Exception("Некорректный рейтинг (1-5).");
        }
    }
}