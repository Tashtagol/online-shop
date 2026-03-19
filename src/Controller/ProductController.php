<?php
namespace Controller;

use Service\Auth\AuthServiceInterface;
use Model\Product;
use Service\CartService;

class ProductController
{
    private AuthServiceInterface $authService;
    private CartService $cartService;

    public function __construct(AuthServiceInterface $authService, CartService $cartService)
    {
        $this->authService = $authService;
        $this->cartService = $cartService;
    }

    // Страница каталога
    public function getCatalog()
    {
        $userId = $this->checkAuth()->getId();

        $products = Product::getAllProducts();

        // Получаем корзину пользователя для отображения количества товаров
        $cartProducts = $this->cartService->getCart($userId);
        $cartCount = array_sum(array_map(fn($item) => $item->getAmount(), $cartProducts));

        require_once './../View/Catalog.php';
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