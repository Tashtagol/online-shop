<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header ("location: /login");
}

function ValidateAddProductForm(array $arrPost): array
{
    $errors = [];

    $pdo = new PDO('pgsql:host=postgres_db;dbname=mydb', 'yonateiko', 'pass');

    if (!isset($arrPost['product-id']) || $arrPost['product-id'] === '') {
        $errors['product-id'] = 'Требуется ввести Product-id';
    } else {
        $productId = (int)$arrPost['product-id'];
        $stmt = $pdo->prepare("SELECT id FROM products WHERE id = :product_id");
        $stmt->execute(['product_id' => $productId]);

        if (!$stmt->fetch()) {
            $errors['product-id'] = 'Товара с таким ID не существует';
        }
    }

    if (isset($arrPost['amount'])) {
        $amount = $arrPost['amount'];
    } else {
        $errors['amount'] = 'Требуется ввести Amount';
    }

    if (isset($arrPost['amount']) && $arrPost['amount'] !== '') {
        $amount = $arrPost['amount'];

        if (!is_numeric($amount)) {
            $errors['amount'] = "Количество продуктов должно быть числом";
        } elseif ($amount < 1) {
            $errors['amount'] = "Количество продуктов должно быть положительным";
        }
    } else {
        $errors['amount'] = 'Требуется ввести количество продуктов';
    }
    return $errors;
}

$errors = ValidateAddProductForm($_POST);

if (empty($errors)) {
    $userId = $_SESSION['user_id'];
    $productId = $_POST['product-id'];
    $amount = $_POST['amount'];

    $pdo = new PDO('pgsql:host=postgres_db;dbname=mydb', 'yonateiko', 'pass');

    $stmt = $pdo->prepare("SELECT amount FROM user_product WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
    $userProductsData = $stmt->fetch();

    if ($userProductsData === false) {
        $stmt = $pdo->prepare("INSERT INTO user_product (user_id,product_id,amount) VALUES (:user_id, :product_id, :amount)");
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId,'amount' => $amount]);
    } else {
        $stmt = $pdo->prepare("UPDATE user_product SET amount = amount + :amount WHERE user_id = :user_id");
        $stmt->execute(['amount' => $amount, 'user_id' => $userId, 'amount' => $amount]);
    }

    header("location: /cart");
} else {
    require_once './get_add_product.php';
}