<?php

if (!isset($_COOKIE['user_id'])) {
    header('Location: ./get+login.php');
}
$products = [];

?>

<div class="container">
    <h3>Catalog</h3>
    <div class="card-deck">
        <?php foreach ($products as $product): ?>
        <div class="card text-center">
            <a href="#">
                <div class="card-header">
                    Hit!
                </div>
                <img class="card-img-top" src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjE0NTg5fQ&s=043d89cbf03cbdbbe8ed9f9e5e44ce6f" alt="Card image">
                <div class="card-body">
                    <p class="card-text text-muted"><?php echo $product['name']; ?> </p>
                    <a href="#"><h5 class="card-title">Very long item name</h5></a>
                    <div class="card-footer">
                        3000$
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
        <div class="card text-center">
            <a href="#">
                <div class="card-header">
                    Hit!
                </div>
                <img class="card-img-top" src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjE0NTg5fQ&s=043d89cbf03cbdbbe8ed9f9e5e44ce6f" alt="Card image">
                <div class="card-body">
                    <p class="card-text text-muted">Category name</p>
                    <a href="#"><h5 class="card-title">Very long item name</h5></a>
                    <div class="card-footer">
                        3000$
                    </div>
                </div>
            </a>
        </div>
        <div class="card text-center">
            <a href="#">
                <div class="card-header">
                    Hit!
                </div>
                <img class="card-img-top" src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjE0NTg5fQ&s=043d89cbf03cbdbbe8ed9f9e5e44ce6f" alt="Card image">
                <div class="card-body">
                    <p class="card-text text-muted">Category name</p>
                    <a href="#"><h5 class="card-title">Very long item name</h5></a>
                    <div class="card-footer">
                        3000$
                    </div>
                </div>
            </a>
        </div>
        <div class="card text-center">
            <a href="#">
                <div class="card-header">
                    Hit!
                </div>
                <img class="card-img-top" src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjE0NTg5fQ&s=043d89cbf03cbdbbe8ed9f9e5e44ce6f" alt="Card image">
                <div class="card-body">
                    <p class="card-text text-muted">Category name</p>
                    <a href="#"><h5 class="card-title">Very long item name</h5></a>
                    <div class="card-footer">
                        3000$
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    body {
        font-style: sans-serif;
    }

    a {
        text-decoration: none;
    }

    a:hover {
        text-decoration: none;
    }

    h3 {
        line-height: 3em;
    }

    .card {
        max-width: 16rem;
    }

    .card:hover {
        box-shadow: 1px 2px 10px lightgray;
        transition: 0.2s;
    }

    .card-header {
        font-size: 13px;
        color: gray;
        background-color: white;
    }

    .text-muted {
        font-size: 11px;
    }

    .card-footer{
        font-weight: bold;
        font-size: 18px;
        background-color: white;
    }
</style>
