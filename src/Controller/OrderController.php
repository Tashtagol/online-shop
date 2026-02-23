<?php
namespace Controller;

use Model\UserProduct;
use Model\Product;
use Model\User;
use Model\Order;

class OrderController
{
    private Order $orderModel;
    private UserProduct $userProductModel;
    private Product $productModel;
    private User $userModel;

    public function __construct()
    {
        $this->orderModel = new Order();           // для работы с заказами
        $this->userProductModel = new UserProduct();
        $this->productModel = new Product();
        $this->userModel = new User();
    }

    // Форма заказа
    public function getOrderForm(array $params = [])
    {
        $userId = $this->checkSession();
        $userProducts = $this->userProductModel->getUserIdCart($userId);

        $orderItems = [];
        $total = 0;

        if (!empty($userProducts)) {
            $productIds = array_column($userProducts, 'product_id');
            $products = $this->productModel->getProductsByIds($productIds);

            foreach ($userProducts as $item) {
                $pid = $item['product_id'];
                if (isset($products[$pid])) {
                    $price = $products[$pid]->getPrice();
                    $orderItems[] = [
                        'product_id' => $pid,
                        'name' => $products[$pid]->getName(),
                        'price' => $price,
                        'amount' => $item['amount'],
                        'sum' => $price * $item['amount'],
                        'viewurl' => $products[$pid]->getVieUrl() ?? ''
                    ];
                    $total += $price * $item['amount'];
                }
            }
        }

        $errors = $params['errors'] ?? [];
        $old = $params['old'] ?? [];

        require_once './../View/order.php';
    }

    // Обработка формы заказа
    public function handleOrdersForm(array $data)
    {
        $userId = $this->checkSession();

        // Валидация
        $errors = $this->validateOrderForm($data);
        if (!empty($errors)) {
            return $this->getOrderForm(['errors' => $errors, 'old' => $data]);
        }

        // Товары пользователя
        $userProducts = $this->userProductModel->getUserIdCart($userId);
        if (empty($userProducts)) {
            return $this->getOrderForm(['errors' => ['cart' => 'Корзина пуста'], 'old' => $data]);
        }

        // Получаем продукты как объекты
        $productIds = array_column($userProducts, 'product_id');
        $products = $this->productModel->getProductsByIds($productIds);

        $orderItems = [];
        foreach ($userProducts as $item) {
            $pid = $item['product_id'];
            if (isset($products[$pid])) {
                $price = $products[$pid]->getPrice();
                $orderItems[] = [
                    'product_id' => $pid,
                    'amount' => $item['amount'],
                    'price' => $price,
                    'sum' => $price * $item['amount']
                ];
            }
        }

        // Создаём заказ как объект
        $order = (new Order($userId, $data['name'], $data['email'], $data['address'], $data['phone']))
            ->saveOrder();

        // Сохраняем товары заказа
        $order->saveOrderProductsBulk($orderItems);

        // Чистим корзину
        $this->userProductModel->clearCart($userId);

        // Редирект
        header("Location: /order-success?number=" . $order->getOrderNumber());
        exit;
    }

    // Валидация формы
    private function validateOrderForm(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) $errors['name'] = 'Имя обязательно';
        elseif (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s-]+$/u", $data['name'])) $errors['name'] = 'Имя может содержать только буквы и пробелы';

        if (empty($data['phone'])) $errors['phone'] = 'Телефон обязателен';
        elseif (!preg_match("/^\+?\d{10,15}$/", $data['phone'])) $errors['phone'] = 'Телефон должен содержать только цифры и может начинаться с +';

        if (empty($data['address'])) $errors['address'] = 'Адрес обязателен';
        elseif (!preg_match("/^[\w\s.,-]+$/u", $data['address'])) $errors['address'] = 'Адрес содержит недопустимые символы';

        if (empty($data['email'])) $errors['email'] = 'Email обязателен';
        elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Некорректный формат email';

        return $errors;
    }

    // Список всех заказов пользователя
    public function getOrders(): void
    {
        $userId = $this->checkSession();

        $orders = Order::getAllByUserId($userId);  // статический метод возвращает массив объектов Order

        foreach ($orders as &$order) {
            $orderProducts = $order->getOrderProducts();  // теперь возвращает объекты OrderProduct
            $productIds = array_map(fn($p) => $p->getProductId(), $orderProducts);

            if (!empty($productIds)) {
                $products = $this->productModel->getProductsByIds($productIds);

                foreach ($orderProducts as $op) {
                    $pid = $op->getProductId();
                    if (isset($products[$pid])) {
                        $products[$pid]->order_amount = $op->getAmount();
                        $products[$pid]->order_price = $op->getPrice();
                    }
                }
                $order->setProducts($products);
            } else {
                $order->setProducts([]);
            }
        }
        unset($order);

        require_once './../View/orders.php';
    }

    // Страница успеха
    public function getSuccessPage()
    {
        $userId = $this->checkSession();
        $orderNumber = $_GET['number'] ?? null;

        if (!$orderNumber) {
            header('Location: /catalog');
            exit;
        }

        $order = Order::getByNumber($orderNumber);
        if (!$order || !$order->orderBelongsToUser($order->getOrderId(), $userId)) {
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