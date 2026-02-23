<?php
$total = 0;
?>

<header class="checkout-header">
    <h1>📝 Оформление заказа</h1>
</header>

<section class="checkout">
    <div class="checkout-wrapper">

        <form action="/order" method="post" class="checkout-form">

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Имя</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" placeholder="Ваше имя">
                    <?php if(!empty($errors['name'])): ?>
                        <div class="form-error"><?= $errors['name'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" placeholder="+7 (___) ___-__-__">
                    <?php if(!empty($errors['phone'])): ?>
                        <div class="form-error"><?= $errors['phone'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="address">Адрес доставки</label>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($old['address'] ?? '') ?>" placeholder="Город, улица, дом, квартира">
                    <?php if(!empty($errors['address'])): ?>
                        <div class="form-error"><?= $errors['address'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="example@mail.com">
                    <?php if(!empty($errors['email'])): ?>
                        <div class="form-error"><?= $errors['email'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="payment">Способ оплаты</label>
                    <select id="payment" name="payment">
                        <option value="">Выберите способ оплаты</option>
                        <option value="card" <?= (!empty($old['payment']) && $old['payment']=='card')?'selected':'' ?>>Банковская карта</option>
                        <option value="cash" <?= (!empty($old['payment']) && $old['payment']=='cash')?'selected':'' ?>>Наличными</option>
                        <option value="online" <?= (!empty($old['payment']) && $old['payment']=='online')?'selected':'' ?>>Онлайн-оплата</option>
                    </select>
                </div>
            </div>

            <div class="checkout-items">
                <h4>Товары в заказе:</h4>
                <div class="checkout-item-list">
                    <?php foreach ($orderItems as $item):
                        $total += $item['sum'];
                        ?>
                        <div class="checkout-item">
                            <div class="checkout-img">
                                <img src="<?= htmlspecialchars($item['viewurl']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            </div>
                            <div class="item-info">
                                <span class="item-name"><?= htmlspecialchars($item['name']) ?></span>
                                <span class="item-amount"><?= $item['amount'] ?> шт × <?= number_format($item['price'],2) ?> ₽</span>
                                <span class="item-subtotal"><?= number_format($item['sum'],2) ?> ₽</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="checkout-total">
                <span>Общая сумма:</span>
                <strong><?= number_format($total,2) ?> ₽</strong>
            </div>

            <button class="btn-submit" type="submit">Подтвердить заказ</button>
        </form>

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

    .checkout-header { display: flex; justify-content: center; margin-bottom: 30px; }
    .checkout-header h1 { font-size: 34px; font-weight: 800; color: #2d2a32; text-align: center; }

    .checkout-wrapper {
        max-width: 950px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 22px;
        padding: 26px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }

    .checkout-form { display: flex; flex-direction: column; gap: 24px; }

    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group input, .form-group select {
        padding: 10px;
        border-radius: 12px;
        border: 1px solid #f3b4d3;
        font-size: 14px;
        background: #fff0f5;
    }

    .form-error { color:#e3342f; font-size:13px; margin-top:4px; }

    .checkout-items h4 { margin-bottom: 15px; font-size: 20px; font-weight: 700; color: #2d2a32; }
    .checkout-item-list { display: flex; flex-direction: column; gap: 16px; }
    .checkout-item { display: flex; align-items: center; gap: 18px; background: #fdf0f5; padding: 12px; border-radius: 16px; }
    .checkout-img { width: 120px; height: 120px; border-radius: 16px; overflow: hidden; background: #fde7f3; flex-shrink: 0; }
    .checkout-img img { width: 100%; height: 100%; object-fit: cover; }

    .item-info { display: flex; flex-direction: column; font-size: 15px; color: #4b5563; }
    .item-name { font-weight: 700; font-size: 17px; margin-bottom: 4px; }
    .item-amount { margin-bottom: 2px; }
    .item-subtotal { font-weight: 600; color: #3b82f6; }

    .checkout-total { font-size: 22px; font-weight: 700; display: flex; justify-content: space-between; margin-top: 20px; padding-top: 15px; border-top: 2px dashed #f3b4d3; }

    .btn-submit {
        padding: 16px 56px;
        border: none;
        border-radius: 16px;
        background: linear-gradient(135deg, #ec4899, #f472b6);
        color: #fff;
        font-size: 17px;
        font-weight: 700;
        cursor: pointer;
        align-self: center;
        box-shadow: 0 15px 35px rgba(236,72,153,0.4);
        transition: 0.2s;
    }
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 45px rgba(236,72,153,0.5);
    }
</style>
