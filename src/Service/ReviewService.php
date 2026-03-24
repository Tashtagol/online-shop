<?php

namespace Service;

use Model\Review;
use Model\UserProduct;
use Model\Order;
use DTO\ReviewDTO;

class ReviewService
{
    public function createReview(ReviewDTO $reviewDTO): void
    {
        $userId = $reviewDTO->getUserId();
        $productId = $reviewDTO->getProductId();
        $rating = $reviewDTO->getRating();
        $comment = $reviewDTO->getComment();

        // Проверка на наличие заказа на этот товар
        $isVerifiedPurchase = Order::checkVerifiedPurchase($userId, $productId);
        if (!$isVerifiedPurchase) {
            throw new \Exception("Вы можете оставить отзыв только на товары, которые вы заказали.");
        }

        // Создание отзыва
        Review::createReview($userId, $productId, $rating, $comment, $isVerifiedPurchase);
    }

    public function isProductInUserOrder(int $productId, int $userId): bool
    {
        return Order::isProductInUserOrder($productId, $userId);
    }
    public function getReviewsByProductId(int $productId): array
    {
        return Review::getReviewsByProductId($productId); // Возвращаем отзывы для продукта
    }

    // Получаем продукт по его ID
    public function getProductById(int $productId): ?\Model\Product
    {
        return \Model\Product::getProductById($productId); // Возвращаем продукт
    }

    // Получаем среднюю оценку для продукта
    public function getAverageRating(int $productId): float
    {
        $reviews = $this->getReviewsByProductId($productId);

        // Если отзывов нет, возвращаем 0
        if (empty($reviews)) {
            return 0;
        }

        $totalRating = 0;
        $count = count($reviews);  // Количество отзывов

        // Суммируем все оценки
        foreach ($reviews as $review) {
            $totalRating += $review['rating'];
        }

        // Возвращаем среднее значение, округленное до 2 знаков после запятой
        return round($totalRating / $count, 2);
    }
}