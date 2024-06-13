<?php

namespace App\Services;

class RecommenderTable
{
    private $data = [];

    public function setCell($productId, $userId, $value)
    {
        $this->data[$userId][$productId] = $value;
    }

    public function getRecommendationsForUser($userId)
    {
        // Assuming this method should return product recommendations for a user
        return $this->data[$userId] ?? [];
    }
}
