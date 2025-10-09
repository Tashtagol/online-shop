<?php
session_start();

$userId = $_SESSION['user_id'];

if (!isset ($_SESSION['user_id'])) {
    header('Location: ./login');
    exit;
//if (!isset($_COOKIE['user_id'])) {
//header('Location: ./get_login.php');

} else {
    $pdo = new PDO('pgsql:host=postgres_db;dbname=mydb', 'yonateiko', 'pass');

    $stmt = $pdo->prepare("SELECT product_id, amount FROM user_product WHERE user_id = :userId");
    $stmt->execute(array('userId' => $userId));
    $userProductsData = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT name FROM users WHERE id = :Id");
    $stmt->execute(array('Id' => $userId));
    $userData = $stmt->fetch();

    $products = [];
    foreach ($userProductsData as $userProduct) {
        $productId = $userProduct['product_id'];
        $amount = $userProduct['amount'];

        // Получаем информацию о товаре
        $stmt = $pdo->prepare("SELECT name, price, viewurl, description FROM products WHERE id = :productId");
        $stmt->execute(array('productId' => $productId));
        $productData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($productData) {
            $productData['amount'] = $amount;  // Добавляем количество товара
            $products[] = $productData;  // Сохраняем в массив
        }
    }
}
$total = 0;

     foreach ($products as $product) {
    $total += $product['price'] * $product['amount'];
}
?>

<header>
    <h3>Ваша корзина</h3>
</header>
<main>
        </form>
    </section>

    <section class="checkout-details">
        <div class="checkout-details-inner">
            <div class="checkout-lists">
                <?php foreach ($products as $product): ?>
                <div class="card">
                    <div class="card-image"><img src="<?php echo $product['viewurl']; ?>" alt=""></div>
                    <div class="card-details">
                        <div class="card-name"><?php echo $product['name']; ?></div>
                        <div class="card-price"><?php echo $product['price']; ?></div>
                        <div class="card-description">
                            <?php echo ($product['description']); ?>
                        </div>

                        <div class="card-wheel">
                            <button>-</button>
                            <span><?= $product['amount'] ?></span>
                            <button>+</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <div class="checkout-total">
                <h6>Общая сумма</h6>
                <p><?= number_format($total, 2) ?> руб</p>
            </div>
                <div class="form-control-btn">
                    <button>Заказать</button>
                </div>
        </div>
    </section>

</main>
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    body {
        font-family: "Poppins", sans-serif;
        height: 100vh;
        width: 70%;
        margin: 0px auto;
        padding: 50px 0px 0px;
        color: #4E5150;


        header {

            height: 5%;
            margin-bottom: 30px;

            > h3 {
                font-size: 25px;
                color: #4E5150;
                font-weight: 500;
            }

        }

        main {
            height: 85%;
            display: flex;
            column-gap: 100px;

            .checkout-form  {
                width: 50%;

                form {

                    h6 {
                        font-size: 12px;
                        font-weight: 500;
                    }

                    .form-control  {
                        margin: 10px 0px;
                        position: relative;

                        label:not([for="checkout-checkbox"]) {
                            display: block;
                            font-size: 10px;
                            font-weight: 500;
                            margin-bottom: 2px;
                        }

                        input:not([type="checkbox"]) {
                            width: 100%;
                            padding: 10px 10px 10px 40px;
                            border-radius: 10px;
                            outline: none;
                            border: .2px solid #4e515085;
                            font-size: 12px;
                            font-weight: 700;

                            &::placeholder {
                                font-size: 10px;
                                font-weight: 500;
                            }
                        }

                        label[for="checkout-checkbox"] {
                            font-size: 9px;
                            font-weight: 500;
                            line-height: 10px;
                        }

                        > div {
                            position: relative;

                            span.fa {
                                position: absolute;
                                top: 50%;
                                left: 0%;
                                transform: translate(15px, -50%);
                            }
                        }
                    }

                    .form-group {
                        display: flex;
                        column-gap: 25px;
                    }

                    .checkbox-control {
                        display: flex;
                        align-items: center;
                        column-gap: 10px;
                    }

                    .form-control-btn {
                        display: flex;
                        align-items: center;
                        justify-content: flex-end;

                        button {
                            padding: 10px 25px;
                            font-size: 10px;
                            color: #fff;
                            background: #F2994A;
                            border: 0;
                            border-radius: 7px;
                            letter-spacing: .5px;
                            font-weight: 200;
                            cursor: pointer;
                        }
                    }
                }
            }

            .checkout-details {
                width: 40%;

                .checkout-details-inner {
                    background: #F2F2F2;
                    border-radius: 10px;
                    padding: 20px;


                            box-sizing: border-box; /* Чтобы паддинги и бордеры учитывались в размере */
                            display: flex;
                            flex-direction: column;
                            gap: 10px; /* Расстояние между элементами внутри карточки */
                        }

                        .card-image img {
                            width: 100%;
                            height: auto; /* Поддерживает пропорции изображения */
                            object-fit: cover; /* Обрезка изображения для сохранения пропорций, если оно слишком большое */
                            border-radius: 10px; /* Радиус для округления углов */
                        }

                        .card-details {
                            display: flex;
                            flex-direction: column;

                                .card-name {
                                    font-size: 30px;
                                    font-weight: 600;
                                }
                                .card-price {
                                    font-size: 25px;
                                    font-weight: 600;
                                    color: #F2994A;
                                    margin-top: 5px;

                                    span {
                                        color: #4E5150;
                                        text-decoration: line-through;
                                        margin-left: 15px;
                                    }
                                }
                                .card-wheel {
                                    margin-top: 17px;
                                    border: .2px solid #4e515085;
                                    width: 90px;
                                    padding: 8px 8px;
                                    border-radius: 10px;
                                    font-size: 12px;
                                    display: flex;
                                    justify-content: space-between;

                                    button {
                                        background: #E0E0E0;
                                        color: #828282;
                                        width: 15px;
                                        height: 15px;
                                        display: flex;
                                        justify-content: center;
                                        align-items: center;
                                        border: 0;
                                        cursor: pointer;
                                        border-radius: 3px;
                                        font-weight: 500;
                                    }
                                }
                            }
                        }
                    }

                    .checkout-shipping, .checkout-total {
                        display: flex;
                        font-size: 20px;
                        padding: 5px 0px;
                        border-top: 1px solid #BDBDBD;
                        justify-content: space-between;

                        p {
                            font-size: 25px;
                            font-weight: 700;
                        }
                    }
                }
            }
        }

        footer {

            height: 5%;
            color: #BDBDBD;
            display: -ms-grid;
            display: grid;
            place-items: center;
            font-size: 12px;

            a {
                text-decoration: none;
                color: inherit;
            }

        }

    }

    @media screen and (max-width: 1024px) {
        body {
            width: 80%;

            main {
                column-gap: 70px;
            }
        }
    }

    @media screen and (max-width: 768px) {
        body {
            width: 92%;

            main {
                flex-direction: column-reverse;
                height: auto;
                margin-bottom: 50px;

                .checkout-form {
                    width: 100%;
                    margin-top: 35px;
                }

                .checkout-details {
                    width: 100%;
                }
            }

            footer {
                height: 10%;
            }
        }

    }
    .form-control-btn {
        display: flex;
        justify-content: center; /* центрирует кнопку */
        margin-top: 20px;
    }

    .form-control-btn button {
        padding: 14px 40px;
        font-size: 14px;
        background-color: #F2994A;
        color: #fff;
        border: none;
        border-radius: 10px;
        cursor: p

</style>

