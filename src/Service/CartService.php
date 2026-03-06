<?php

namespace Service;

use Model\Product;
use Model\UserProduct;

class CartService
{
    private UserProduct $userProductModel;
    private Product $productModel;
    public function __construct()
    {
        $this->userProductModel = new UserProduct();
        $this->productModel = new Product();
    }
    public function getCart(int $userId): array
    {
        $userProducts = $this->userProductModel->getUserIdCart($userId);

        if (empty($userProducts)) {
            return [];
        }

        $productIds = array_map(fn($item) => $item->product_id, $userProducts);
        $products = $this->productModel->getProductsByIds($productIds);

        $result = [];

        foreach ($userProducts as $item) {
            $pid = $item->product_id;

            if (isset($products[$pid])) {
                // Добавляем description товара в массив
                $result[] = [
                    'id' => $pid,
                    'name' => $products[$pid]->getName(),
                    'price' => $products[$pid]->getPrice(),
                    'amount' => $item->amount,
                    'sum' => $products[$pid]->getPrice() * $item->amount,
                    'viewurl' => $products[$pid]->getVieUrl(),
                    'description' => $products[$pid]->getDescription() // добавляем описание товара
                ];
            }
        }

        return $result;
    }
    public function getTotal(array $products): float
    {
        $total = 0;
        foreach ($products as $product) {
            $total += $product['price'] * $product['amount'];
        }
        return $total;
    }
}