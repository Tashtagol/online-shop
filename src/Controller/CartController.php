<?php
namespace Controller;

use Service\CartService;


class CartController
{
    private CartService $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();

    }

    public function getCartForm()
    {
        $userId = $this->checkSession();
        $products = $this->cartService->getCart($userId);
        $total = $this->cartService->getTotal($products);

        require_once './../View/cart.php';
    }



    private function checkSession(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ./login');
            exit;
        }

        return (int)$_SESSION['user_id'];
    }
}