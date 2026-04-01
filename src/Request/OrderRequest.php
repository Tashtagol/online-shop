<?php

namespace Request;

class OrderRequest extends Request
{
    private string $name;
    private string $phone;
    private string $address;
    private string $email;
    private string $payment;
    private ?string $number = null;

    // Ядро (App.php) передает HTTP-метод и массив данных (например, $_POST)
    public function __construct(string $method, array $data = [])
    {
         parent::__construct($method, $data);
        // Инициализируем свойства из переданного массива
        $this->name = $data['name'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->payment = $data['payment'] ?? '';
        $this->number = $data['number'] ?? null;

    }

    // Методы для получения значений
    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPayment(): string
    {
        return $this->payment;
    }
    public function getNumber(): ?string
    {
        return $this->number;
    }

    // Валидация данных
    public function validate(): array
    {
        if (!$this->isPost()) {
            return [];
        }

            // Очищаем ошибки перед новой валидацией
        $this->errors = [];


        // Проверка имени
        if (empty($this->name)) {
            $this->errors['name'] = 'Имя обязательно';
        } elseif (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u', $this->name)) {
            $this->errors['name'] = 'Имя должно содержать только буквы, пробелы или дефисы';
        }

        // Проверка телефона
        if (empty($this->phone)) {
            $this->errors['phone'] = 'Телефон обязателен';
        } else {
            $phone = preg_replace('/[^\d+]/', '', $this->phone);
            if (!preg_match('/^(\+7|8)?\d{10}$/', $phone)) {
                $this->errors['phone'] = 'Телефон должен содержать 10 цифр и может начинаться с +7 или 8';
            }
        }

        // Проверка адреса
        if (empty($this->address)) {
            $this->errors['address'] = 'Адрес обязателен';
        }

        // Проверка email
        if (empty($this->email)) {
            $this->errors['email'] = 'Email обязателен';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Некорректный email';
        }

        // Проверка способа оплаты
        if (empty($this->payment)) {
            $this->errors['payment'] = 'Выберите способ оплаты';
        }

        return $this->errors;
    }

    // Получить ошибки
    public function getErrors(): array
    {
        return $this->errors;
    }
}