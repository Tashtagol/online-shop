<?php $total = 0; ?>

<header class="cart-header">
    <h1>🛒 Ваша корзина</h1>
</header>

<section class="cart">
    <div class="cart-wrapper">

        <?php foreach ($products as $product):
            $subtotal = $product['price'] * $product['amount'];
            $total += $subtotal;
            ?>
            <div class="cart-item" data-product-id="<?= $product['id']; ?>">
                <div class="cart-img">
                    <img src="<?= $product['viewurl']; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                </div>

                <div class="cart-info">
                    <div class="cart-title"><?= htmlspecialchars($product['name']); ?></div>
                    <div class="cart-desc"><?= htmlspecialchars($product['description']); ?></div>
                </div>

                <div class="cart-right">
                    <div class="cart-price"><?= number_format($subtotal,2); ?> ₽</div>

                    <div class="cart-qty">
                        <button type="button" class="qty-minus">−</button>
                        <span class="qty-value"><?= $product['amount']; ?></span>
                        <button type="button" class="qty-plus">+</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="cart-summary">
            <div class="cart-total">
                <span>Общая сумма:</span>
                <strong id="cart-total"><?= number_format($total,2); ?> ₽</strong>
            </div>

            <form action="/order" method="post">
                <button class="cart-btn" type="submit">Оформить заказ</button>
            </form>
        </div>
    </div>
</section>

<style>
    /* стили корзины — оставляем как у тебя */
    .cart-wrapper { max-width:950px; margin:0 auto; background:#fff; border-radius:22px; padding:26px; box-shadow:0 20px 50px rgba(0,0,0,0.1); }
    .cart-item { display:grid; grid-template-columns:120px 1fr 180px; gap:22px; padding:18px 0; border-bottom:1px solid #f1cfe0; align-items:center; }
    .cart-img { width:120px; height:120px; border-radius:16px; background:#fce7f3; display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .cart-img img { width:100%; height:100%; object-fit:contain; }
    .cart-title { font-size:18px; font-weight:700; margin-bottom:6px; }
    .cart-desc { font-size:15px; line-height:1.6; color:#4b5563; max-width:520px; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; }
    .cart-right { text-align:right; }
    .cart-price { font-size:20px; font-weight:800; color:#3b82f6; margin-bottom:12px; }
    .cart-qty { display:inline-flex; align-items:center; gap:10px; border:1px solid #f3b4d3; border-radius:12px; padding:6px 10px; background:#fff; }
    .cart-qty button { width:26px; height:26px; border:none; border-radius:8px; background:#fde2f0; cursor:pointer; font-size:16px; }
    .cart-qty button:hover { background:#fbcfe8; }
    .qty-value { min-width:22px; text-align:center; font-size:15px; font-weight:600; }
    .cart-summary { display:flex; justify-content:space-between; align-items:center; margin-top:40px; padding-top:20px; border-top:2px dashed #f3b4d3; }
    .cart-total { font-size:22px; }
    .cart-total strong { font-size:26px; color:#3b82f6; }
    .cart-btn { padding:16px 56px; border:none; border-radius:16px; background:linear-gradient(135deg,#ec4899,#f472b6); color:#fff; font-size:17px; font-weight:700; cursor:pointer; box-shadow:0 15px 35px rgba(236,72,153,0.4); }
    .cart-btn:hover { transform:translateY(-3px); box-shadow:0 20px 45px rgba(236,72,153,0.5); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', ()=>{
        function updateCart(productId, newAmount){
            fetch('/cart', {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body:JSON.stringify({product_id:productId, amount:newAmount})
            })
                .then(res => res.json())
                .then(data => {
                    if(data.success){
                        const item = document.querySelector(`.cart-item[data-product-id='${productId}']`);
                        item.querySelector('.qty-value').textContent = newAmount;
                        item.querySelector('.cart-price').textContent = (data.subtotal).toLocaleString('ru-RU', {minimumFractionDigits:2}) + ' ₽';
                        document.getElementById('cart-total').textContent = (data.total).toLocaleString('ru-RU', {minimumFractionDigits:2}) + ' ₽';
                    } else {
                        alert('Ошибка обновления корзины');
                    }
                })
                .catch(()=>alert('Ошибка сети'));
        }

        document.querySelectorAll('.cart-item').forEach(item=>{
            const productId = item.dataset.productId;
            const minus = item.querySelector('.qty-minus');
            const plus = item.querySelector('.qty-plus');
            const qty = item.querySelector('.qty-value');

            minus.addEventListener('click', ()=>{
                let val = parseInt(qty.textContent);
                if(val > 1) updateCart(productId, val-1);
            });

            plus.addEventListener('click', ()=>{
                let val = parseInt(qty.textContent);
                updateCart(productId, val+1);
            });
        });
    });
</script>