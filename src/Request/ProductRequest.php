<?php
namespace Request;

use Model\Product;

class ProductRequest extends Request
{
    public function getAmount(): int
    {
        return (int)($this->data['amount'] ?? 0);
    }

    public function getProductId(): int
    {
        return (int)($this->data['product-id'] ?? 0);
    }

    public function validate(): array
    {
        $this->errors = [];

        $productIdRaw = $this->data['product-id'] ?? '';

        if ($productIdRaw === '') {
            $this->errors['product-id'] = 'Требуется ID товара';
        } elseif (!ctype_digit($productIdRaw)) {
            $this->errors['product-id'] = 'ID товара должен содержать только цифры';
        } elseif (strlen($productIdRaw) > 1 && $productIdRaw[0] === '0') {
            $this->errors['product-id'] = 'ID товара не может начинаться с нуля';
        } else {
            $productModel = new Product();
            $product = $productModel->getProductById((int)$productIdRaw);

            if (!$product) {
                $this->errors['product-id'] = 'Товар с таким ID не найден';
            }
        }

        // Проверка amount
        $amountRaw = $this->data['amount'] ?? '';
        if ($amountRaw === '' || !ctype_digit($amountRaw) || (int)$amountRaw < 1) {
            $this->errors['amount'] = 'Количество должно быть положительным числом';
        }

        return $this->errors;
    }
}