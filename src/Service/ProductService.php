<?php

namespace Service;

use Model\UserProduct;

class ProductService
{
    private UserProduct $userProductModel;
    public function __construct()
    {
        $this->userProductModel = new UserProduct();
    }
    public function addProduct($userId, $productId, $amount)
    {
        $cartItem = $this->userProductModel->getUserProduct($userId, $productId);

        if (!$cartItem) {
            $this->userProductModel->setUserProduct($userId, $productId, $amount);
        } else {
            $this->userProductModel->updateUserProduct($amount, $userId, $productId);
        }
    }
}