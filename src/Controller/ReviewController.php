<?php

namespace Controller;

use Service\ReviewService;
use Service\Auth\AuthServiceInterface;
use Request\ReviewRequest;

class ReviewController
{
    private ReviewService $reviewService;
    private AuthServiceInterface $authService;

    public function __construct(
        ReviewService $reviewService,
        AuthServiceInterface $authService
    ) {
        $this->reviewService = $reviewService;
        $this->authService = $authService;
    }

    public function getReviewForm(int $productId)
    {
        $currentUser = $this->authService->checkAuth();

        $product = $this->reviewService->getProductById($productId);

        if (!$product) {
            echo "Продукт не найден";
            exit;
        }

        // Проверяем, может ли пользователь оставить отзыв
        $userCanReview = false;
        if ($currentUser) {
            $hasPurchased = $this->reviewService->checkVerifiedPurchase($currentUser->getId(), $productId);
            $reviews = $this->reviewService->getReviewsByProductId($productId);
            $hasReview = false;
            foreach ($reviews as $r) {
                if ($r['user_id'] === $currentUser->getId()) {
                    $hasReview = true;
                    break;
                }
            }
            $userCanReview = $hasPurchased && !$hasReview;
        }

        $isEdit = false;
        $old = [];
        $errors = [];
        $success = false;

        require_once __DIR__ . '/../View/Review.php';
    }

    public function submitReview(int $productId, ReviewRequest $request)
    {
        $user = $this->authService->checkAuth();

        if (!$request->isPost()) {
            return $this->getReviewForm($productId);
        }

        $errors = $request->validate();
        if (!$request->isPost() || empty($_POST)) { return $this->getReviewForm($productId); }

        if (!empty($errors)) {
            $product = $this->reviewService->getProductById($productId);
            $isEdit = false;
            $old = $request->all();
            $success = false;

            require_once __DIR__ . '/../View/Review.php';
            return;
        }

        try {
            $this->reviewService->createReview(
                $user->getId(),
                $productId,
                $request->getRating(),
                $request->getComment()
            );

            header("Location: /product/$productId/reviews/view");
            exit;

        } catch (\Exception $e) {

            $product = $this->reviewService->getProductById($productId);
            $errors = [$e->getMessage()];
            $old = $request->all();
            $isEdit = false;
            $success = false;

            require_once __DIR__ . '/../View/Review.php';
        }
    }

    public function getProductReviews(int $productId)
    {
        $product = $this->reviewService->getProductById($productId);

        if (!$product) {
            echo "Продукт не найден.";
            exit;
        }

        $reviews = $this->reviewService->getReviewsByProductId($productId);
        $averageRating = $this->reviewService->getAverageRating($productId);
        $currentUser = $this->authService->getCurrentUser();

        // Проверяем, может ли пользователь оставить отзыв
        $userCanReview = false;
        if ($currentUser) {
            $hasPurchased = $this->reviewService->checkVerifiedPurchase($currentUser->getId(), $productId);
            $hasReview = false;
            foreach ($reviews as $r) {
                if ($r['user_id'] === $currentUser->getId()) {
                    $hasReview = true;
                    break;
                }
            }
            $userCanReview = $hasPurchased && !$hasReview;
        }

        require_once __DIR__ . '/../View/ProductReviews.php';
    }


    public function editReviewForm(int $reviewId)
    {
        $user = $this->authService->checkAuth();
        $review = $this->reviewService->getReviewById($reviewId);

        if (!$review || $review['user_id'] !== $user->getId()) {
            echo "Доступ запрещён";
            exit;
        }

        if ($review['is_edited']) {
            echo "Редактировать отзыв можно только один раз.";
            exit;
        }

        $product = $this->reviewService->getProductById($review['product_id']);

        $isEdit = true;
        $old = $review;
        $errors = [];
        $success = false;

        require_once __DIR__ . '/../View/Review.php';
    }

    public function updateReview(int $reviewId, ReviewRequest $request)
    {
        $user = $this->authService->checkAuth();
        $review = $this->reviewService->getReviewById($reviewId);

        if (!$review || $review['user_id'] !== $user->getId()) {
            echo "Доступ запрещён";
            exit;
        }

        if ($review['is_edited']) {
            echo "Редактировать отзыв можно только один раз.";
            exit;
        }

        $errors = $request->validate();

        if (!empty($errors)) {

            $product = $this->reviewService->getProductById($review['product_id']);
            $isEdit = true;
            $old = $request->all();

            require_once __DIR__ . '/../View/Review.php';
            return;
        }

        try {

            $this->reviewService->updateReviewOnce(
                $reviewId,
                $request->getRating(),
                $request->getComment()
            );

            header("Location: /product/{$review['product_id']}/reviews/view");
            exit;

        } catch (\Exception $e) {

            $product = $this->reviewService->getProductById($review['product_id']);
            $errors = [$e->getMessage()];
            $isEdit = true;
            $old = $request->all();

            require_once __DIR__ . '/../View/Review.php';
        }
    }
}