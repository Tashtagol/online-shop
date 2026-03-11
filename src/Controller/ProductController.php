<?php
namespace Controller;

use Model\Product;
use Model\UserProduct;
use Request\ProductRequest;
use Service\Auth\AuthServiceInterface;
use Service\Auth\AuthSessionService;
use Service\CartService;
use Service\ProductService;

class ProductController
{
    private Product $productModel;

    private ProductService $productService;
    private AuthServiceInterface $authService;
    private CartService $cartService;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->productService = new ProductService();
        $this->authService = new AuthSessionService();
        $this->cartService = new CartService();
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

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            header("Location: /login");
            exit;
        }
        $userId = $user->getId();
        $productId = $request ->getProductId();
        $amount = $request ->getAmount();

       $this->productService->addProduct($userId, $productId, $amount);

        header("Location: /cart");
        exit;
    }


    private function checkSession(): void
    {
        if (!$this->authService->check()) {
            header("Location: /login");
            exit;
        }
    }
}