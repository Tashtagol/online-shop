<?php
namespace Model;
use PDO;
class User extends Model
{
    public function create(string $name, string $email, string $password)
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (name,email,password) VALUES (:name, :email, :password)");
        $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password]);
    }
    public function getEmail(string $email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
    public function getLogin(string $login)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :login');
        $stmt->execute(['login' => $login]);
        return $stmt->fetch();
    }

    public function selectAllByUserId(int $userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user_product WHERE user_id = :id');
        $stmt->execute(['id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
