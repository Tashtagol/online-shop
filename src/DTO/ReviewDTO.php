<?php

namespace DTO;

class ReviewDTO
{
    private int $userId;
    private int $productId;
    private int $rating;
    private string $comment;

    public function __construct(int $userId, int $productId, int $rating, string $comment, bool $isVerifiedPurchase)
    {
        $this->userId = $userId;
        $this->productId = $productId;
        $this->rating = $rating;
        $this->comment = $comment;
    }

    public function getUserId(): int { return $this->userId; }
    public function getProductId(): int { return $this->productId; }
    public function getRating(): int { return $this->rating; }
    public function getComment(): string { return $this->comment; }

}