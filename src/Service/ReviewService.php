<?php

namespace Service;
use Model\Review;
use Model\UserProduct;
use Model\Order;
use Service\OrderService;
class ReviewService
{
    public function createReview(ReviewDTO $reviewDTO): void
    {
        $userId = $reviewDTO->getUserId();
        $productId = $reviewDTO->getProductId();
        $rating = $reviewDTO->getRating();
        $comment = $reviewDTO->getComment();

        $isVerifiedPurchase = Order::checkVerifiedPurchase($userId, $productId);
        if (!$isVerifiedPurchase) {
            throw new \Exception("You can only leave a review for a product you've purchased.");
        }
        Review::createReview($userId, $productId, $rating, $comment);

    }
    public function isProductInUserOrder(int $productId, int $userId): bool
    {
        return Order::isProductInUserOrder($productId, $userId);
    }


}