<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\User;
use App\Services\CommentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function getAllReviewByProductId(Request $request)
    {
        $result = $this->commentService->getAllReviewByProductId($request->id);
        return response()->json($result);
    }
    public function createNewReview(Request $request)
    {
        $data = $request->only(['content', 'productId', 'userId', 'star', 'image']);
        $response = $this->commentService->createNewReview($data);
        return $response;
    }
    public function replyReview(Request $request)
    {
        $data = $request->only(['content', 'productId', 'userId', 'parentId']);
        $response = $this->commentService->replyReview($data);
        return $response;
    }
    public function deleteReview(Request $request)
    {
        $data = $request->only(['id']);
        $response = $this->commentService->deleteReview($data);
        return $response;
    }

    public function createNewComment(Request $request)
    {
        $data = $request->only(['content', 'blogId', 'userId', 'image']);
        $response = $this->commentService->createNewComment($data);
        return $response;
    }
    public function getAllCommentByBlogId(Request $request)
    {
        $response = $this->commentService->getAllCommentByBlogId($request->id);
        return $response;
    }
}
