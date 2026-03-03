<?php
namespace Controller;

use Model\UserProduct;
use Model\Product;
use Model\OrderProduct;
use Request\Request;
use Request\RegistrateRequest;
use Request\LoginRequest;
class CartController
{
    private UserProduct $userProductModel;
    private Product $productModel;

    public function __construct()
    {
        $this->userProductModel = new UserProduct();
        $this->productModel = new Product();
    }

    public function getCartForm()
    {
        $userId = $this->checkSession();
        $products = $this->getCart($userId);
        $total = $this->getTotal($products);

        require_once './../View/cart.php';
    }

    private function getCart(int $userId): array
    {
        $userProducts = $this->userProductModel->getUserIdCart($userId);

        if (empty($userProducts)) {
            return [];
        }

        $productIds = array_map(fn($item) => $item->product_id, $userProducts);
        $products = $this->productModel->getProductsByIds($productIds);

        $result = [];

        foreach ($userProducts as $item) {
            $pid = $item->product_id;

            if (isset($products[$pid])) {
                // Добавляем description товара в массив
                $result[] = [
                    'id' => $pid,
                    'name' => $products[$pid]->getName(),
                    'price' => $products[$pid]->getPrice(),
                    'amount' => $item->amount,
                    'sum' => $products[$pid]->getPrice() * $item->amount,
                    'viewurl' => $products[$pid]->getVieUrl(),
                    'description' => $products[$pid]->getDescription() // добавляем описание товара
                ];
            }
        }

        return $result;
    }

    private function getTotal(array $products): float
    {
        $total = 0;
        foreach ($products as $product) {
            $total += $product['price'] * $product['amount'];
        }
        return $total;
    }

    private function checkSession(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ./login');
            exit;
        }

        return (int)$_SESSION['user_id'];
    }
}