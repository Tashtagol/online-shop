<?php
namespace Controller;

use Model\Product;
use Model\UserProduct;

class ProductController
{
    private Product $productModel;
    private UserProduct $userProductModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->userProductModel = new UserProduct();
    }

    public function getProduct()
    {
        $this->checkSession();

        $products = $this->productModel->getAllProducts();
        require_once './../View/Catalog.php';
    }

    public function getProductForm()
    {
        require_once './../View/addProduct.php';
    }

    public function addProduct()
    {
        $errors = $this->validateAddProduct($_POST);

        if (!empty($errors)) {
            require_once './../View/addProduct.php';
            return;
        }

        $userId = $this->checkSession();
        $productId = (int)$_POST['product-id'];
        $amount = (int)$_POST['amount'];

        $cartItem = $this->userProductModel->getUserProduct($userId, $productId);

        if (!$cartItem) {
            $this->userProductModel->setUserProduct($userId, $productId, $amount);
        } else {
            $this->userProductModel->updateUserProduct($amount, $userId, $productId);
        }

        header("Location: /cart");
        exit;
    }

    private function validateAddProduct(array $data): array
    {
        $errors = [];

        if (empty($data['product-id'])) {
            $errors['product-id'] = 'Требуется ID товара';
        }

        if (empty($data['amount']) || !is_numeric($data['amount']) || $data['amount'] < 1) {
            $errors['amount'] = 'Количество должно быть положительным числом';
        }

        return $errors;
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