<?php
namespace Model;

use PDO;



class User extends Model
{
    private ?int $id = null;
    private string $name;
    private string $email;
    private string $password;

    public function __construct(
        ?int $id = null,
        string $name = '',
        string $email = '',
        string $password = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public static function createFromData(array $data): self
    {
        return new self((int)$data['id'], $data['name'], $data['email'], $data['password']);
    }

    public static  function create(string $name, string $email, string $password): self
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = self::getPDO()->prepare("INSERT INTO users (name, email, password)VALUES (:name, :email, :password)RETURNING id, name, email, password");

        $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password]);

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

    public static function getByLogin(string $login): ?self
    {
        return self::getByEmail($login);
    }
    public static function getById(string $userId): ?self
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::createFromData($data) : null;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
}