<?php

namespace Request;

class ReviewRequest extends Request
{
    public function getProductId(): ?int
    {
        return isset($this->data['product_id']) ? (int)$this->data['product_id'] : null;
    }

    public function getRating(): ?int
    {
        return isset($this->data['rating']) ? (int)$this->data['rating'] : null;
    }

    public function getComment(): ?string
    {
        return isset($this->data['comment']) ? trim($this->data['comment']) : null;
    }

    public function validate(): array
    {
        $this->errors = [];

        // --- Product ID ---
        $productId = $this->getProductId();
        if (!$productId || $productId < 1) {
            $this->errors['product_id'] = 'Неверный ID продукта';
        }

        // --- Rating ---
        $rating = $this->getRating();
        if ($rating === null) {
            $this->errors['rating'] = 'Рейтинг обязателен';
        } elseif (!in_array($rating, [1,2,3,4,5], true)) {
            $this->errors['rating'] = 'Рейтинг должен быть от 1 до 5';
        }

        // --- Comment ---
        $comment = $this->getComment();
        if ($comment === null || $comment === '') {
            $this->errors['comment'] = 'Комментарий обязателен';
        } elseif (strlen($comment) < 2) {
            $this->errors['comment'] = 'Комментарий слишком короткий (минимум 2 символа)';
        } elseif (strlen($comment) > 1000) {
            $this->errors['comment'] = 'Комментарий слишком длинный (максимум 1000 символов)';
        } elseif (ctype_digit(str_replace(' ', '', $comment))) {
            $this->errors['comment'] = 'Комментарий не может состоять только из цифр';
        }

        return $this->errors;
    }
}