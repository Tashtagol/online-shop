<?php
// Получаем данные продукта и ошибок (если есть)
$product = $product ?? null;
$errors = $errors ?? [];
$oldData = $old ?? [];
?>

<div class="container">
    <h3>Оставить отзыв на продукт: <?= htmlspecialchars($product->getName()) ?></h3>

    <!-- Выводим ошибки валидации -->
    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Данные продукта -->
    <div class="product-info">
        <div class="product-image">
            <img src="<?= htmlspecialchars($product->getViewUrl()) ?>" alt="<?= htmlspecialchars($product->getName()) ?>" />
        </div>
        <div class="product-details">
            <h4><?= htmlspecialchars($product->getName()) ?></h4>
            <p><?= htmlspecialchars($product->getDescription()) ?></p>
        </div>
    </div>

    <!-- Форма для отзыва -->
    <form action="/review/submit" method="POST">
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product->getProductId()) ?>" />

        <div class="form-group">
            <label for="rating">Оценка (1-5):</label>
            <div class="rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" <?= ($oldData['rating'] ?? '') == $i ? 'checked' : '' ?>>
                    <label for="star<?= $i ?>">★</label>
                <?php endfor; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="comment">Комментарий:</label>
            <textarea name="comment" id="comment" rows="4"><?= htmlspecialchars($oldData['comment'] ?? '') ?></textarea>
        </div>

        <button type="submit">Отправить отзыв</button>
    </form>
</div>

<style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .product-info {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .product-image img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }

    .product-details h4 {
        margin-bottom: 10px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border-radius: 6px;
        border: 1px solid #ddd;
    }

    .form-group textarea {
        resize: vertical;
    }

    .rating input[type="radio"] {
        display: none;
    }

    .rating label {
        font-size: 30px;
        color: #ffbc00;
        cursor: pointer;
    }

    .rating input[type="radio"]:checked ~ label {
        color: #ffbc00;
    }

    .rating input[type="radio"]:not(:checked) ~ label {
        color: #ccc;
    }

    button {
        padding: 12px 24px;
        background-color: #ff7f50;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #e26e47;
    }

    .error-messages {
        background-color: #f8d7da;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 6px;
    }
</style>