<?php
namespace Service;

use Model\Product;
use Model\UserProduct;

class CartService
{

    public function getCart(int $userId): array
    {
        $userProducts = UserProduct::getUserIdCart($userId);
        if (empty($userProducts)) return [];

        $productIds = array_map(fn($item) => $item->product_id, $userProducts);
        $products = Product::getProductsByIds($productIds);

        $result = [];
        foreach ($userProducts as $item) {
            $pid = $item->product_id;
            if (isset($products[$pid])) {
                $result[] = [
                    'id' => $pid,
                    'name' => $products[$pid]->getName(),
                    'price' => $products[$pid]->getPrice(),
                    'amount' => $item->amount,
                    'sum' => $products[$pid]->getPrice() * $item->amount,
                    'viewurl' => $products[$pid]->getVieUrl(),
                    'description' => $products[$pid]->getDescription()
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

    public function add(int $userId, int $productId, int $amount = 1)
    {
        $existing = UserProduct::getUserProduct($userId, $productId);

        if ($existing) {
            UserProduct::updateUserProduct($amount, $userId, $productId);
        } else {
            UserProduct::setUserProduct($userId, $productId, $amount);
        }

        return true;
    }

    public function update(int $userId, int $productId, int $amount)
    {
        if ($amount <= 0) {
            UserProduct::clearCartItem($userId, $productId);
        } else {
            UserProduct::updateUserProduct($amount, $userId, $productId);
        }

        return true;
    }
}