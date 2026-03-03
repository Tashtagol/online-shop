<?php
namespace Request;

class RegistrateRequest extends Request
{
    // Получение имени
    public function getName(): ?string
    {
        return $this->data['Name'] ?? null;
    }

    // Получение email
    public function getEmail(): ?string
    {
        return $this->data['email'] ?? null;
    }

    // Получение пароля
    public function getPassword(): ?string
    {
        return $this->data['psw'] ?? null;
    }

    // Валидация формы регистрации
    public function registrationValidate(array $data = null): array
    {
        if ($data === null) {
            $data = $this->data; // используем данные из запроса
        }

        $errors = [];

        // Проверка имени
        if (empty($data['Name'])) {
            $errors['Name'] = 'Имя обязательно';
        }

        // Проверка email
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный email';
        }

        // Проверка пароля
        if (empty($data['psw']) || strlen($data['psw']) < 5) {
            $errors['psw'] = 'Пароль должен быть минимум 5 символов';
        }

        // Проверка совпадения пароля
        if (($data['psw'] ?? null) !== ($data['psw-repeat'] ?? null)) {
            $errors['psw-repeat'] = 'Пароли не совпадают';
        }

        return $errors;
    }
}