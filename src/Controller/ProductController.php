<?php
namespace Controller;
use Model\UserProduct;
use Model\Product;
class ProductController
{
    private Product $productModel;
    private UserProduct $userProductModel;

    public function __construct()
    {
        // Теперь мы используем этот объект везде в классе
        $this->productModel = new Product();
        $this->userProductModel = new UserProduct();
    }

    public function getProduct()
    {

       $this -> checkSession();
            try {
                $products = $this->productModel -> getProducts();

                require_once './../View/Catalog.php';

            } catch (PDOException $e) {
                die("Ошибка подключения к БД: " . $e->getMessage());
            }
    }
    public function getProductForm()
    {
        require_once './../View/addProduct.php';
    }
    public function addProduct()
    {
        $errors = $this-> ValidateAddProduct($_POST);

        if (empty($errors)) {
            $userId = $this->checkSession();
            $productId = (int) $_POST['product-id'];
            $amount = (int) $_POST['amount'];

            $cartItem = $this->userProductModel -> getUserProduct($userId, $productId);


            if ($cartItem === false) {

                $this-> userProductModel -> setUserProduct($userId, $productId, $amount);

            } else {

                $this -> userProductModel-> updateUserProduct($amount, $userId,$productId);
            }
            header("location: /cart");
            exit;

        } else {
            require_once './../View/addProduct.php';
        }
    }
    public function ValidateAddProduct(array $arrPost): array
    {
        $errors = [];

        if (!isset($arrPost['product-id']) || $arrPost['product-id'] === '') {
            $errors['product-id'] = 'Требуется ввести Product-id';
        } else {

            $productId = (int) $arrPost['product-id'];
            $productModel = new Product();
            $productData = $productModel->getProductId($productId);

            if (!$productData) {
                $errors['product-id'] = 'Товара с таким ID не существует';
            }
        }
        if (!isset($arrPost['amount']) || $arrPost['amount'] === '') {
            $errors['amount'] = 'Требуется ввести количество продуктов';
        } elseif (!is_numeric($arrPost['amount'])) {
            $errors['amount'] = 'Количество продуктов должно быть числом';
        } elseif ($arrPost['amount'] < 1) {
            $errors['amount'] = 'Количество продуктов должно быть положительным';
        }

        return $errors;
    }
    private function checkSession(): int
    {
        // ✅ стартуем сессию только если она ещё не запущена
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ./login');
            exit;
        }

        return $_SESSION['user_id'];
    }
}