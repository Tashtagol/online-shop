<?php
namespace Request;

class RegistrateRequest extends Request
{
    public function getName(): ?string
    {
        return $this->data['Name'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->data['email'] ?? null;
    }

    public function getPassword(): ?string
    {
        return $this->data['psw'] ?? null;
    }

    public function getPasswordRepeat(): ?string
    {
        return $this->data['psw-repeat'] ?? null;
    }

    // Универсальная валидация формы регистрации
    public function registrationValidate(): array
    {
        $this->errors = [];

        // --- Имя ---
        $name = trim($this->getName());
        if (empty($name)) {
            $this->errors['Name'] = 'Имя обязательно';
        } elseif (strlen($name) < 2) {
            $this->errors['Name'] = 'Имя слишком короткое';
        } elseif (strlen($name) > 50) {
            $this->errors['Name'] = 'Имя слишком длинное';
        } elseif (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s-]+$/u', $name)) {
            $this->errors['Name'] = 'Имя содержит недопустимые символы';
        }

        // --- Email ---
        $email = trim($this->getEmail());
        if (empty($email)) {
            $this->errors['email'] = 'Email обязателен';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Некорректный email';
        }

        // --- Пароль ---
        $password = $this->getPassword();
        if (empty($password)) {
            $this->errors['psw'] = 'Пароль обязателен';
        } elseif (strlen($password) < 6) {
            $this->errors['psw'] = 'Пароль слишком короткий, минимум 6 символов';
        } elseif (strlen($password) > 50) {
            $this->errors['psw'] = 'Пароль слишком длинный';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $this->errors['psw'] = 'Пароль должен содержать хотя бы одну заглавную букву';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $this->errors['psw'] = 'Пароль должен содержать хотя бы одну строчную букву';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $this->errors['psw'] = 'Пароль должен содержать хотя бы одну цифру';
        } elseif (!preg_match('/[\W_]/', $password)) {
            $this->errors['psw'] = 'Пароль должен содержать хотя бы один спецсимвол';
        }

        // --- Повтор пароля ---
        $passwordRepeat = $this->getPasswordRepeat();
        if ($password !== $passwordRepeat) {
            $this->errors['psw-repeat'] = 'Пароли не совпадают';
        }

        return $this->errors;
    }
}