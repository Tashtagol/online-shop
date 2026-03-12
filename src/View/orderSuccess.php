<?php
$orderId = $orderId ?? null;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заказ оформлен</title>

    <!-- Авторедирект через 10 секунд -->
    <meta http-equiv="refresh" content="10;url=/catalog">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Inter", "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #fde7f3 0%, #fff5f8 100%);
            min-height: 100vh;
            padding: 40px 20px;
            color: #1f2937;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .success-header {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .success-header h1 {
            font-size: 34px;
            font-weight: 800;
            color: #2d2a32;
            text-align: center;
        }

        .success-wrapper {
            max-width: 700px;
            margin: 0 auto;
        }

        .success-card {
            background: #ffffff;
            border-radius: 22px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .success-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .success-card h2 {
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: 700;
            color: #2d2a32;
        }

        .order-number {
            font-size: 18px;
            margin-bottom: 12px;
            color: #ec4899;
        }

        .success-text {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .redirect-text {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 30px;
        }

        .redirect-text span {
            font-weight: 700;
            color: #ec4899;
        }

        .btn-back {
            display: inline-block;
            padding: 16px 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, #ec4899, #f472b6);
            color: #fff;
            font-size: 17px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 15px 35px rgba(236,72,153,0.4);
            transition: 0.2s;
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 45px rgba(236,72,153,0.5);
        }
    </style>
</head>
<body>

<header class="success-header">
    <h1>✅ Заказ оформлен!</h1>
</header>

<section class="success">
    <div class="success-wrapper">

        <div class="success-card">
            <div class="success-icon">💖</div>

            <h2>Спасибо за ваш заказ!</h2>

            <?php if($orderId): ?>
                <p class="order-number">
                    Номер заказа: <strong>№<?= htmlspecialchars($orderId) ?></strong>
                </p>
            <?php endif; ?>

            <p class="success-text">
                Мы получили ваш заказ и уже начали его обработку.
            </p>

            <p class="redirect-text">
                Автоматический переход в каталог через
                <span id="countdown">10</span> сек.
            </p>

            <a href="/catalog" class="btn-back">Вернуться в каталог сейчас</a>
        </div>

    </div>
</section>

<script>
    let seconds = 10;
    const countdown = document.getElementById('countdown');

    const interval = setInterval(() => {
        seconds--;
        countdown.textContent = seconds;

        if (seconds <= 0) {
            clearInterval(interval);
        }
    }, 1000);
</script>

</body>
</html>
