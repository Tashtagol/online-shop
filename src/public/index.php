<?php
require_once __DIR__ . '/../Core/Autoload.php';

use Core\App;
use Core\Autoload;
use Controller\CartController;
use Controller\OrderController;
use Controller\ProductController;
use Controller\UserController;
use Controller\ReviewController;
use Core\Container;
use Request\OrderRequest;
use Request\ReviewRequest;
use Request\ProductRequest;
use Request\RegistrateRequest;
use Request\LoginRequest;
use Service\Auth\AuthSessionService;
use Service\CartService;
use Service\OrderService;
use Service\ProductService;
use Service\ReviewService;

// Регистрируем автолоадер на папку src
Autoload::register(__DIR__ . '/../');

// Создаем экземпляр логгера
$loggerService = new \Service\Logger\LoggerFileService();

// Создаем контейнер
$container = new \Core\Container();

// Регистрация CartService в контейнере
$container->set(CartService::class, function () {
    return new CartService();  // Создаем CartService
});

// Регистрация OrderService в контейнере с передачей зависимостей
$container->set(OrderService::class, function (Container $container) {
    $cartService = $container->get(CartService::class); // Получаем CartService из контейнера
    return new OrderService($cartService); // Передаем CartService в OrderService
});

// Регистрация остальных сервисов
$container->set(\Service\Auth\AuthServiceInterface::class, function () {
    return new AuthSessionService();
});

// Регистрация контроллеров с использованием контейнера для зависимостей
$container->set(CartController::class, function (Container $container) {
    $authService = $container->get(\Service\Auth\AuthServiceInterface::class);
    $cartService = $container->get(CartService::class);  // Получаем CartService из контейнера
    return new CartController($cartService, $authService);
});

$container->set(OrderController::class, function (Container $container) {
    $authService = $container->get(\Service\Auth\AuthServiceInterface::class);
    $orderService = $container->get(OrderService::class);  // Получаем OrderService из контейнера
    return new OrderController($orderService, $authService);
});

$container->set(ProductController::class, function (Container $container) {
    $authService = $container->get(\Service\Auth\AuthServiceInterface::class);
    $cartService = $container->get(CartService::class);  // Получаем CartService из контейнера
    return new ProductController($authService, $cartService);
});

$container->set(UserController::class, function (Container $container) {
    $authService = $container->get(\Service\Auth\AuthServiceInterface::class);
    return new UserController($authService);
});

$container->set(ReviewController::class, function (Container $container) {
    $authService = $container->get(\Service\Auth\AuthServiceInterface::class);
    $reviewService = $container->get(ReviewService::class);  // Получаем ReviewService из контейнера
    $orderService = $container->get(OrderService::class);  // Получаем OrderService из контейнера
    return new ReviewController($reviewService, $authService, $orderService);
});

$app = new App($loggerService, $container);

// Определяем маршруты
$app->addRoute('/registration', 'GET', UserController::class, 'getRegistrationForm');
$app->addRoute('/registration', 'POST', UserController::class, 'handleRegistration', RegistrateRequest::class);
$app->addRoute('/login', 'GET', UserController::class, 'getLoginForm');
$app->addRoute('/login', 'POST', UserController::class, 'handleLogin', LoginRequest::class);
$app->addRoute('/catalog', 'GET', ProductController::class, 'getCatalog');
$app->addRoute('/add-product', 'GET', ProductController::class, 'getProductForm');
$app->addRoute('/add-product', 'POST', ProductController::class, 'addProduct', ProductRequest::class);
$app->addRoute('/cart', 'GET', CartController::class, 'getCart');
$app->addRoute('/cart/clear', 'POST', CartController::class, 'clearCart');
$app->addRoute('/cart', 'POST', CartController::class, 'addOrUpdateItem');
$app->addRoute('/order', 'GET', OrderController::class, 'getOrderForm');
$app->addRoute('/order', 'POST', OrderController::class, 'handleOrdersForm', OrderRequest::class);
$app->addRoute('/order-success', 'GET', OrderController::class, 'getSuccessPage');
$app->addRoute('/orders', 'GET', OrderController::class, 'listOrders');
$app->addRoute('/product/{productId}/reviews', 'GET', ReviewController::class, 'getReviewForm');
$app->addRoute('/product/{productId}/reviews', 'POST', ReviewController::class, 'submitReview', ReviewRequest::class);
$app->addRoute('/product/{id}/reviews/view', 'GET', ReviewController::class, 'getProductReviews'); // Страница с отзывами


// Запускаем приложение
$app->run();