<div class="container">
    <h3>Каталог товаров</h3>

    <div class="top-buttons">
        <a href="/cart" class="add-orders-btn">🛒 Моя корзина</a>
        <a href="/orders" class="my-orders-btn">📦 Мои заказы</a>
        <a href="/add-product" class="add-product-btn">Добавить товар</a>
    </div>

    <div class="product-grid">
        <?php if(!empty($products)): ?>
            <?php foreach($products as $product): ?>
                <div class="product-card" data-product-id="<?= $product->getId() ?>">
                    <div class="product-image">
                        <img src="<?= htmlspecialchars($product->getVieUrl()) ?>" alt="<?= htmlspecialchars($product->getName()) ?>">
                    </div>
                    <div class="product-info">
                        <h4 class="product-name"><?= htmlspecialchars($product->getName()) ?></h4>
                        <p class="product-description"><?= htmlspecialchars($product->getDescription()) ?></p>
                        <div class="product-price"><?= number_format($product->getPrice(),2,',',' ') ?> ₽</div>

                        <div class="quantity-selector">
                            <button type="button" class="qty-btn minus">−</button>
                            <input type="number" class="qty-input" value="1" min="1">
                            <button type="button" class="qty-btn plus">+</button>
                        </div>

                        <button class="btn-add ajax-add">Добавить в корзину</button>
                        <div class="add-msg">Добавлено!</div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Товары отсутствуют</p>
        <?php endif; ?>
    </div>
</div>

<style>
    body { font-family:"Inter","Segoe UI",sans-serif; background:#fde7f3; padding:40px 20px; }
    .container { max-width:950px; margin:0 auto; }
    h3 { text-align:center; margin-bottom:20px; color:#333; }

    .top-buttons { display:flex; justify-content:center; gap:15px; margin-bottom:30px; }
    .add-orders-btn, .add-product-btn, .my-orders-btn {
        padding:10px 20px;
        color:white;
        border-radius:5px;
        font-weight:bold;
        text-decoration:none;
    }
    .add-orders-btn { background:#F2994A; }
    .add-orders-btn:hover { background:#d87d2a; }
    .add-product-btn { background:#04AA6D; }
    .add-product-btn:hover { background:#039c64; }
    .my-orders-btn { background:#6366F1; }
    .my-orders-btn:hover { background:#4F46E5; }

    .product-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:25px; }
    .product-card { background:white; border-radius:22px; box-shadow:0 20px 50px rgba(0,0,0,0.1); overflow:hidden; display:flex; flex-direction:column; transition:transform 0.3s; }
    .product-card:hover { transform:translateY(-5px); }

    .product-image { width:100%; flex:1; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#fff0f5; border-radius:16px 16px 0 0; }
    .product-image img { width:100%; height:100%; object-fit:cover; display:block; }

    .product-info { display:flex; flex-direction:column; gap:10px; margin-top:0; padding:15px; }
    .product-name { font-size:18px; font-weight:700; }
    .product-description { font-size:14px; color:#4b5563; height:42px; overflow:hidden; text-overflow:ellipsis; }
    .product-price { font-size:18px; font-weight:600; color:#3b82f6; }

    .quantity-selector { display:flex; align-items:center; gap:5px; margin-top:10px; }
    .qty-btn { padding:5px 10px; background:#fde2f0; border:none; cursor:pointer; border-radius:8px; font-weight:bold; }
    .qty-btn:hover { background:#fbcfe8; }
    .qty-input { width:50px; text-align:center; border-radius:5px; border:1px solid #ccc; }

    .btn-add { margin-top:10px; background:#ec4899; color:white; border:none; padding:10px 15px; border-radius:12px; cursor:pointer; transition:0.2s; }
    .btn-add:hover { background:#f472b6; }

    .add-msg { font-size:13px; color:green; margin-top:5px; display:none; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Кнопки +/-
        document.querySelectorAll('.product-card').forEach(card => {
            const minus = card.querySelector('.minus');
            const plus = card.querySelector('.plus');
            const input = card.querySelector('.qty-input');

            minus.addEventListener('click', () => {
                let val = parseInt(input.value) || 1;
                if (val > 1) input.value = val - 1;
            });

            plus.addEventListener('click', () => {
                let val = parseInt(input.value) || 1;
                input.value = val + 1;
            });
        });

        // Ajax добавление
        document.querySelectorAll('.ajax-add').forEach(btn => {
            btn.addEventListener('click', async function() {
                const card = this.closest('.product-card');
                const productId = card.dataset.productId;
                const amount = parseInt(card.querySelector('.qty-input').value) || 1;
                const msg = card.querySelector('.add-msg');

                try {
                    const res = await fetch('/cart', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ product_id: productId, amount })
                    });

                    const data = await res.json();
                    if (res.ok && data.success) {
                        msg.style.display = 'block';
                        setTimeout(() => msg.style.display = 'none', 1000);
                    } else {
                        alert('Ошибка при добавлении в корзину');
                    }
                } catch (e) {
                    alert('Ошибка сети');
                }
            });
        });
    });
</script>