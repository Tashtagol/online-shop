<?php
namespace Model;

use PDO;
use PDOException;

class Model
{
    protected PDO $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO(
                'pgsql:host=postgres_db;dbname=mydb',
                'yonateiko',
                'pass'
            );

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе: " . $e->getMessage());
        }
    }
}