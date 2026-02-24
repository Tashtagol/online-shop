<?php
namespace Controller;

use Model\Order;
use Model\UserProduct;
use Model\Product;
use Model\OrderProduct;

class OrderController
{
    private Order $orderModel;
    private UserProduct $userProductModel;
    private Product $productModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->userProductModel = new UserProduct();
        $this->productModel = new Product();
    }

    public function getOrderForm(array $params = [])
    {
        $userId = $this->checkSession();
        $userProducts = $this->userProductModel->getUserIdCart($userId);

        $orderItems = [];
        $total = 0;

        if (!empty($userProducts)) {
            $productIds = array_map(fn($item) => $item->product_id, $userProducts);
            $products = $this->productModel->getProductsByIds($productIds);

            foreach ($userProducts as $item) {
                $pid = $item->product_id;

                if (isset($products[$pid])) {
                    $price = $products[$pid]->getPrice();

                    $orderItems[] = [
                        'product_id' => $pid,
                        'name' => $products[$pid]->getName(),
                        'price' => $price,
                        'amount' => $item->amount,
                        'sum' => $price * $item->amount,
                        'viewurl' => $products[$pid]->getVieUrl()
                    ];

                    $total += $price * $item->amount;
                }
            }
        }

        $errors = $params['errors'] ?? [];
        $old = $params['old'] ?? [];

        require_once './../View/order.php';
    }

    public function handleOrdersForm(array $data)
    {
        $userId = $this->checkSession();

        $errors = $this->validateOrderForm($data);
        if (!empty($errors)) {
            return $this->getOrderForm(['errors' => $errors, 'old' => $data]);
        }

        $userProducts = $this->userProductModel->getUserIdCart($userId);
        if (empty($userProducts)) {
            return $this->getOrderForm(['errors' => ['cart' => 'Корзина пуста'], 'old' => $data]);
        }

        $productIds = array_map(fn($item) => $item->product_id, $userProducts);
        $products = $this->productModel->getProductsByIds($productIds);

        $orderItems = [];
        foreach ($userProducts as $item) {
            $pid = $item->product_id;

            if (isset($products[$pid])) {
                $price = $products[$pid]->getPrice();

                $orderItems[] = [
                    'product_id' => $pid,
                    'amount' => $item->amount,
                    'price' => $price
                ];
            }
        }

        $order = (new Order($userId, $data['name'], $data['email'], $data['address'], $data['phone']))->saveOrder();

        $order->saveOrderProductsBulk($orderItems);
        $this->userProductModel->clearCart($userId);

        header("Location: /order-success?number=" . $order->getOrderNumber());
        exit;
    }

    public function getOrders(): void
    {
        $userId = $this->checkSession();
        $orders = $this->orderModel->getAllByUserId($userId);

        // Загружаем товары для каждого заказа
        foreach ($orders as &$order) {
            $orderProducts = $order->getOrderProducts(); // Получаем товары для заказа

            // Если товары есть, добавляем их в заказ
            if (!empty($orderProducts)) {
                $order->setProducts($orderProducts);
            }
        }

        require_once './../View/orders.php'; // Передаем заказы в вьюшку
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

    private function validateOrderForm(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) $errors['name'] = 'Имя обязательно';
        if (empty($data['phone'])) $errors['phone'] = 'Телефон обязателен';
        if (empty($data['address'])) $errors['address'] = 'Адрес обязателен';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'Некорректный email';

        return $errors;
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