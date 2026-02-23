<?php
$total = 0;
?>

<header class="cart-header">
    <h1>🛒 Ваша корзина</h1>
</header>

<section class="cart">
    <div class="cart-wrapper">

        <?php foreach ($products as $product):
            $subtotal = $product['price'] * $product['amount'];
            $total += $subtotal;
            ?>
            <div class="cart-item">
                <div class="cart-img">
                    <img src="<?= $product['viewurl']; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                </div>

                <div class="cart-info">
                    <div class="cart-title"><?= htmlspecialchars($product['name']); ?></div>
                    <div class="cart-desc">
                        <?= str_replace('iPhone', '<strong>iPhone</strong>', htmlspecialchars($product['description'])); ?>
                    </div>
                </div>

                <div class="cart-right">
                    <div class="cart-price"><?= number_format($subtotal, 2); ?> ₽</div>

                    <div class="cart-qty">
                        <button type="button" class="qty-minus">−</button>
                        <span><?= $product['amount']; ?></span>
                        <button type="button" class="qty-plus">+</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="cart-summary">
            <div class="cart-total">
                <span>Общая сумма:</span>
                <strong><?= number_format($total, 2); ?> ₽</strong>
            </div>

            <!-- Форма теперь идет через фронт-контроллер -->
            <form action="/order" method="post">
                <button class="cart-btn" type="submit">Оформить заказ</button>
            </form>
        </div>

    </div>
</section>

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: "Inter", "Segoe UI", sans-serif;
        background: linear-gradient(135deg, #fde7f3 0%, #fff5f8 100%);
        min-height: 100vh;
        padding: 40px 20px;
        color: #1f2937;
    }

    .cart-header { display: flex; justify-content: center; margin-bottom: 30px; }
    .cart-header h1 { font-size: 34px; font-weight: 800; color: #2d2a32; text-align: center; }

    .cart-wrapper {
        max-width: 950px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 22px;
        padding: 26px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }

    .cart-item {
        display: grid;
        grid-template-columns: 120px 1fr 180px;
        gap: 22px;
        padding: 18px 0;
        border-bottom: 1px solid #f1cfe0;
        align-items: center;
    }

    .cart-img {
        width: 120px;
        height: 120px;
        border-radius: 16px;
        background: #fce7f3;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .cart-img img { width: 100%; height: 100%; object-fit: contain; }

    .cart-title { font-size: 18px; font-weight: 700; margin-bottom: 6px; }
    .cart-desc {
        font-size: 15px;
        line-height: 1.6;
        color: #4b5563;
        max-width: 520px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        transition: color 0.2s, transform 0.2s;
    }
    .cart-desc strong { font-weight: 700; color: #ec4899; }
    .cart-item:hover .cart-desc { color: #2563eb; transform: translateY(-1px); }

    .cart-right { text-align: right; }
    .cart-price { font-size: 20px; font-weight: 800; color: #3b82f6; margin-bottom: 12px; }

    .cart-qty {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #f3b4d3;
        border-radius: 12px;
        padding: 6px 10px;
        background: #fff;
    }
    .cart-qty button {
        width: 26px; height: 26px;
        border: none; border-radius: 8px;
        background: #fde2f0;
        cursor: pointer; font-size: 16px;
        transition: 0.15s;
    }
    .cart-qty button:hover { background: #fbcfe8; }
    .cart-qty span { min-width: 22px; text-align: center; font-size: 15px; font-weight: 600; }

    .cart-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 2px dashed #f3b4d3;
    }
    .cart-total { font-size: 22px; }
    .cart-total strong { font-size: 26px; color: #3b82f6; }

    .cart-btn {
        padding: 16px 56px;
        border: none;
        border-radius: 16px;
        background: linear-gradient(135deg, #ec4899, #f472b6);
        color: #fff;
        font-size: 17px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 15px 35px rgba(236,72,153,0.4);
        transition: 0.2s;
    }
    .cart-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 45px rgba(236,72,153,0.5);
    }
</style>
