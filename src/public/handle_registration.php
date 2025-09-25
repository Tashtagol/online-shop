<?php

$name = $_GET['Name'];
$email = $_GET['email'];
$password = $_GET['psw'];
$passwordRepeat = $_GET ['psw-repeat'];

$pdo = new PDO('pgsql:host=postgres_db;dbname=mydb', 'yonateiko', 'pass');
$pdo -> exec (statement: 'INSERT INTO users (name,email,password) VALUES (name,email,password)');

$result = $pdo->query (query: "SELECT * FROM users");
print_r($result->fetchAll());
