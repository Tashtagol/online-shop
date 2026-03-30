<?php
namespace Controller;

use Service\Auth\AuthServiceInterface;
use Service\CartService;

class CartController
{
    private CartService $cartService;
    private AuthServiceInterface $authService;

    public function __construct(CartService $cartService, AuthServiceInterface $authService)
    {
        $this->cartService = $cartService;
        $this->authService = $authService;
    }

    // Страница корзины
    public function getCart()
    {
        $userId = $this->checkAuth()->getId();
        $orderProducts = $this->cartService->getCart($userId);
        $total = $this->cartService->getTotal($orderProducts);

        require_once './../View/cart.php';
    }

    // Добавление/обновление товара
    public function addOrUpdateItem()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $this->checkAuth()->getId();
        $result = $this->cartService->addOrUpdateProductInCart($userId, $data);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'total' => $result['total'],
            'subtotal' => $result['subtotal'],
            'count' => $result['count'],
        ]);
        exit;
    }

    public function clearFullCart()
    {
        $userId = $this->checkAuth()->getId();
        $this->cartService->clearCart($userId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'count' => 0,
            'items' => [],
            'total' => 0
        ]);
        exit;
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