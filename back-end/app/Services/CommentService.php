<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\User;
use Exception;

class CommentService
{
    public function getAllReviewByProductId($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $reviews = Comment::where('productId', $id)->get();

            foreach ($reviews as $review) {
                $review->image = $review->image ? base64_decode($review->image) : '';

                $review->childComments = Comment::where('parentId', $review->id)->get();
                $review->user = User::select('id', 'name', 'email', 'image') // Assuming these are the fields you want
                                    ->where('id', $review->user_id)
                                    ->first();

                if ($review->user) {
                    $review->user->image = base64_decode($review->user->image);
                }
            }

            return [
                'errCode' => 0,
                'data' => $reviews
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
}
