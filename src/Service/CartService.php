<?php
namespace Service;

use Model\UserProduct;
use DTO\ProductItemDTO;

class CartService {
    public function getCart(int $userId): array {
        $items = UserProduct::getUserCartItems($userId);
        return array_map(fn($item) => new ProductItemDTO(
            (int)$item->product_id, $item->name, (float)$item->price,
            (int)$item->amount, $item->viewurl, $item->description
        ), $items);
    }

    public function getTotal(array $items): float {
        return array_reduce($items, fn($sum, $item) => $sum + $item->getSum(), 0.0);
    }

    public function addOrUpdateProductInCart(int $userId, int $productId, int $amount, string $source = 'catalog'): void
    {
        $cartProduct = UserProduct::getUserProduct($userId, $productId);

        if ($cartProduct) {
            $newAmount = ($source === 'catalog') ? $cartProduct->amount + $amount : $amount;
            UserProduct::updateUserProduct($newAmount, $userId, $productId);
        } else {
            UserProduct::addProductToCart($userId, $productId, $amount);
        }
    }
    public function getSubtotal(array $items, int $productId): float {
        foreach ($items as $item) {
            if ($item->getId() === $productId) {
                return $item->getSum();
            }
        }
        return 0.0;
    }
    public function getCount(array $items): int
    {
        return array_sum(array_map(fn($i) => $i->getAmount(), $items));
    }

}