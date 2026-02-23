<?php

require_once './../Core/Autoload.php';

Use Core\App;
use Core\Autoload;
use Controller\CartController;
use Controller\OrderController;
use Controller\ProductController;
use Controller\UserController;

Autoload::register(__DIR__ . '/../');

$app = new App();

$app->addRoute('/registration','GET',UserController::class,'getRegistrationForm');
$app->addRoute('/registration','POST',UserController::class,'handleRegistration');
$app->addRoute('/login','GET',UserController::class,'getLoginForm');
$app->addRoute('/login','POST',UserController::class,'handleLogin');
$app->addRoute('/catalog','GET',ProductController::class,'getProduct');
$app->addRoute('/add-product','GET',ProductController::class,'getProductForm');
$app->addRoute('/add-product','POST',ProductController::class,'addProduct');
$app->addRoute('/cart','GET',CartController::class,'getCartForm');
$app->addRoute('/order','GET',OrderController::class,'getOrderForm');
$app->addRoute('/order','POST',OrderController::class,'handleOrdersForm');
$app->addRoute('/order-success','GET',OrderController::class,'getSuccessPage');
$app->addRoute('/orders','GET',OrderController::class,'getOrders');

$app->run();


