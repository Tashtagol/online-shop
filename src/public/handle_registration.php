<?php

$name = $_POST['Name'];
$email = $_POST['email'];
$password = $_POST['psw'];
$passwordRepeat = $_POST['psw-repeat'];

$errors = [];

if (isset($_POST['Name'])){
    $name = $_POST['Name'];
    if (empty($name)) {
        $errors['Name'] = 'Имя не должно быть пустым';
    } elseif (strlen($name) < 3) {
        $errors['Name'] = 'Имя должно быть больше 3-х символов';
    }
} else {
    $errors['Name'] = 'input name required';
}

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    if (empty($email)) {
        $errors['email'] = 'Email не должен быть пустым';
    } elseif (!str_contains($email, '@')) {
        $errors['email'] = 'Email должен содержать символ @';
    }
} else {
    $errors['email'] = 'input email required';
}

if (isset($_POST['psw'])) {
    $password = $_POST['psw'] ?? null;
    if (empty($password)) {
        $errors['psw'] = 'Пароль не должен быть пустым';
    } elseif (strlen($password) < 5) {
        $errors['psw'] = 'Пароль должен быть более 5-ти символов';
    } elseif (is_numeric($password)) {
        $errors ['psw'] = 'Пароль не должен состоять только из цифр';
    } elseif ($password === strtolower ($password) || $password === strtoupper($password)) {
        $errors ['psw'] = 'Пароль должен содержать заглавные и строчные буквы';
    }
} else {
    $errors['psw'] = 'input password required';
}

if (isset($_POST['psw-repeat'])) {
    $passwordRepeat= $_POST['psw-repeat'];
    if (empty($passwordRepeat)) {
        $errors['psw-repeat'] = 'Повторный пароль не должен быть пустым';
    } elseif ($password !== $passwordRepeat) {
        $errors['psw-repeat'] = 'Повторный пароль не совпадает с паролем';
    }
}
if (empty($errors)) {
    $pdo = new PDO('pgsql:host=postgres_db;dbname=mydb', 'yonateiko', 'pass');
    $stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (:name, :email, :password)");

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hash]);


}

require_once './get_registration.php';