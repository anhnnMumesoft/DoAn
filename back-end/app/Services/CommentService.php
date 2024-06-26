<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;

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
                $review->image = $review->image ?? '';

                $review->childComment = Comment::where('parentId', $review->id)->get();
                $review->user = User::where('id', $review->userId)
                                    ->first();

                if ($review->user) {
                    $review->user->image = $review->user->image;
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
    public function createNewReview($data)
    {
        $validator = Validator::make($data, [
            'content' => 'required',
            'productId' => 'required',
            'userId' => 'required',
            'star' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ]);
        }

        try {
            $comment = Comment::create([
                'content' => $data['content'],
                'productId' => $data['productId'],
                'userId' => $data['userId'],
                'star' => $data['star'],
                'image' => $data['image'] ?? null  // Assuming 'image' is optional
            ]);

            return response()->json([
                'errCode' => 0,
                'errMessage' => 'ok'
            ]);
        } catch (\Exception $e) {
            // Handle exception
            return response()->json([
                'errCode' => 2,
                'errMessage' => $e->getMessage()
            ]);
        }
    }
    public function replyReview($data)
    {
        $validator = Validator::make($data, [
            'content' => 'required',
            'productId' => 'required',
            'userId' => 'required',
            'parentId' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ]);
        }

        try {
            $comment = Comment::create([
                'content' => $data['content'],
                'productId' => $data['productId'],
                'userId' => $data['userId'],
                'parentId' => $data['parentId']
            ]);

            return response()->json([
                'errCode' => 0,
                'errMessage' => 'ok'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errCode' => 2,
                'errMessage' => $e->getMessage()
            ]);
        }
    }
    public function deleteReview($data)
    {
        $validator = Validator::make($data, [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ]);
        }

        try {
            $review = Comment::find($data['id']);
            if ($review) {
                $review->delete();
                return response()->json([
                    'errCode' => 0,
                    'errMessage' => 'ok'
                ]);
            } else {
                return response()->json([
                    'errCode' => 1,
                    'errMessage' => 'Review not found'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'errCode' => 2,
                'errMessage' => $e->getMessage()
            ]);
        }
    }
    public function createNewComment($data)
    {
        $validator = Validator::make($data, [
            'content' => 'required',
            'blogId' => 'required',
            'userId' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ]);
        }

        try {
            $comment = Comment::create([
                'content' => $data['content'],
                'blogId' => $data['blogId'],
                'userId' => $data['userId'],
                'image' => $data['image'] ?? null  // Assuming 'image' is optional
            ]);

            return response()->json([
                'errCode' => 0,
                'errMessage' => 'ok'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errCode' => 2,
                'errMessage' => $e->getMessage()
            ]);
        }
    }

    public function getAllCommentByBlogId($id)
    {
        if (empty($id)) {
            return response()->json([
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ]);
        }

        try {
            $comments = Comment::where('blogId', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($comments as $comment) {
                $comment->user = User::where('id', $comment->userId)
                    ->first();
                $comment->image = $comment->image ?? '';
                $comment->childComments = Comment::where('parentId', $comment->id)->get();
                if ($comment->user) {
                    $comment->user->image = $comment->user->image ?? '';
                }
            }

            return response()->json([
                'errCode' => 0,
                'data' => $comments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errCode' => 2,
                'errMessage' => $e->getMessage()
            ]);
        }
    }
}
