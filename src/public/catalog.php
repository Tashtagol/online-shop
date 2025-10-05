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
    <h3>Catalog</h3>
    <div class="card-deck">
        <?php foreach ($products as $product): ?>
        <div class="card text-center">
            <a href="#">
                <div class="card-header">
                    Электроника
                </div>
                <img class="card-img-top" src="<?php echo $product['urlview']; ?>" alt="Card image">
                <div class="card-body">
                    <p class="card-text text-muted"><?php echo $product['name']; ?> </p>
                    <a href="#"><h5 class="card-title"><?php echo $product['description']; ?></h5></a>
                    <div class="card-footer">
                        Price: <?php echo $product['price']; ?> рублей
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    body {
        font-style: sans-serif;
    }

    a {
        text-decoration: none;
    }

    a:hover {
        text-decoration: none;
    }

    h3 {
        line-height: 3em;
    }

    .card {
        max-width: 16rem;
    }

    .card:hover {
        box-shadow: 1px 2px 10px lightgray;
        transition: 0.2s;
    }

    .card-header {
        font-size: 18px;
        color: cornflowerblue;
        background-color: white;
    }

    .text-muted {
        font-size: 11px;
    }

    .card-footer{
        font-weight: bold;
        font-size: 20px;
        background-color: white;
    }

    /* Для увеличения размера названий товаров */
    .card-title {
        font-size: 16px; /* увеличьте по желанию */
        font-weight: bold;
    }
    .card-text {
        font-size: 24px; /* или больше, по желанию */
        font-weight: bold;
    }
    .card-img-top {
        width: 125  %;          /* картинка занимает всю ширину карточки */
        height: 320px;        /* задайте нужную высоту (больше, если хотите) */
        object-fit: cover;    /* обрезает изображение по размеру без искажения */
    }
</style>
<script src ="forCatalog.js"></script>