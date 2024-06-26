<?php
namespace App\Http\Controllers;

use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BlogController extends Controller
{
    protected $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function createNewBlog(Request $request)
    {
        $data = $request->only(['title', 'contentMarkdown', 'contentHTML', 'image', 'subjectId', 'userId', 'shortdescription']);
        $result = $this->blogService->createNewBlog($data);
        return response()->json($result);
    }
    public function getAllBlog(Request $request)
    {
        $data = $request->only(['limit', 'offset', 'subjectId', 'keyword']);
        $result = $this->blogService->getAllBlog($data);
        return response()->json($result);
    }
    public function getDetailBlogById(Request $request)
    {
        $result = $this->blogService->getDetailBlogById($request->id);
        return response()->json($result);
    }
    public function updateBlog(Request $request)
    {
        $data = $request->only(['id', 'title', 'contentMarkdown', 'contentHTML', 'image', 'subjectId', 'shortdescription']);
        $result = $this->blogService->updateBlog($data);
        return response()->json($result);
    }
    public function deleteBlog(Request $request)
    {
        $id = $request->id; // Ensure 'id' is passed as a parameter
        $result = $this->blogService->deleteBlog($id);
        return response()->json($result);
    }
    public function getNewBlog(Request $request)
    {
        $data = $request->only(['limit']);
        $result = $this->blogService->getNewBlog($data);
        return response()->json($result);
    }
    public function getFeatureBlog (Request $request)
    {
        $limit = $request->input('limit', 5); // Default to 5 if no limit is provided
        $result = $this->blogService->getFeaturedBlogs($limit);
        return response()->json($result);
    }
}
