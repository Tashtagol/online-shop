<header class="cart-header">
    <h1>🛒 Ваша корзина</h1>
</header>

<section class="cart">
    <div class="cart-wrapper">

        <button id="clear-cart-btn" class="btn-clear">🗑️ Очистить корзину</button>

        <?php $total = 0; ?>
        <div class="cart-items">
            <?php foreach ($orderProducts as $product):
                $subtotal = $product->getPrice() * $product->getAmount();
                $total += $subtotal;
                ?>
                <div class="cart-item" data-product-id="<?= $product->getId(); ?>">
                    <div class="cart-img">
                        <img src="<?= $product->getViewUrl(); ?>" alt="<?= htmlspecialchars($product->getName()); ?>">
                    </div>

                    <div class="cart-info">
                        <div class="cart-title"><?= htmlspecialchars($product->getName()); ?></div>
                        <div class="cart-desc"><?= htmlspecialchars($product->getDescription()); ?></div>
                    </div>

                    <div class="cart-right">
                        <div class="cart-price">
                            <?= number_format($subtotal, 2, ',', ' ') ?> ₽
                        </div>

                        <div class="cart-qty">
                            <button type="button" class="qty-minus">−</button>
                            <span class="qty-value"><?= $product->getAmount(); ?></span>
                            <button type="button" class="qty-plus">+</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <div class="cart-total">
                <span>Общая сумма:</span>
                <strong id="cart-total"><?= number_format($total, 2, ',', ' ') ?> ₽</strong>
            </div>

            <form action="/order" method="post">
                <button class="cart-btn" type="submit">Оформить заказ</button>
            </form>
        </div>
    </div>
</section>

<!-- ================= MODAL ================= -->
<div id="confirmModal" class="modal hidden">
    <div class="modal-box">
        <h3>Очистка корзины</h3>
        <p>Вы уверены, что хотите удалить все товары из корзины?</p>

        <div class="modal-actions">
            <button id="cancelClear" class="btn-cancel">Нет</button>
            <button id="confirmClear" class="btn-confirm">Да, очистить</button>
        </div>
    </div>
</div>

<style>
    .cart-wrapper {
        max-width: 950px;
        margin: 0 auto;
        background: #fff;
        border-radius: 22px;
        padding: 26px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
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

    .cart-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .cart-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .cart-desc {
        font-size: 15px;
        color: #4b5563;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .cart-right {
        text-align: right;
    }

    .cart-price {
        font-size: 20px;
        font-weight: 800;
        color: #3b82f6;
        margin-bottom: 12px;
    }

    .cart-qty {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #f3b4d3;
        border-radius: 12px;
        padding: 6px 10px;
    }

    .cart-qty button {
        width: 26px;
        height: 26px;
        border: none;
        border-radius: 8px;
        background: #fde2f0;
        cursor: pointer;
    }

    .qty-value {
        min-width: 22px;
        text-align: center;
        font-weight: 600;
    }

    .cart-summary {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
        border-top: 2px dashed #f3b4d3;
        padding-top: 20px;
    }

    .cart-total strong {
        font-size: 26px;
        color: #3b82f6;
    }

    .cart-btn {
        padding: 16px 56px;
        border: none;
        border-radius: 16px;
        background: linear-gradient(135deg, #ec4899, #f472b6);
        color: #fff;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-clear {
        margin-bottom: 20px;
        padding: 10px 20px;
        background: #f87171;
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 700;
    }

    /* ========== MODAL ========== */
    .modal {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
    }

    .modal.hidden {
        display: none;
    }

    .modal-box {
        background: #fff;
        padding: 25px;
        border-radius: 16px;
        width: 320px;
        text-align: center;
    }

    .modal-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .btn-cancel {
        background: #e5e7eb;
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        cursor: pointer;
    }

    .btn-confirm {
        background: #ef4444;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        cursor: pointer;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        // ================== UPDATE ITEM ==================
        function updateCart(productId, newAmount) {
            fetch('/cart', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    product_id: productId,
                    amount: newAmount,
                    source: 'cart'
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return alert('Ошибка');

                    const item = document.querySelector(`.cart-item[data-product-id='${productId}']`);

                    if (item) {
                        item.querySelector('.qty-value').textContent = newAmount;
                        item.querySelector('.cart-price').textContent =
                            data.subtotal.toLocaleString('ru-RU', { minimumFractionDigits: 2 }) + ' ₽';
                    }

                    document.getElementById('cart-total').textContent =
                        data.total.toLocaleString('ru-RU', { minimumFractionDigits: 2 }) + ' ₽';

                    if (newAmount === 0 && item) item.remove();
                });
        }

        document.querySelectorAll('.cart-item').forEach(item => {
            const id = item.dataset.productId;
            const qty = item.querySelector('.qty-value');

            item.querySelector('.qty-minus').onclick = () => {
                let val = parseInt(qty.textContent);
                updateCart(id, val > 1 ? val - 1 : 0);
            };

            item.querySelector('.qty-plus').onclick = () => {
                let val = parseInt(qty.textContent);
                updateCart(id, val + 1);
            };
        });

        // ================== MODAL CLEAR CART ==================
        const modal = document.getElementById('confirmModal');
        const openBtn = document.getElementById('clear-cart-btn');
        const cancelBtn = document.getElementById('cancelClear');
        const confirmBtn = document.getElementById('confirmClear');

        openBtn.onclick = () => modal.classList.remove('hidden');

        cancelBtn.onclick = () => modal.classList.add('hidden');

        confirmBtn.onclick = async () => {
            const res = await fetch('/cart/clear', { method: 'POST' });
            const data = await res.json();

            if (data.success) {
                document.querySelector('.cart-items').innerHTML = '<p>Корзина пуста</p>';
                document.getElementById('cart-total').textContent = '0 ₽';
            }

            modal.classList.add('hidden');
        };

    });
</script>