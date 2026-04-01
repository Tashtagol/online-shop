<?php

namespace Controller;

use Model\Order;
use Model\Product;
use Model\Review;
use Request\Request;
use Request\ReviewRequest;
use Service\Auth\AuthServiceInterface;
use Service\ReviewService;

class ReviewController
{
    public function __construct(
        private ReviewService $service,
        private AuthServiceInterface $auth
    ) {}

    public function getProductReviews(int $productId, Request $request): void
    {
        $product = Product::getProductById($productId);
        if (!$product) {
            http_response_code(404);
            echo "Товар не найден";
            return;
        }

        $reviews = Review::getReviewsByProductId($productId);
        $averageRating = $this->service->getAverageRating($productId);
        $currentUser = $this->auth->getCurrentUser();

        $userCanReview = false;
        if ($currentUser) {
            $hasPurchased = Order::checkVerifiedPurchase($currentUser->getId(), $productId);
            $hasAlreadyReviewed = !empty(array_filter(
                $reviews, fn(Review $r) => $r->getUserId() === $currentUser->getId()
            ));
            $userCanReview = $hasPurchased && !$hasAlreadyReviewed;
        }

        require __DIR__ . '/../View/ProductReviews.php';
    }

    public function getReviewForm(int $productId, Request $request): void
    {
        $this->auth->checkAuth();
        $product = Product::getProductById($productId);
        $this->renderReviewForm($product);
    }

    public function submitReview(int $productId, ReviewRequest $request): void
    {
        $user = $this->auth->checkAuth();
        $errors = $request->validate();
        $product = Product::getProductById($productId);

        if (!empty($errors)) {
            $this->renderReviewForm($product, $errors, $request->all());
            return;
        }

        try {
            $this->service->createReview(
                $user->getId(), $productId,
                $request->getRating(), $request->getComment()
            );
            header("Location: /product/$productId/reviews/view");
            exit;
        } catch (\Exception $e) {
            $this->renderReviewForm($product, ['common' => $e->getMessage()], $request->all());
        }
    }

    public function editReviewForm(int $reviewId, Request $request): void
    {
        $user = $this->auth->checkAuth();
        $review = Review::getReviewById($reviewId);

        if (!$review || $review->getUserId() !== $user->getId()) {
            header('Location: /catalog');
            exit;
        }

        if ($review->isEdited()) {
            header('Location: /catalog');
            exit;
        }

        $product = Product::getProductById($review->getProductId());
        $this->renderReviewForm($product, [], [
            'id'      => $reviewId,
            'rating'  => $review->getRating(),
            'comment' => $review->getComment(),
        ], true);
    }

    public function updateReview(int $reviewId, ReviewRequest $request): void
    {
        $user = $this->auth->checkAuth();
        $review = Review::getReviewById($reviewId);

        if (!$review || $review->getUserId() !== $user->getId() || $review->isEdited()) {
            header('Location: /catalog');
            exit;
        }

        $errors = $request->validate();
        $product = Product::getProductById($review->getProductId());

        if (!empty($errors)) {
            $this->renderReviewForm($product, $errors, array_merge($request->all(), ['id' => $reviewId]), true);
            return;
        }

        try {
            $this->service->updateReviewOnce($reviewId, $request->getRating(), $request->getComment());
            header("Location: /product/{$review->getProductId()}/reviews/view");
            exit;
        } catch (\Exception $e) {
            $this->renderReviewForm($product, ['common' => $e->getMessage()], array_merge($request->all(), ['id' => $reviewId]), true);
        }
    }

    private function renderReviewForm(
        ?Product $product,
        array $errors = [],
        array $old = [],
        bool $isEdit = false
    ): void {
        require __DIR__ . '/../View/Review.php';
    }
}