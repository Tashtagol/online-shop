<?php
namespace Controller;
use DTO\CreateOrderDTO;
use Model\Order;
use Model\Product;
use Model\UserProduct;
use Request\OrderRequest;
use Service\OrderService;

class OrderController
{
    private Order $orderModel;
    private UserProduct $userProductModel;
    private Product $productModel;
    private OrderService  $orderService;

    public function __construct()
    {

        $this->orderService = new OrderService();
        $this->orderModel = new Order();
        $this->userProductModel = new UserProduct();
        $this->productModel = new Product();
    }

    public function getOrderForm(array $params = [])
    {
        $userId = $this->checkSession();
        $cartData = $this->orderService->getCartData($userId);
        $orderItems = $cartData['items'];
        $total = $cartData['total'];

        $errors = $params['errors'] ?? [];
        $old = $params['old'] ?? [];

        require_once './../View/order.php';
    }

    public function handleOrdersForm(OrderRequest  $request)
    {
        $userId = $this->checkSession();

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
        $userId = $this->checkSession();
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
        $userId = $this->checkSession();
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

    private function checkSession(): int
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: ./login');
            exit;
        }

        return (int)$_SESSION['user_id'];
    }
}