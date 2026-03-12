<?php

namespace Service;

use Model\UserProduct;
use Model\Product;

class ProductService
{

    public function addProduct($userId, $productId, $amount)
    {
        $cartItem = UserProduct::getUserProduct($userId, $productId);

        if (!$cartItem) {
            UserProduct::setUserProduct($userId, $productId, $amount);
        } else {
            UserProduct::updateUserProduct($amount, $userId, $productId);
        }
    }
    public function getAllProducts(): array
    {
        return Product::getAllProducts();
    }
}