<div class="container">

    <h2><?= htmlspecialchars($product->getName()) ?></h2>
    <p>Средний рейтинг: <?= renderStars(round($averageRating)) ?> (<?= round($averageRating,1) ?> из 5)</p>

    <?php if (isset($userCanReview) && $userCanReview): ?>
        <a href="/product/<?= $product->getId() ?>/reviews" class="add-review-button">✏ Оставить отзыв</a>
    <?php endif; ?>

    <?php foreach ($reviews as $review): ?>

        <div class="product-info review-card
            <?= ($currentUser && $currentUser->getId() === $review['user_id'])
                ? 'my-review'
                : '' ?>">

            <div class="product-image">
                <img src="<?= htmlspecialchars($product->getViewUrl()) ?>" alt="<?= htmlspecialchars($product->getName()) ?>">
            </div>

            <div class="product-details">
                <strong><?= htmlspecialchars($review['user_name']) ?></strong>

                <div class="review-date">
                    <?= !empty($review['created_date'])
                            ? date('d.m.Y H:i', strtotime($review['created_date']))
                            : 'Дата не указана' ?>
                </div>

                <div class="review-rating">
                    <?= renderStars($review['rating']) ?>
                </div>

                <?php if ($review['is_edited']): ?>
                    <div class="edited-label">Отредактированный отзыв</div>
                <?php endif; ?>

                <p><?= htmlspecialchars($review['comment']) ?></p>

                <?php if ($review['is_verified_purchase']): ?>
                    <span class="verified">✔ Подтвержденная покупка</span>
                <?php endif; ?>

                <?php if (
                        $currentUser &&
                        $currentUser->getId() === $review['user_id'] &&
                        !$review['is_edited']
                ): ?>
                    <a href="/review/<?= $review['id'] ?>/edit" class="edit-button">
                        ✏ Редактировать
                    </a>
                <?php endif; ?>
            </div>

        </div>

    <?php endforeach; ?>

    <a href="/catalog" class="back-button">⬅ Вернуться в каталог</a>

</div>

<?php
function renderStars($rating, $max = 5) {
    $html = '<span class="stars">';
    for ($i = 1; $i <= $max; $i++) {
        $html .= $i <= $rating
                ? '<span class="star filled">★</span>'
                : '<span class="star">★</span>';
    }
    $html .= '</span>';
    return $html;
}
?>

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

    .add-review-button {
        display: inline-block;
        margin-bottom: 20px;
        padding: 10px 20px;
        background-color: #ff7f50;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        border-radius: 10px;
        transition: 0.3s;
    }

    .add-review-button:hover {
        background-color: #e26e47;
        transform: translateY(-2px);
    }

    .review-card {
        display: flex;
        gap: 20px;
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 12px;
        background: #fff;
        align-items: flex-start;
    }

    .my-review {
        border: 2px solid #ff7f50;
        background-color: #fff8f4;
    }

    .product-image img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #eee;
    }

    .product-details {
        flex: 1;
    }

    .review-date {
        font-size: 12px;
        color: #888;
        margin-bottom: 5px;
    }

    .edit-button {
        display: inline-block;
        margin-top: 10px;
        padding: 6px 12px;
        background: #ffc107;
        color: #000;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
    }

    .verified {
        display: inline-block;
        margin-top: 5px;
        color: green;
        font-size: 14px;
    }

    .stars {
        display: inline-block;
        font-size: 18px;
        margin: 5px 0;
    }

    .star {
        color: #ccc; /* пустая звезда */
        margin-right: 2px;
    }

    .star.filled {
        color: #ffbc00; /* заполненная звезда */
    }

    .edited-label {
        font-size: 13px;
        color: #555;
        font-style: italic;
        margin-bottom: 5px;
    }

    .back-button {
        display: inline-block;
        margin-top: 30px;
        padding: 10px 20px;
        background-color: #3498db;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        border-radius: 10px;
        transition: 0.3s;
    }

    .back-button:hover {
        background-color: #2c80b4;
        transform: translateY(-2px);
    }
</style>