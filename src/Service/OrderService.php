<?php

namespace Service;

use Model\Order;
use Model\Product;
use Model\UserProduct;
use DTO\CreateOrderDTO;


class OrderService
{
    private Order $order;
    private UserProduct $userProductModel;
    private Product $productModel;
    public function __construct()
    {
        $this->order = new Order();
        $this->userProductModel = new UserProduct();
        $this->productModel = new Product();
    }
    public function create (CreateOrderDTO $orderDTO)
    {
        $order = new Order(
            $orderDTO->getUserId(),
            $orderDTO->getName(),
            $orderDTO->getEmail(),
            $orderDTO->getAddress(),
            $orderDTO->getPhone()
        );
        $userProducts = $this->userProductModel->getUserIdCart($orderDTO->getUserId());
        if (empty($userProducts)) {
            return null;
        }

        $productIds = array_map(fn($item) => $item->product_id, $userProducts);
        $products = $this->productModel->getProductsByIds($productIds);

        $orderItems = [];
        foreach ($userProducts as $item) {
            $pid = $item->product_id;

            if (isset($products[$pid])) {
                $price = $products[$pid]->getPrice();

                $orderItems[] = ['product_id' => $pid, 'amount' => $item->amount, 'price' => $price];
            }
        }

        $order->saveOrder();
        $order->saveOrderProductsBulk($orderItems);
        $this->userProductModel->clearCart($orderDTO->getUserId());
        return $order;

    }

    public function getCartData(int $userId): array
    {
        $userProducts = $this->userProductModel->getUserIdCart($userId);

        $orderItems = [];
        $total = 0;

        if (!empty($userProducts)) {
            $productIds = array_map(fn($item) => $item->product_id, $userProducts);
            $products = $this->productModel->getProductsByIds($productIds);

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