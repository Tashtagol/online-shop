<?php
require_once __DIR__ . '/../Core/Autoload.php';

use Core\App;
use Core\Autoload;
use Controller\CartController;
use Controller\OrderController;
use Controller\ProductController;
use Controller\UserController;
use Core\Container;
use Request\OrderRequest;
use Request\ProductRequest;
use Request\RegistrateRequest;
use Request\LoginRequest;
use Service\Auth\AuthSessionService;
use Service\CartService;
use Service\OrderService;
use Service\ProductService;

// Регистрируем автолоадер на папку src
Autoload::register(__DIR__ . '/../');

$loggerService = new \Service\Logger\LoggerFileService();
$container = new \Core\Container();
$container->set(CartController::class,function (Container $container) {
    $authService = $container->get(\Service\Auth\AuthServiceInterface::class);
    $cartService = new CartService();

    return new CartController($cartService, $authService);
});
$container->set(OrderController::class,function (Container $container) {
    $authService = $container->get(\Service\Auth\AuthServiceInterface::class);
    $orderService = new OrderService();

    return new OrderController($orderService,$authService);
});
$container->set(ProductController::class,function (Container $container) {
    $authService = $container->get(\Service\Auth\AuthServiceInterface::class);
    $productService = new ProductService();
    $cartService = new CartService();
    return new ProductController ($productService,$authService,$cartService);
});
$container->set(\Service\Auth\AuthServiceInterface::class,function () {
   return new AuthSessionService();
});
$container->set(UserController::class,function (Container $container) {
    $authService = $container->get(\Service\Auth\AuthServiceInterface::class);
    return new UserController ($authService);
});



$app = new App($loggerService,$container);

// маршруты
$app->addRoute('/registration','GET',UserController::class,'getRegistrationForm');
$app->addRoute('/registration','POST',UserController::class,'handleRegistration',RegistrateRequest::class);
$app->addRoute('/login','GET',UserController::class,'getLoginForm');
$app->addRoute('/login','POST',UserController::class,'handleLogin',LoginRequest::class);
$app->addRoute('/catalog','GET',CartController::class,'getCatalogForm');
$app->addRoute('/add-product','GET',ProductController::class,'getProductForm');
$app->addRoute('/add-product','POST',ProductController::class,'addProduct',ProductRequest::class);
$app->addRoute('/cart','GET',CartController::class,'getCartForm');
$app->addRoute('/cart/clear','POST',CartController::class,'clearCart');
$app->addRoute('/cart', 'POST', CartController::class, 'addOrUpdate');
$app->addRoute('/order','GET',OrderController::class,'getOrderForm');
$app->addRoute('/order','POST',OrderController::class,'handleOrdersForm',OrderRequest::class);
$app->addRoute('/order-success','GET',OrderController::class,'getSuccessPage');
$app->addRoute('/orders','GET',OrderController::class,'getOrders');

$app->run();