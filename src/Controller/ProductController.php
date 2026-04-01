<?php
namespace Controller;

use Service\Auth\AuthServiceInterface;
use Model\Product;
use Service\CartService;

class ProductController {
    public function __construct(private AuthServiceInterface $auth, private CartService $cart)
    {}

    public function getCatalog() {
        $userId = $this->auth->checkAuth()->getId();
        $products = Product::getAllProducts(); // Прямой вызов модели
        $cartItems = $this->cart->getCart($userId);
        $cartCount = array_sum(array_map(fn($i) => $i->getAmount(), $cartItems));

        require_once './../View/Catalog.php';
    }
}