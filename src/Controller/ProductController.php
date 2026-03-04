<?php
namespace Controller;

use Model\Product;
use Model\UserProduct;
use Request\ProductRequest;

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

    public function addProduct(ProductRequest $request)
    {
        $request->validate();
        $errors = $request->getErrors();
        if (!empty($errors)) {
            require_once './../View/addProduct.php';
            return;
        }

        $userId = $this->checkSession();
        $productId = $request ->getProductId();
        $amount = $request ->getAmount();

        $cartItem = $this->userProductModel->getUserProduct($userId, $productId);

        if (!$cartItem) {
            $this->userProductModel->setUserProduct($userId, $productId, $amount);
        } else {
            $this->userProductModel->updateUserProduct($amount, $userId, $productId);
        }

        header("Location: /cart");
        exit;
    }


    public function checkSession(): int
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