<?php

namespace Service;

use Model\Model;
use Model\Order;
use Model\Product;
use Model\UserProduct;
use DTO\CreateOrderDTO;


class OrderService
{

    public function create(CreateOrderDTO $orderDTO)
    {
           $pdo=Model::getPDO();
            $userProducts = UserProduct::getUserIdCart($orderDTO->getUserId());
            if (empty($userProducts)) {
                return null;
            }
        try {
            $pdo->beginTransaction();
            $order = Order::create(
                $orderDTO->getUserId(),
                $orderDTO->getName(),
                $orderDTO->getEmail(),
                $orderDTO->getAddress(),
                $orderDTO->getPhone(),
                $orderDTO->getPayment()
            );

            $productIds = array_map(fn($item) => $item->product_id, $userProducts);
            $products = Product::getProductsByIds($productIds);

            $orderItems = [];
            foreach ($userProducts as $item) {
                $pid = $item->product_id;

                if (isset($products[$pid])) {
                    $price = $products[$pid]->getPrice();

                    $orderItems[] = ['product_id' => $pid, 'amount' => $item->amount, 'price' => $price];
                }
            }

            $order->saveProducts($orderItems);
            UserProduct::clearCart($orderDTO->getUserId());
            $pdo->commit();
            return $order;
        }
        catch
            (\PDOException $exception) {
                $pdo->rollBack();
                throw $exception;
            }

    }

    public function getCartData(int $userId): array
    {
        UserProduct::getUserIdCart($userId);

        $orderItems = [];
        $total = 0;

        if (!empty($userProducts)) {
            $productIds = array_map(fn($item) => $item->product_id, $userProducts);
            Product::getProductsByIds($productIds);

            foreach ($userProducts as $item) {
                $pid = $item->product_id;

                if (isset($products[$pid])) {
                    $price = $products[$pid]->getPrice();

                    $orderItems[] = [
                        'product_id' => $pid,
                        'name' => $products[$pid]->getName(),
                        'price' => $price,
                        'amount' => $item->amount,
                        'sum' => $price * $item->amount,
                        'viewurl' => $products[$pid]->getVieUrl()
                    ];

                    $total += $price * $item->amount;
                }
            }
        }

        return ['items' => $orderItems, 'total' => $total];
    }

}