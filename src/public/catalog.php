<?php
session_start();
//if (!isset($_COOKIE['user_id'])) {
    //header('Location: ./get_login.php');
  if (!isset ($_SESSION['user_id'])) {
    header('Location: ./get_login.php');
} else {
    $pdo = new PDO('pgsql:host=postgres_db;dbname=mydb', 'yonateiko', 'pass');
    $stmt = $pdo->query ("SELECT * FROM products");

    $products = $stmt->fetchAll();
};

?>

<div class="container">
    <h3>Каталог товаров</h3>

    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="<?php echo $product['viewurl']; ?>" alt="Product image">
                </div>
                <div class="product-info">
                    <h4 class="product-name"><?php echo $product['name']; ?></h4>
                    <p class="product-description"><?php echo $product['description']; ?></p>
                    <div class="product-price">
                        <?php echo number_format($product['price'], 2); ?> ₽
                    </div>
                    <a href="#" class="btn-add">Добавить в корзину</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="add-product-wrapper">
        <a href="/add-product" class="add-product-btn">Добавить товар</a>
    </div>
</div>

<style>
    body {
        font-family: "Poppins", sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 20px;
    }

    .container {
        width: 90%;
        margin: 0 auto;
    }

    h3 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 25px;
    }

    .product-card {
        width: 240px;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
        background-color: white;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .product-image img {
        width: 100%;
        height: 250px;           /* зафиксированная высота */
        object-fit: cover;       /* обрезка по блоку без искажений */
        display: block;          /* убирает лишние пробелы снизу */
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }



    .product-info {
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .product-name {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }

    .product-description {
        font-size: 14px;
        color: #777;
        line-height: 1.4;
        height: 42px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-price {
        font-size: 18px;
        font-weight: 600;
        color: #F2994A;
    }

    .btn-add {
        display: inline-block;
        text-align: center;
        background-color: #F2994A;
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        margin-top: auto;
        transition: background-color 0.2s ease;
    }

    .btn-add:hover {
        background-color: #d87d2a;
    }
    .add-product-btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #04AA6D;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .add-product-btn:hover {
        background-color: #039c64;
    }
    .add-product-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 40px;
    }



</style>
<script src ="forCatalog.js"></script>