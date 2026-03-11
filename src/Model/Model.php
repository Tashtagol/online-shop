<?php
namespace Model;

use PDO;
use PDOException;

class Model
{
    protected static PDO $pdo;

    public static function getPDO(): PDO
    {
        if (!isset(self::$pdo)) {
            self::$pdo = new PDO('pgsql:host=postgres_db;dbname=mydb', 'yonateiko', 'pass');
        }
        return self::$pdo;
    }
}