<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\User;
use Exception;

class BlogService
{
    public function createNewBlog($data)
    {
        $requiredFields = [
            'title' => 'tiêu đề',
            'contentMarkdown' => 'nội dung Markdown',
            'contentHTML' => 'nội dung HTML',
            'image' => 'hình ảnh',
            'subjectId' => 'ID chủ đề',
            'userId' => 'ID người dùng'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
        }

        try {
            $blog = Blog::create([
                'shortdescription' => $data['shortdescription'] ?? '',
                'title' => $data['title'],
                'subjectId' => $data['subjectId'],
                'statusId' => 'S1',
                'image' => $data['image'],
                'contentMarkdown' => $data['contentMarkdown'],
                'contentHTML' => $data['contentHTML'],
                'userId' => $data['userId'],
                'view' => 0
            ]);

            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getAllBlog($data)
    {
        try {
            $query = Blog::with([
                'subjectData' => function ($query) {
                    $query->select('value', 'code');
                }
            ])
                ->where('statusId', 'S1')
                ->orderBy('created_at', 'desc');

            if (!empty($data['limit']) && !empty($data['offset'])) {
                $query->limit($data['limit'])->offset($data['offset']);
            }

            if (!empty($data['subjectId'])) {
                $query->where('subjectId', $data['subjectId']);
            }

            if (!empty($data['keyword'])) {
                $query->where('title', 'like', '%' . $data['keyword'] . '%');
            }

            $blogs = $query->get();

            foreach ($blogs as $blog) {
                $blog->image = $blog->image;
                $blog->userData = User::where('id', $blog->userId)->first();
                $blog->commentData = Comment::where('blogId', $blog->id)->get();
            }

            return [
                'errCode' => 0,
                'data' => $blogs,
                'count' => $blogs->count()
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getDetailBlogById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $blog = Blog::with(['subjectData' => function ($query) {
                $query->select('value', 'code');
            }])->find($id);

            if (!$blog) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Blog not found!'
                ];
            }

            // Increment the view count
            $blog->increment('view');

            // Refresh the instance to get updated data
            $blog->refresh();

            $blog->userData = User::where('id', $blog->userId)->first();

            if ($blog->image) {
                $blog->image = $blog->image;
            }

            return [
                'errCode' => 0,
                'data' => $blog
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function updateBlog($data)
    {
        $requiredFields = [
            'id' => 'ID bài viết',
            'title' => 'tiêu đề',
            'contentMarkdown' => 'nội dung Markdown',
            'contentHTML' => 'nội dung HTML',
            'image' => 'hình ảnh',
            'subjectId' => 'ID chủ đề'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
        }

        try {
            $blog = Blog::find($data['id']);

            if (!$blog) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Blog not found!'
                ];
            }

            $blog->title = $data['title'];
            $blog->contentMarkdown = $data['contentMarkdown'];
            $blog->contentHTML = $data['contentHTML'];
            $blog->image = $data['image'];
            $blog->subjectId = $data['subjectId'];
            $blog->shortdescription = $data['shortdescription'] ?? $blog->shortdescription; // Use existing if not provided

            $blog->save();

            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function deleteBlog($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $blog = Blog::find($id);

            if (!$blog) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Blog not found!'
                ];
            }

            $blog->delete();

            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getNewBlog($data)
    {
        try {
            $blogs = Blog::with(['subjectData'])
                ->where('statusId', 'S1')
                ->orderBy('created_at', 'desc')
                ->limit($data['limit'])
                ->get();

            foreach ($blogs as $blog) {
                $blog->image = $blog->image;
                $blog->userData = User::find($blog->userId);
                $blog->commentData = Comment::where('blogId', $blog->id)->get();
            }

            return [
                'errCode' => 0,
                'data' => $blogs
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getFeaturedBlogs($limit)
    {
        try {
            $blogs = Blog::with(['subjectData' => function ($query) {
                $query->select('value', 'code');
            }])
                ->where('statusId', 'S1')
                ->orderBy('view', 'desc')
                ->limit($limit)
                ->get();

            foreach ($blogs as $blog) {
                $blog->image =$blog->image;
                $blog->userData = User::find($blog->user_id);
                $blog->commentData = Comment::where('blogId', $blog->id)->get();
            }

            return [
                'errCode' => 0,
                'data' => $blogs
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => $e->getMessage()
            ];
        }
    }
}
