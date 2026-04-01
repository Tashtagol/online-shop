<?php

namespace Controller;

use Model\UserProduct;
use Request\ProductRequest;
use Request\Request;
use Service\Auth\AuthServiceInterface;
use Service\CartService;

class CartController
{
    public function __construct(
        private CartService $cartService,
        private AuthServiceInterface $auth
    ) {}

    public function getCart(Request $request): void
    {
        $userId        = $this->auth->checkAuth()->getId();
        $orderProducts = $this->cartService->getCart($userId);
        $total         = $this->cartService->getTotal($orderProducts);
        require __DIR__ . '/../View/cart.php';
    }

    public function addOrUpdateItem(ProductRequest $request): void
    {
        $userId    = $this->auth->checkAuth()->getId();
        $productId = $request->getProductId();

        $this->cartService->addOrUpdateProductInCart($userId, $productId, $request->getAmount(), $request->getSource());

        $items = $this->cartService->getCart($userId);

        header('Content-Type: application/json');
        echo json_encode([
            'success'  => true,
            'total'    => $this->cartService->getTotal($items),
            'count'    => $this->cartService->getCount($items),
            'subtotal' => $this->cartService->getSubtotal($items, $productId),
        ]);
        exit;
    }

    public function clearFullCart() {
        $userId = $this->auth->checkAuth()->getId();
        UserProduct::clearCart($userId);
        echo json_encode(['success' => true, 'count' => 0, 'total' => 0]);
        exit;
    }
}