<?php
$product = $product ?? null;
$reviews = $reviews ?? [];
$averageRating = $averageRating ?? 0;
?>

<div class="container">

    <!-- 🔥 PRODUCT HEADER -->
    <div class="product-header">

        <div class="product-image">
            <img src="<?= htmlspecialchars($product->getViewUrl()) ?>" alt="Product image">
        </div>

        <div class="product-title">
            <h3>Отзывы о продукте: <?= htmlspecialchars($product->getName()) ?></h3>

            <p class="description">
                <?= htmlspecialchars($product->getDescription()) ?>
            </p>
        </div>

    </div>

    <!-- 🔥 AVERAGE RATING -->
    <div class="average-rating">
        <strong>Средняя оценка: </strong>

        <?php for ($i = 1; $i <= 5; $i++): ?>
            <?= $i <= $averageRating ? '★' : '☆' ?>
        <?php endfor; ?>

        <span>(<?= $averageRating ?> / 5)</span>
    </div>

    <!-- 🔥 REVIEWS -->
    <?php if (!empty($reviews)): ?>
        <div class="reviews-list">

            <?php foreach ($reviews as $review): ?>
                <div class="review-card">

                    <div class="review-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?= $i <= $review['rating'] ? '★' : '☆' ?>
                        <?php endfor; ?>
                    </div>

                    <p class="user">
                        <strong><?= htmlspecialchars($review['user_name']) ?></strong>
                    </p>

                    <p class="comment">
                        <?= htmlspecialchars($review['comment']) ?>
                    </p>

                    <p class="date">
                        <small><?= htmlspecialchars($review['created_date']) ?></small>
                    </p>

                </div>
            <?php endforeach; ?>

        </div>
    <?php else: ?>
        <p class="empty">Отзывов пока нет.</p>
    <?php endif; ?>

    <!-- 🔥 BUTTON -->
    <a href="/catalog" class="back-button">← Вернуться в каталог</a>

</div>

<style>
    body {
        background: #f4f6f8;
    }

    .container {
        max-width: 950px;
        margin: 40px auto;
        padding: 30px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* 🔥 PRODUCT HEADER */
    .product-header {
        display: flex;
        gap: 20px;
        align-items: center;
        margin-bottom: 25px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .product-image img {
        width: 160px;
        height: 160px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #eee;
    }

    .product-title h3 {
        margin-bottom: 10px;
        color: #333;
    }

    .description {
        color: #666;
        font-size: 14px;
        line-height: 1.4;
    }

    /* 🔥 AVERAGE RATING */
    .average-rating {
        text-align: center;
        font-size: 18px;
        margin-bottom: 20px;
    }

    /* 🔥 REVIEWS */
    .reviews-list {
        margin-top: 20px;
    }

    .review-card {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .review-rating {
        color: #ffbc00;
        font-size: 18px;
        margin-bottom: 10px;
    }

    .review-card .user {
        margin-bottom: 5px;
    }

    .review-card .comment {
        color: #555;
        font-size: 16px;
        margin-bottom: 5px;
    }

    .review-card .date {
        color: #888;
        font-size: 14px;
    }

    .empty {
        text-align: center;
        color: #777;
        margin-top: 20px;
    }

    /* 🔥 BUTTON */
    .back-button {
        display: block;
        width: 100%;
        margin-top: 25px;
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

    /* 🔥 RESPONSIVE */
    @media (max-width: 600px) {
        .product-header {
            flex-direction: column;
            text-align: center;
        }

        .product-image img {
            width: 100%;
            height: auto;
        }
    }
</style>