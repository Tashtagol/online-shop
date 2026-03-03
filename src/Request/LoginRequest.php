<?php

namespace Request;

class LoginRequest extends Request
{
    public function getPassword(): string
    {
        return $this->data['Password'] ?? '';
    }
    public function getLogin(): string
    {
        return $this->data['login'] ?? '';
    }

    public function loginValidate(array $data = null): array
    {
        if ($data === null) {
            $data = $this->data;
        }

        $errors = [];

        if (empty($data['login'])) $errors['login'] = 'Введите email';
        if (empty($data['password'])) $errors['password'] = 'Введите пароль';

        return $errors;
    }
}