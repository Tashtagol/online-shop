<?php
namespace Controller;
use Model\UserProduct;
use Model\Product;
use Model\User;

class dCartController
{
    private UserProduct $userProductModel;
    private User $userModel;
    private Product $productModel;
    public function __construct()
    {

        $this->userProductModel = new UserProduct();
        $this->productModel = new Product();
        $this->userModel = new User();

    }

    public function getCartForm()
    {
        $userId = $this->checkSession();
        $products = $this->getCart($userId);
        $total = $this->getTotal($products);

        require_once './../View/cart.php'; // Подключаем вьюшку
    }

    private function getCart(int $userId): array
    {

        $userProductsData = $this->userProductModel->getUserIdCart($userId);

        $ProductsIds = [];
        foreach ($userProductsData as $item) {
            $ProductsIds[] = $item['product_id'];
        }

        $allProducts = $this->productModel->getProductsByIds($ProductsIds);
        $ProductById = [];

        foreach ($allProducts as $product) {
            $ProductById[$product['id']] = $product;
        }

        $products = [];
        foreach ($userProductsData as $userProduct) {
            $ProductId = $userProduct['product_id'];
            $amount = $userProduct['amount'];
            if (isset($ProductById[$ProductId])) {
                $product = $ProductById[$ProductId];
                $product['amount'] = $amount;
                $products[] = $product;
            }
        }
        return $products;

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
