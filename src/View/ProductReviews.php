<?php
$product = $product ?? null;
$reviews = $reviews ?? [];
$averageRating = $averageRating ?? 0;
?>

<div class="container">
    <h3>Отзывы о продукте: <?= htmlspecialchars($product->getName()) ?></h3>

    <!-- Отображаем среднюю оценку -->
    <div class="average-rating">
        <strong>Средняя оценка: </strong>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <?= $i <= $averageRating ? '★' : '☆' ?>
        <?php endfor; ?>
        <span>(<?= $averageRating ?> / 5)</span>
    </div>

    <!-- Список отзывов -->
    <?php if (!empty($reviews)): ?>
        <div class="reviews-list">
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <!-- Показываем рейтинг в виде звезд -->
                    <div class="review-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?= $i <= $review['rating'] ? '★' : '☆' ?>
                        <?php endfor; ?>
                    </div>
                    <!-- Имя пользователя -->
                    <p><strong><?= htmlspecialchars($review['user_name']) ?>:</strong></p>
                    <!-- Текст отзыва -->
                    <p><?= htmlspecialchars($review['comment']) ?></p>
                    <!-- Дата отзыва -->
                    <p><small>Дата отзыва: <?= htmlspecialchars($review['created_date']) ?></small></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Отзывов пока нет.</p>
    <?php endif; ?>
</div>

<style>
    .container {
        max-width: 950px;
        margin: 0 auto;
        padding: 40px 20px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    h3 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    .average-rating {
        text-align: center;
        font-size: 18px;
        margin-bottom: 20px;
    }

    .reviews-list {
        margin-top: 20px;
    }

    .review-card {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .review-rating {
        color: #ffbc00;
        font-size: 18px;
        margin-bottom: 10px;
    }

    .review-card p {
        color: #555;
        font-size: 16px;
    }

    .review-card small {
        color: #888;
        font-size: 14px;
    }
</style>