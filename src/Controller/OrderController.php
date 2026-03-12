<?php
namespace Controller;
use DTO\CreateOrderDTO;
use Model\Order;
use Model\Product;
use Model\UserProduct;
use Request\OrderRequest;
use Service\Auth\AuthServiceInterface;
use Service\Auth\AuthSessionService;
use Service\OrderService;

class OrderController
{
    private OrderService  $orderService;
    private AuthServiceInterface $authService;

    public function __construct(OrderService  $orderService,AuthServiceInterface $authService)
    {
        $this->orderService = $orderService;
        $this->authService = $authService;
    }

    public function getOrderForm(array $params = [])
    {
        $user = $this->checkAuth();
        $cartData = $this->orderService->getCartData($user->getId());
        $orderItems = $cartData['items'];
        $total = $cartData['total'];

        $errors = $params['errors'] ?? [];
        $old = $params['old'] ?? [];

        require_once './../View/order.php';
    }

    public function handleOrdersForm(OrderRequest  $request)
    {
        $user = $this->checkAuth();
        $userId = $user->getId();

        $data = $request->getData();
        $errors = $request->validate();

        if (!empty($errors)) {
            return $this->getOrderForm(['errors' => $errors, 'old' => $data]);
        }
        $orderDTO = new CreateOrderDTO(
            $userId,
            $data['name'],
            $data['email'],
            $data['address'],
            $data['phone'],
            $data['payment'] ?? ''
        );
        $order = $this->orderService->create($orderDTO);

        if (!$order) { // если корзина пуста
            return $this->getOrderForm(['errors' => ['cart' => 'Корзина пуста'], 'old' => $data]);
        }

        header("Location: /order-success?number=" . $order->getOrderNumber());
        exit;
    }

    public function getOrders(): void
    {
        $user = $this->checkAuth();
        $userId = $user->getId();
        $orders = Order::getAllByUserId($userId);

        // Загружаем товары для каждого заказа
        foreach ($orders as $order) {
            $orderProducts = $order->getOrderProducts();
            $order->setProducts($orderProducts); // Получаем товары для заказа

        }
        require_once './../View/orders.php';
    }

    public function getSuccessPage()
    {
        $user = $this->checkAuth();
        $userId = $user->getId();

        $orderNumber = $_GET['number'] ?? null;

        if (!$orderNumber) {
            header('Location: /catalog');
            exit;
        }

        $order = Order::getByNumber($orderNumber);

        if (!$order || !$order->orderBelongsToUser($userId)) {
            header('Location: /catalog');
            exit;
        }

        require_once './../View/orderSuccess.php';
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