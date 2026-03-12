<?php

namespace Request;

class LoginRequest extends Request
{
    public function getPassword(): string
    {
        return $this->data['psw'] ?? '';
    }
    public function getLogin(): string
    {
        return $this->data['login'] ?? '';
    }

    public function loginValidate(): array
    {

        $this-> errors = [];

        if (empty($this->data['login'])) $this->errors['login'] = 'Введите email';
        if (empty($this->data['psw'])) $this->errors['psw'] = 'Введите пароль';

        return $this->errors;
    }
}