<?php

namespace Controller;

use Service\ReviewService;
use Service\Auth\AuthSessionService;
use Request\ReviewRequest;

class ReviewController
{
    private ReviewService $reviewService;
    private AuthSessionService $authService;

    public function __construct(ReviewService $reviewService, AuthSessionService $authService)
    {
        $this->reviewService = $reviewService;
        $this->authService = $authService;
    }

    // ===== GET FORM =====
    public function getReviewForm(int $productId)
    {
        $this->authService->checkAuth();

        $product = $this->reviewService->getProductById($productId);

        if (!$product) {
            echo "Продукт не найден";
            exit;
        }

        $success = isset($_GET['success']) && $_GET['success'] == 1;

        $old = [];
        $errors = [];

        require_once __DIR__ . '/../View/Review.php';
    }

    // ===== POST REVIEW =====
    public function submitReview(int $productId, ReviewRequest $request)
    {
        $user = $this->authService->checkAuth();

        if (!$request->isPost() || empty($_POST)) {
            return $this->getReviewForm($productId);
        }

        $errors = $request->validate();

        if (!empty($errors)) {
            $product = $this->reviewService->getProductById($productId);

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

            header("Location: /product/$productId/reviews?success=1");
            exit;

        } catch (\Exception $e) {

            $product = $this->reviewService->getProductById($productId);

            $errors = [$e->getMessage()];
            $old = $request->all();
            $success = false;

            require_once __DIR__ . '/../View/Review.php';
        }
    }

    // ===== REVIEWS LIST =====
    public function getProductReviews(int $productId)
    {
        $product = $this->reviewService->getProductById($productId);

        if (!$product) {
            echo "Продукт не найден.";
            exit;
        }

        $reviews = $this->reviewService->getReviewsByProductId($productId);
        $averageRating = $this->reviewService->getAverageRating($productId);

        require_once './../View/ProductReviews.php';
    }

}