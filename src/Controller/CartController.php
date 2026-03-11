<?php
namespace Controller;

use Service\Auth\AuthSessionService;
use Service\Auth\AuthServiceInterface;
use Service\CartService;


class CartController
{
    private CartService $cartService;
    private AuthServiceInterface $authService;

    public function __construct()
    {
        $this->cartService = new CartService();
        $this->authService = new AuthSessionService();
    }

    public function getCartForm()
    {
        $user = $this->checkAuth();
        if (!$user) {
            header("Location: ./login");
            exit;
        }
        $products = $this->cartService->getCart($user->getId());
        $total = $this->cartService->getTotal($products);

        require_once './../View/cart.php';
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