<?php

namespace Controller;

use DTO\CreateOrderDTO;
use Model\Order;
use Request\OrderRequest;
use Request\Request;
use Service\Auth\AuthServiceInterface;
use Service\CartService;
use Service\OrderService;

class OrderController
{
    public function __construct(
        private OrderService $orderService,
        private CartService $cartService,
        private AuthServiceInterface $auth
    ) {}

    public function getOrderForm(Request $request): void
    {
        $this->renderOrderForm();
    }

    public function handleOrdersForm(OrderRequest $request): void
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            $this->renderOrderForm($errors, [
                'name'    => $request->getName(),
                'phone'   => $request->getPhone(),
                'address' => $request->getAddress(),
                'email'   => $request->getEmail(),
                'payment' => $request->getPayment(),
            ]);
            return;
        }

        $userId = $this->currentUserId();
        $dto = new CreateOrderDTO(
            $userId,
            $request->getName(),
            $request->getEmail(),
            $request->getAddress(),
            $request->getPhone(),
            $request->getPayment()
        );

        $order = $this->orderService->create($dto);
        if (!$order) {
            $this->renderOrderForm(['cart' => 'Корзина пуста']);
            return;
        }

        header("Location: /orderSuccess?number=" . $order->getOrderNumber());
        exit;
    }

    public function listOrders(Request $request): void
    {
        $userId = $this->currentUserId();
        $orders = Order::getAllByUserId($userId);
        require __DIR__ . '/../View/orders.php';
    }

    public function getSuccessPage(OrderRequest $request): void
    {
        $userId = $this->currentUserId();
        $orderNumber = $request->getNumber();

        if (!$orderNumber) {
            header('Location: /catalog');
            exit;
        }

        $order = Order::getByNumber($orderNumber);
        if (!$order || !$order->orderBelongsToUser($userId)) {
            header('Location: /catalog');
            exit;
        }

        require __DIR__ . '/../View/orderSuccess.php';
    }

    private function renderOrderForm(array $errors = [], array $old = []): void
    {
        $userId = $this->currentUserId();
        $orderItems = $this->cartService->getCart($userId);
        $total = $this->cartService->getTotal($orderItems);
        require __DIR__ . '/../View/order.php';
    }

    private function currentUserId(): int
    {
        return $this->auth->checkAuth()->getId();
    }
}