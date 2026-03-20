<?php

namespace Controller;

use Service\Auth\AuthSessionService;
use Service\OrderService;
use Service\ReviewService;
use Request\ReviewRequest;
use Model\ReviewDTO;

class ReviewController
{
    private $reviewService;
    private $authService;
    private $orderService;

    public function __construct(ReviewService $reviewService, AuthSessionService $authService, OrderService $orderService)
    {
        $this->reviewService = $reviewService;
        $this->authService = $authService;
        $this->orderService = $orderService;
    }

    // Форма для оставления отзыва
    public function getReviewForm(int $productId)
    {
        $user = $this->checkAuth(); // Проверяем авторизацию

        // Проверяем, что продукт есть в заказе пользователя
        if (!$this->reviewService->isProductInUserOrder($productId, $user->getId())) {
            echo "Вы не можете оставить отзыв на этот продукт, так как он не был заказан вами.";
            exit;
        }

        // Загружаем продукт, чтобы отобразить его название и другие данные в форме
        $product = $this->orderService->getProductById($productId);

        // Передаем данные в представление
        require_once './../View/Review.php';
    }

    // Обработка отзыва
    public function submitReview(int $productId, ReviewRequest $request)
    {
        $user = $this->checkAuth(); // Проверяем авторизацию

        // Получаем данные из запроса
        $data = $request->getData();
        $errors = $request->validate();

        // Если есть ошибки валидации, показываем их в форме
        if (!empty($errors)) {
            return $this->getReviewForm($productId, ['errors' => $errors, 'old' => $data]);
        }

        // Создаем DTO для отзыва
        $reviewDTO = new ReviewDTO(
            $user->getId(),
            $request->getProductId(),
            $request->getRating(),
            $request->getComment()
        );

        try {
            // Сохраняем отзыв
            $this->reviewService->createReview($reviewDTO);

            // Перенаправляем на страницу с отзывами
            header("Location: /product/{$productId}/reviews");
            exit;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    // Проверка авторизации
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