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
        $this->errors = [];

        // Имя: обязательно, только буквы, пробелы, дефисы
        if (empty($this->data['name'])) {
            $this->errors['name'] = 'Имя обязательно';
        } elseif (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u', $this->data['name'])) {
            $this->errors['name'] = 'Имя должно содержать только буквы, пробелы или дефисы';
        }

        // Телефон: обязательно, 10-11 цифр, может быть с + в начале
        if (empty($this->data['phone'])) {
            $this->errors['phone'] = 'Телефон обязателен';
        } else {
            // Убираем все кроме цифр и плюса
            $phone = preg_replace('/[^\d+]/', '', $this->data['phone']);
            // Проверяем на формат: +7xxxxxxxxxx или 8xxxxxxxxxx или просто 10-11 цифр
            if (!preg_match('/^(\+7|8)?\d{10}$/', $phone)) {
                $this->errors['phone'] = 'Телефон должен содержать 10 цифр и может начинаться с +7 или 8';
            }
        }

        // Адрес: просто не пустой
        if (empty($this->data['address'])) {
            $this->errors['address'] = 'Адрес обязателен';
        }

        // Email: обязательно и валидный email
        if (empty($this->data['email'])) {
            $this->errors['email'] = 'Email обязателен';
        } elseif (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Некорректный email';
        }
        if (empty($this->data['payment'])) {
            $this->errors['payment'] = 'Выберите способ оплаты';
        }


        return $this->errors;
    }
}