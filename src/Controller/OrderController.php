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
    private Order $orderModel;

    private OrderService  $orderService;
    private AuthServiceInterface $authService;

    public function __construct()
    {

        $this->orderService = new OrderService();
        $this->orderModel = new Order();
        $this->authService = new AuthSessionService();

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
        $orderDTO = new CreateOrderDTO($userId, $data['name'], $data['email'], $data['address'], $data['phone']);
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
        $orders = $this->orderModel->getAllByUserId($userId);

        // Загружаем товары для каждого заказа
        foreach ($orders as $order) {
            $orderProducts = $order->getOrderProducts(); // Получаем товары для заказа

            // Если товары есть, добавляем их в заказ
            if (!empty($orderProducts)) {
                $order->setProducts($orderProducts);
            }
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

        $order = $this->orderModel->getByNumber($orderNumber);

        if (!$order || !$order->orderBelongsToUser($order->getId(), $userId)) {
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