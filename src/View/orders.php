<div class="container">
    <h3>Мои заказы</h3>

    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <span>Заказ <?= htmlspecialchars($order->getOrderNumber()) ?></span>
                    <span><?= date('d.m.Y H:i', strtotime($order->getOrderDate())) ?></span>
                    <span class="toggle-indicator">▼</span>
                </div>

                <div class="order-products">
                    <?php foreach ($order->getProducts() as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?= htmlspecialchars($product->getViewUrl()) ?>" alt="<?= htmlspecialchars($product->getName()) ?>">
                            </div>
                            <div class="product-info">
                                <h5><?= htmlspecialchars($product->getName()) ?></h5>
                                <div class="product-details">
                                    <span>Кол-во: <?= $product->getAmount() ?></span>
                                    <span>Цена: <?= number_format($product->getPrice(), 2) ?> ₽</span>
                                </div>
                                <div class="product-actions">
                                    <!-- Кнопка для перехода к отзыву -->
                                    <a href="/product/<?= $product->getId() ?>/reviews" class="btn-review">Оставить отзыв</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-total">
                    Общая сумма:
                    <?php
                    $total = array_reduce($order->getProducts(), function($sum, $p) {
                        return $sum + ($p->getPrice() * $p->getAmount());
                    }, 0);
                    echo number_format($total, 2);
                    ?> ₽
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>У вас пока нет заказов.</p>
    <?php endif; ?>
</div>

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: "Inter", "Segoe UI", sans-serif;
        background: linear-gradient(135deg, #fde7f3 0%, #fff5f8 100%);
        padding: 40px 20px;
        color: #1f2937;
    }
    .container { max-width: 1200px; margin: 0 auto; }
    h3 { text-align: center; margin-bottom: 30px; font-size: 28px; font-weight: 700; color: #2d2a32; }

    .order-card {
        background: #fff0f5;
        border-radius: 22px;
        box-shadow: 0 20px 50px rgba(236,72,153,0.15);
        padding: 24px;
        margin-bottom: 30px;
        overflow: hidden;
        transition: all 0.3s;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        font-weight: 700;
        color: #2d2a32;
        cursor: pointer;
        align-items: center;
        font-size: 18px;
    }

    .order-header .toggle-indicator {
        font-size: 16px;
        margin-left: 10px;
        transition: transform 0.3s;
    }

    .order-card.expanded .order-header .toggle-indicator { transform: rotate(180deg); }

    .order-products, .order-total { max-height: 0; overflow: hidden; transition: max-height 0.5s ease; }

    .order-card.expanded .order-products, .order-card.expanded .order-total { max-height: 3000px; }

    .order-products { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px; }

    .product-card {
        background: #fff;
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        width: 300px;
        box-shadow: 0 10px 20px rgba(236,72,153,0.15);
        transition: transform 0.2s;
    }

    .product-card:hover { transform: translateY(-5px); }

    .product-image {
        width: 100%;
        height: 225px;
        background: #fde7f3;
        display: flex;
        align-items: center;
        justify-content: center;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }

    .product-image img { max-width: 100%; max-height: 100%; object-fit: contain; }

    .product-info { padding: 16px; display: flex; flex-direction: column; flex-grow: 1; }

    .product-info h5 {
        font-size: 18px;
        margin: 5px 0;
        color: #2d2a32;
        line-height: 1.2em;
        height: 2.4em;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-details {
        margin-top: auto;
        font-size: 15px;
        color: #2d2a32;
        display: flex;
        justify-content: space-between;
        font-weight: 600;
    }

    .product-actions {
        margin-top: 10px;
    }

    .btn-review {
        background-color: #ec4899;
        color: white;
        padding: 8px 16px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-review:hover {
        background-color: #f472b6;
    }

    .order-total {
        font-weight: 700;
        margin-top: 20px;
        text-align: right;
        font-size: 20px;
        padding-top: 15px;
        border-top: 2px dashed #f3b4d3;
    }

    @media (max-width: 1024px) { .product-card { width: 45%; } }
    @media (max-width: 600px) {
        .product-card { width: 100%; }
        .order-header { flex-direction: column; align-items: flex-start; gap: 6px; }
        .order-total { text-align: left; }
    }
</style>

<script>
    document.querySelectorAll('.order-header').forEach(header => {
        header.addEventListener('click', () => {
            header.parentElement.classList.toggle('expanded');
        });
    });
</script>