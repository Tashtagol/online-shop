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
        parent::__construct();
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public static function createFromData(array $data): self
    {
        return new self(
            (int)$data['id'],
            $data['name'],
            $data['email'],
            $data['password']
        );
    }

    public function create(string $name, string $email, string $password): self
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (name, email, password)VALUES (:name, :email, :password)RETURNING id, name, email, password");

        $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return self::createFromData($data);
    }

    public function getByEmail(string $email): ?self
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? self::createFromData($data) : null;
    }

    public function getByLogin(string $login): ?self
    {
        return $this->getByEmail($login);
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
}