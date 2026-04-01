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
        return (int)($this->data['product_id'] ?? 0);
    }
    public function getSource(): string
    {
        return $this->data['source'] ?? 'catalog';
    }

    public function validate(): array
    {
        $this->errors = [];

        $productIdRaw = $this->data['product_id'] ?? '';

        if ($productIdRaw === '') {
            $this->errors['product_id'] = 'Требуется ID товара';
        } elseif (!ctype_digit($productIdRaw)) {
            $this->errors['product_id'] = 'ID товара должен содержать только цифры';
        } elseif (strlen($productIdRaw) > 1 && $productIdRaw[0] === '0') {
            $this->errors['product_id'] = 'ID товара не может начинаться с нуля';
        } else {
            $productModel = new Product();
            $product = $productModel->getProductById((int)$productIdRaw);

            if (!$product) {
                $this->errors['product_id'] = 'Товар с таким ID не найден';
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