<?php
$product = $product ?? null;
$errors = $errors ?? [];
$oldData = $old ?? [];
$success = $success ?? false;
?>

<div class="container">
    <h3>Оставить отзыв на продукт: <?= htmlspecialchars($product->getName()) ?></h3>

    <!-- Уведомление об успехе -->
    <?php if ($success): ?>
        <div class="success-message">
            Отзыв успешно добавлен!
        </div>
    <?php endif; ?>

    <!-- Ошибки при отправке -->
    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="product-info">
        <div class="product-image">
            <img src="<?= htmlspecialchars($product->getViewUrl()) ?>" alt="<?= htmlspecialchars($product->getName()) ?>">
        </div>
        <div class="product-details">
            <h4><?= htmlspecialchars($product->getName()) ?></h4>
            <p><?= htmlspecialchars($product->getDescription()) ?></p>
        </div>
    </div>

    <form action="/product/<?= htmlspecialchars($product->getId()) ?>/reviews" method="POST" id="review-form">
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product->getId()) ?>">

        <div class="form-group">
            <label>Оценка (1-5):</label>
            <div class="rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" <?= (isset($oldData['rating']) && $oldData['rating'] == $i) ? 'checked' : '' ?>>
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
    .container { max-width: 800px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    h3 { text-align: center; margin-bottom: 30px; color: #333; }
    .error-messages { background-color: #ffe0e0; color: #d8000c; padding: 12px 20px; border-radius: 8px; margin-bottom: 25px; }
    .success-message { background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 25px; }
    .product-info { display: flex; gap: 20px; margin-bottom: 25px; align-items: center; }
    .product-image img { width: 150px; height: 150px; object-fit: cover; border-radius: 12px; border: 2px solid #eee; }
    .product-details h4 { margin-bottom: 10px; font-size: 22px; color: #222; }
    .product-details p { color: #555; line-height: 1.5; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
    .form-group textarea { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 16px; outline: none; transition: border 0.3s, box-shadow 0.3s; resize: vertical; min-height: 100px; }
    .rating { display: flex; flex-direction: row-reverse; justify-content: flex-start; gap: 5px; }
    .rating input[type="radio"] { display: none; }
    .rating label { font-size: 36px; color: #ccc; cursor: pointer; transition: color 0.3s, transform 0.2s; }
    .rating label:hover,
    .rating label:hover ~ label,
    .rating input[type="radio"]:checked ~ label { color: #ffbc00; transform: scale(1.2); }
    button { display: block; width: 100%; padding: 14px; background-color: #ff7f50; color: white; font-size: 18px; font-weight: 600; border: none; border-radius: 10px; cursor: pointer; transition: background 0.3s, transform 0.2s; }
    button:hover { background-color: #e26e47; transform: translateY(-2px); }
</style>

<script>
    document.getElementById("review-form").addEventListener("submit", function(event) {
        var rating = document.querySelector('input[name="rating"]:checked');
        if (!rating) {
            alert("Пожалуйста, выберите рейтинг от 1 до 5.");
            event.preventDefault(); // Останавливает отправку формы
        }
    });
</script>