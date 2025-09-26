<?php

$login = $_POST['login'];
$password = $_POST['password'];

$errors = [];
 if (isset($_POST['login'])) {
     $login = $_POST['login'];
 } else { $errors['login'] = 'login is required';
}

if (isset($_POST ['password'])) {
    $password = $_POST['password'];
} else {
    $errors['password'] = 'password is required';
}

if (empty($errors)) {
    $pdo = new PDO('pgsql:host=postgres_db;dbname=mydb', 'yonateiko', 'pass');

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :login');
    $stmt->execute(['login' => $login]);

    $data = $stmt->fetch();

    if ($data === false) {
        $errors['login'] = 'Неверный логин или пароль';
    } else {
        $passwordFromDB = $data['password'];
        if (password_verify ($password, $passwordFromDB)) {
            echo 'ok!';
        } else {
            $errors['password'] = 'Неверный логин или пароль';
        }
    }
}

require_once './get_login.php';