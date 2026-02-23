<header class="cart-header">
    <h1>Добавление товара</h1>
</header>

<section class="cart">
    <div class="cart-wrapper">

        <form action="/add-product" method="POST" class="add-product-form">

            <div class="cart-item">
                <div class="cart-info">
                    <label for="product-id" class="cart-title">Product-id</label>
                    <input type="text" placeholder="Enter product-id" name="product-id" id="product-id" required>
                    <div class="error-msg"><?php echo $errors['product-id'] ?? ''; ?></div>
                </div>
            </div>

            <div class="cart-item">
                <div class="cart-info">
                    <label for="amount" class="cart-title">Amount</label>
                    <input type="text" placeholder="Enter amount" name="amount" id="amount" required>
                    <div class="error-msg"><?php echo $errors['amount'] ?? ''; ?></div>
                </div>
            </div>

            <div class="cart-summary">
                <button type="submit" class="cart-btn">
                    <span class="cart-btn-icon">🛒</span> Добавить в корзину
                </button>
            </div>

        </form>

    </div>
</section>

<style>
    body {
        font-family: "Inter", "Segoe UI", sans-serif;
        background: linear-gradient(135deg, #f5e9f2 0%, #faf7f9 100%);
        padding: 40px 20px;
        color: #1f2937;
    }

    .cart-header {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
        text-align: center;
    }

    .cart-header h1 {
        font-size: 34px;
        font-weight: 800;
        color: #2d2a32;
    }

    .cart-wrapper {
        max-width: 650px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 22px;
        padding: 26px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.08);
    }

    .add-product-form .cart-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 14px 18px;
        border-radius: 16px;
        background: #f6edf3;
        margin-bottom: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .add-product-form .cart-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(167,139,250,0.15);
    }

    .add-product-form input[type="text"] {
        width: 100%;
        padding: 14px 16px;
        border-radius: 12px;
        border: 1px solid #d8c9d3;
        background: #fff;
        font-size: 16px;
    }

    .add-product-form input[type="text"]:focus {
        outline: none;
        border-color: #8b5cf6;
        box-shadow: 0 0 6px rgba(139,92,246,0.25);
    }

    .cart-title {
        font-weight: 700;
        font-size: 16px;
        color: #2d2a32;
    }

    .error-msg {
        color: #ef4444;
        font-size: 14px;
        margin-top: 4px;
    }

    .cart-summary {
        display: flex;
        justify-content: center;
        margin-top: 24px;
    }

    .cart-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 16px 46px;
        border: none;
        border-radius: 16px;
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        color: #fff;
        font-size: 17px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 10px 24px rgba(139,92,246,0.35);
        transition: 0.2s;
    }

    .cart-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(139,92,246,0.45);
    }

    .cart-btn-icon {
        font-size: 18px;
    }

    /* ===== АДАПТИВ ===== */
    @media (max-width: 768px) {
        body {
            padding: 24px 14px;
        }

        .cart-header h1 {
            font-size: 26px;
        }

        .cart-wrapper {
            padding: 20px;
            border-radius: 18px;
        }

        .add-product-form .cart-item {
            padding: 12px 14px;
        }

        .cart-btn {
            width: 100%;
            padding: 16px;
            font-size: 16px;
        }
    }

    @media (max-width: 420px) {
        .cart-header h1 {
            font-size: 22px;
        }

        .cart-title {
            font-size: 15px;
        }
    }
</style>
