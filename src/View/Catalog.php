<div class="container">
    <h3>Каталог товаров</h3>

    <!-- Кнопки сверху -->
    <div class="top-buttons">
        <a href="/orders" class="add-orders-btn">Мои заказы</a>
        <a href="/add-product" class="add-product-btn">Добавить товар</a>
    </div>

    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($product->getVieUrl()); ?>" alt="Изображение товара">
                    </div>
                    <div class="product-info">
                        <h4 class="product-name"><?php echo htmlspecialchars($product->getName()); ?></h4>
                        <p class="product-description"><?php echo htmlspecialchars($product->getDescription()); ?></p>
                        <div class="product-price">
                            <?php echo number_format($product->getPrice(), 2, ',', ' '); ?> ₽
                        </div>
                        <a href="/add-product" class="btn-add">Добавить в корзину</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Товары отсутствуют</p>
        <?php endif; ?>
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
        max-width: 1200px;
        margin: 0 auto;
    }

    h3 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    /* Кнопки сверху */
    .top-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 30px;
    }

    .add-product-btn,
    .add-orders-btn {
        display: inline-block;
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .add-product-btn {
        background-color: #04AA6D;
    }

    .add-product-btn:hover {
        background-color: #039c64;
    }

    .add-orders-btn {
        background-color: #F2994A;
    }

    .add-orders-btn:hover {
        background-color: #d87d2a;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 25px;
    }

    .product-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s, box-shadow 0.3s;
        width: 100%;
        max-width: 260px;
        margin: 0 auto;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .product-image img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .product-info {
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex-grow: 1;
    }

    .product-name {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin: 0;
    }

    .product-description {
        font-size: 14px;
        color: #777;
        line-height: 1.4;
        height: 42px;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0;
    }

    .product-price {
        font-size: 18px;
        font-weight: 600;
        color: #F2994A;
    }

    .btn-add {
        background-color: #F2994A;
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        text-align: center;
        margin-top: auto;
        transition: background-color 0.2s ease;
    }

    .btn-add:hover {
        background-color: #d87d2a;
    }
</style>