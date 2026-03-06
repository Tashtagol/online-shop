<?php
namespace Controller;

use Model\Product;
use Model\UserProduct;
use Request\ProductRequest;
use Service\CartService;
use Service\ProductService;

class ProductController
{
    private Product $productModel;

    private ProductService $productService;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->productService = new ProductService();
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

       $this->productService->addProduct($userId, $productId, $amount);

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