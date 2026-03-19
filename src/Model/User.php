<?php
namespace Model;

use PDO;

class User extends Model
{
    private ?int $id = null;
    private string $name;
    private string $email;
    private string $password;


    public static function createFromData(array $data): self
    {
        $user = new self();
        $user->id = (int)$data['id'];
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];

        return $user;
    }

    public static function create(string $name, string $email, string $password): self
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = self::getPDO()->prepare("INSERT INTO users (name, email, password)  VALUES (:name, :email, :password)  RETURNING id, name, email, password");

        $stmt->execute(['name' => $name, 'email' => $email, 'password' => $passwordHash]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return self::createFromData($data);
    }

    public static function getByEmail(string $email): ?self
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::createFromData($data) : null;
    }

    public static function getById(int $userId): ?self
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::createFromData($data) : null; // Аналогично, если данные есть, создаем объект
    }


    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
}