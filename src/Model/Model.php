<?php
namespace Model;
use PDO;

class Model
{
    protected static PDO $pdo;

    protected static function getPdo(): PDO
    {
        if (!isset(self::$pdo)) {
            self::$pdo = new PDO('pgsql:host=postgres_db;dbname=mydb', 'yonateiko', 'pass');
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$pdo;
    }
}