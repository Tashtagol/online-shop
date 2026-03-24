<?php

namespace Controller;

use Service\ReviewService;
use Service\Auth\AuthSessionService;
use Service\OrderService;
use Request\ReviewRequest;
use DTO\ReviewDTO;

class ReviewController
{
    private ReviewService $reviewService;
    private AuthSessionService $authService;
    private OrderService $orderService;

    public function __construct(
        ReviewService $reviewService,
        AuthSessionService $authService,
        OrderService $orderService
    ) {
        $this->reviewService = $reviewService;
        $this->authService = $authService;
        $this->orderService = $orderService;
    }

    // GET: показать форму
    public function getReviewForm(int $productId, $data = [])
    {
        $user = $this->checkAuth();

        $product = $this->reviewService->getProductById($productId);
        if (!$product) {
            echo "Продукт не найден";
            exit;
        }

        // Проверка наличия старых данных
        if (isset($data['old'])) {
            $old = $data['old'];
        } else {
            $old = ['rating' => null, 'comment' => '']; // Убедитесь, что рейтинг не будет равен 0 по умолчанию
        }

        // Отправляем данные в шаблон
        require_once './../View/Review.php';
    }

    // POST: обработка формы
    public function submitReview(int $productId, ReviewRequest $request)
    {
        $user = $this->checkAuth();

        $data = $request->getData();
        $errors = $request->validate();

        if (!empty($errors)) {
            return $this->getReviewForm($productId, ['errors' => $errors, 'old' => $data]);
        }

        // Проверка, что пользователь купил товар
        $isVerified = \Model\Order::checkVerifiedPurchase($user->getId(), $productId);
        if (!$isVerified) {
            return $this->getReviewForm($productId, [
                'errors' => ['Вы можете оставить отзыв только на купленные товары.'],
                'old' => $data
            ]);
        }

        // Проверка, что отзыв ещё не оставлен
        $existingReviews = \Model\Review::getReviewsByProductId($productId);
        foreach ($existingReviews as $review) {
            if ($review['user_id'] === $user->getId()) {
                return $this->getReviewForm($productId, [
                    'errors' => ['Вы уже оставили отзыв на этот продукт.'],
                    'old' => $data
                ]);
            }
        }

        // Создаём DTO только после успешной валидации
        $reviewDTO = new ReviewDTO(
            $user->getId(),
            $productId,
            (int)$request->getRating(),
            (string)$request->getComment(),
            true
        );

        // Создаём отзыв через сервис
        try {
            $this->reviewService->createReview($reviewDTO);
            return $this->getReviewForm($productId, ['success' => true]);
        } catch (\Exception $e) {
            return $this->getReviewForm($productId, [
                'errors' => ['Ошибка при добавлении отзыва. Попробуйте позже.'],
                'old' => $data
            ]);
        }
    }
    public function getProductReviews(int $productId) {
        $product = $this->reviewService->getProductById($productId);
        if (!$product) { echo "Продукт не найден."; exit; }
        $reviews = $this->reviewService->getReviewsByProductId($productId);
        $averageRating = $this->reviewService->getAverageRating($productId);
        require_once './../View/ProductReviews.php';
    }

    private function checkAuth()
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            header("Location: /login");
            exit;
        }
        return $user;
    }
}