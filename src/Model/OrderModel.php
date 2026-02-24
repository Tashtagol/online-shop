<?php
namespace Model;
use PDO;
class OrderProduct extends Model
{
    public int $product_id;
    public int $order_id;
    public int $amount;
    public float $price;
    public string $name;
    public string $vieUrl;

    public function __construct(array $data)
    {
        $this->product_id = (int)($data['product_id'] ?? 0);
        $this->order_id = (int)($data['order_id'] ?? 0);
        $this->amount = (int)($data['amount'] ?? 0);
        $this->price = (float)($data['price'] ?? 0);
        $this->name = $data['name'] ?? '';
        $this->vieUrl = $data['vieUrl'] ?? '';
    }
}