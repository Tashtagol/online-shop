<?php

$product = $product ?? null;
$errors = $errors ?? [];
$old = $old ?? [];
$success = $success ?? false;

?>

<div class="container">

    <h3>Оставить отзыв на продукт: <?= htmlspecialchars($product->getName()) ?></h3>

    <?php if ($success): ?>
        <div class="success-message">
            Отзыв успешно добавлен!
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="product-info">
        <div class="product-image">
            <img src="<?= htmlspecialchars($product->getViewUrl()) ?>">
        </div>

        <div class="product-details">
            <h4><?= htmlspecialchars($product->getName()) ?></h4>
            <p><?= htmlspecialchars($product->getDescription()) ?></p>
        </div>
    </div>

    <form method="POST" action="/product/<?= $product->getId() ?>/reviews">

        <input type="hidden" name="form_sent" value="1">

        <div class="form-group">
            <label>Оценка:</label>

            <div class="rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio"
                           name="rating"
                           value="<?= $i ?>"
                           id="star<?= $i ?>"
                            <?= (isset($old['rating']) && (int)$old['rating'] === $i) ? 'checked' : '' ?>>

                    <label for="star<?= $i ?>">★</label>
                <?php endfor; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Комментарий:</label>

            <textarea name="comment"><?= htmlspecialchars($old['comment'] ?? '') ?></textarea>
        </div>

        <button type="submit">Отправить отзыв</button>

        <!-- 🔥 КНОПКИ НАВИГАЦИИ -->
        <a href="/catalog" class="back-button">← Вернуться в каталог</a>
        <a href="/orders" class="orders-button">← Вернуться в мои заказы</a>

    </form>

</div>

<style>
    .container {
        max-width: 800px;
        margin: 40px auto;
        padding: 30px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    h3 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    .error-message {
        background-color: #ffe0e0;
        color: #d8000c;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .product-info {
        display: flex;
        gap: 20px;
        margin-bottom: 25px;
        align-items: center;
    }

    .product-image img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #eee;
    }

    .product-details h4 {
        margin-bottom: 10px;
        font-size: 22px;
        color: #222;
    }

    .product-details p {
        color: #555;
        line-height: 1.5;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-group textarea {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 16px;
        outline: none;
        resize: vertical;
        min-height: 100px;
    }

    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-start;
        gap: 5px;
    }

    .rating input[type="radio"] {
        display: none;
    }

    .rating label {
        font-size: 36px;
        color: #ccc;
        cursor: pointer;
        transition: 0.3s;
    }

    .rating label:hover,
    .rating label:hover ~ label,
    .rating input[type="radio"]:checked ~ label {
        color: #ffbc00;
        transform: scale(1.2);
    }

    button {
        width: 100%;
        padding: 14px;
        background-color: #ff7f50;
        color: white;
        font-size: 18px;
        font-weight: 600;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 10px;
    }

    button:hover {
        background-color: #e26e47;
        transform: translateY(-2px);
    }

    /* 🔥 КНОПКА НАЗАД */
    .back-button {
        display: block;
        width: 100%;
        margin-top: 15px;
        padding: 14px;
        text-align: center;
        background-color: #6c757d;
        color: white;
        font-size: 18px;
        font-weight: 600;
        border-radius: 10px;
        text-decoration: none;
        transition: 0.3s;
    }

    .back-button:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
    }

    /* 🔥 МОИ ЗАКАЗЫ */
    .orders-button {
        display: block;
        width: 100%;
        margin-top: 15px;
        padding: 14px;
        text-align: center;
        background-color: #343a40;
        color: white;
        font-size: 18px;
        font-weight: 600;
        border-radius: 10px;
        text-decoration: none;
        transition: 0.3s;
    }

    .orders-button:hover {
        background-color: #23272b;
        transform: translateY(-2px);
    }
</style>