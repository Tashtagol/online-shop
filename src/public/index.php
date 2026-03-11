<?php
require_once __DIR__ . '/../Core/Autoload.php';

use Core\App;
use Core\Autoload;
use Controller\CartController;
use Controller\OrderController;
use Controller\ProductController;
use Controller\UserController;
use Request\OrderRequest;
use Request\ProductRequest;
use Request\RegistrateRequest;
use Request\LoginRequest;
use Service\OrderService;

// Регистрируем автолоадер на папку src
Autoload::register(__DIR__ . '/../');

$loggerService = new \Service\Logger\LoggerFileService();


$app = new App($loggerService );

// маршруты
$app->addRoute('/registration','GET',UserController::class,'getRegistrationForm');
$app->addRoute('/registration','POST',UserController::class,'handleRegistration',RegistrateRequest::class);
$app->addRoute('/login','GET',UserController::class,'getLoginForm');
$app->addRoute('/login','POST',UserController::class,'handleLogin',LoginRequest::class);
$app->addRoute('/catalog','GET',ProductController::class,'getProduct');
$app->addRoute('/add-product','GET',ProductController::class,'getProductForm');
$app->addRoute('/add-product','POST',ProductController::class,'addProduct',ProductRequest::class);
$app->addRoute('/cart','GET',CartController::class,'getCartForm');
$app->addRoute('/order','GET',OrderController::class,'getOrderForm');
$app->addRoute('/order','POST',OrderController::class,'handleOrdersForm',OrderRequest::class);
$app->addRoute('/order-success','GET',OrderController::class,'getSuccessPage');
$app->addRoute('/orders','GET',OrderController::class,'getOrders');

$app->run();