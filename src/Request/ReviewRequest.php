<?php

namespace Request;

class ReviewRequest extends Request
{
    protected array $errors = [];

    public function getRating(): ?int
    {
        return isset($this->data['rating']) && $this->data['rating'] !== ''
            ? (int)$this->data['rating']
            : null;
    }

    public function getComment(): ?string
    {
        return isset($this->data['comment']) && $this->data['comment'] !== ''
            ? trim($this->data['comment'])
            : null;
    }

    public function validate(): array
    {
        if (!$this->isPost()) {
            return [];
        }

        $this->errors = [];

        /* =========================
         * RATING VALIDATION
         * ========================= */
        $rating = $this->getRating();

        if ($rating === null) {
            $this->errors['rating'] = 'Выберите рейтинг';
        } elseif ($rating < 1 || $rating > 5) {
            $this->errors['rating'] = 'Рейтинг должен быть от 1 до 5';
        }

        /* =========================
         * COMMENT VALIDATION
         * ========================= */
        $comment = $this->getComment();

        if ($comment === null || $comment === '') {
            $this->errors['comment'] = 'Введите комментарий';
        } else {

            // 1. Минимальная длина
            if (mb_strlen($comment) < 2) {
                $this->errors['comment'] = 'Минимум 2 символа';
            }

            // 2. Максимальная длина (защита от спама)
            elseif (mb_strlen($comment) > 1000) {
                $this->errors['comment'] = 'Максимум 1000 символов';
            }

            // 3. НЕЛЬЗЯ только цифры
            elseif (preg_match('/^\d+$/', $comment)) {
                $this->errors['comment'] = 'Комментарий не может состоять только из цифр';
            }

            // 4. НЕЛЬЗЯ чистый мусор типа "1111aaa111" (минимальная логика)
            elseif (!preg_match('/[a-zA-Zа-яА-Я]/u', $comment)) {
                $this->errors['comment'] = 'Комментарий должен содержать текст';
            }

            // 5. НЕЛЬЗЯ слишком много повторяющихся символов
            elseif (preg_match('/(.)\1{10,}/u', $comment)) {
                $this->errors['comment'] = 'Комментарий содержит слишком много повторов';
            }
        }
        return $this->errors;
    }
}