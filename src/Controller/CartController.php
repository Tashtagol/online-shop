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

    // Страница каталога
    public function getCatalogForm()
    {
        // Получаем текущего пользователя через сервис авторизации
        $user = $this->checkAuth();

        // Получаем все продукты каталога
        $products = \Model\Product::getAllProducts();

        // Получаем корзину пользователя через CartService
        $cartProducts = $this->cartService->getCart($user->getId());

        // Считаем общее количество товаров в корзине
        $cartCount = array_sum(array_map(fn($item) => $item->getAmount(), $cartProducts)); // Исправили доступ к данным

        // Подключаем представление
        require_once './../View/Catalog.php';
    }

    // Страница корзины
    public function getCartForm()
    {
        $user = $this->checkAuth();
        $products = $this->cartService->getCart($user->getId());
        $total = $this->cartService->getTotal($products);

        // Подключаем представление
        require_once './../View/cart.php';
    }

    // Добавление/обновление товара
    public function addOrUpdate()
    {
        $user = $this->checkAuth();
        $data = json_decode(file_get_contents('php://input'), true);

        $productId = intval($data['product_id'] ?? 0);
        $amount = intval($data['amount'] ?? 1);
        $source = $data['source'] ?? 'catalog';

        // Получаем все товары в корзине
        $existingProducts = $this->cartService->getCart($user->getId());
        $productInCart = null;

        foreach ($existingProducts as $product) {
            if ($product->getId() === $productId) {
                $productInCart = $product;
                break;
            }
        }

        if ($productInCart) {
            // Если товар уже есть в корзине, обновляем его количество
            $newAmount = ($source === 'catalog') ? $productInCart->getAmount() + $amount  : $amount;

            // Если количество товара стало 0, удаляем товар
            if ($newAmount <= 0) {
                $this->cartService->remove($user->getId(), $productId);  // Метод для удаления товара
            } else {
                $newAmount = max(0, $newAmount);
                $this->cartService->update($user->getId(), $productId, $newAmount);
            }
        } else {
            // Если товара нет в корзине, добавляем его
            $this->cartService->add($user->getId(), $productId, $amount);
        }

        // Обновляем данные корзины
        $products = $this->cartService->getCart($user->getId());
        $total = $this->cartService->getTotal($products);

        $subtotal = 0;
        foreach ($products as $p) {
            if ($p->getId() === $productId) {
                $subtotal = $p->getPrice() * $p->getAmount(); // Считаем стоимость для конкретного товара
                break;
            }
        }

        $count = array_sum(array_map(fn($p) => $p->getAmount(), $products)); // Подсчитываем общее количество товаров в корзине

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'total' => $total,
            'subtotal' => $subtotal,
            'count' => $count
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

    public function clearCart()
    {
        $user = $this->checkAuth();
        $this->cartService->clear($user->getId());

        // Пустая корзина → count = 0, items = []
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'count' => 0,
            'items' => [],
            'total' => 0
        ]);
        exit;
    }
}