<?php

namespace Request;

class OrderRequest extends Request
{
    public function getNumber()
    {
        return $this->data['address'];
    }
    public function getName()
    {
        return $this->data['name'];
    }
    public function getPhone()
    {
        return $this->data['phone'];
    }
    public function getEmail()
    {
        return $this->data['email'];
    }
    public function validate(): array
    {
        $this-> errors = [];

        if (empty($this->data['name'])) $this->errors['name'] = 'Имя обязательно';
        if (empty($this->data['phone'])) $this->errors['phone'] = 'Телефон обязателен';
        if (empty($this->data['address'])) $this->errors['address'] = 'Адрес обязателен';
        if (empty($this->data['email']) || !filter_var($this->data['email'], FILTER_VALIDATE_EMAIL))
            $this->errors['email'] = 'Некорректный email';

        return $this->errors;
    }
}