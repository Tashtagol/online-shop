<?php
namespace Controller;

use Service\Auth\AuthSessionService;
use Service\Auth\AuthServiceInterface;
use Service\CartService;


class CartController
{
    private CartService $cartService;
    private AuthServiceInterface $authService;

    public function __construct(CartService $cartService,AuthServiceInterface $authService)
    {
        $this->cartService = $cartService;
        $this->authService = $authService;
    }

    public function getCartForm()
    {
        $user = $this->checkAuth();
        $products = $this->cartService->getCart($user->getId());
        $total = $this->cartService->getTotal($products);
        require_once './../View/cart.php';
    }
    public function addOrUpdate()
    {
        $user = $this->checkAuth();
        $data = json_decode(file_get_contents('php://input'), true);
        $productId = intval($data['product_id'] ?? 0);
        $amount = intval($data['amount'] ?? 1);

        // Используем update, чтобы установить точное количество
        $this->cartService->update($user->getId(), $productId, $amount);

        $products = $this->cartService->getCart($user->getId());
        $total = $this->cartService->getTotal($products);

        $subtotal = 0;
        foreach ($products as $p) {
            if ($p['id'] === $productId) {
                $subtotal = $p['price'] * $p['amount'];
                break;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success'=>true,'total'=>$total,'subtotal'=>$subtotal]);
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
    ы
}