<?php

namespace Service;

use Model\UserProduct;
use Model\Product;

class ProductService
{

    public function getAllProducts(): array
    {
        return Product::getAllProducts();  // Вызов метода из модели
    }

    // Получаем продукт по ID
    public function getProductById(int $productId): ?Product
    {
        return Product::getProductById($productId);
    }

}