<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AllCodeService;
use Illuminate\Routing\Controller;

// Assume you have a similar service in Laravel

class AllcodeController extends Controller
{
    protected $allCodeService;

    public function __construct(AllCodeService $allCodeService)
    {
        $this->allCodeService = $allCodeService;
    }

    public function handleCreateNewAllCode(Request $request)
    {
        try {
            $data = $this->allCodeService->handleCreateNewAllCode($request->all());
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['errCode' => -1, 'errMessage' => 'Error from server'], 200);
        }
    }

    public function getAllCodeService(Request $request)
    {
        try {
            $data = $this->allCodeService->getAllCodeService($request->query('type'));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['errCode' => -1, 'errMessage' => 'Error from server'], 200);
        }
    }

    public function getAllCategoryBlog(Request $request)
    {
        try {
            $data = $this->allCodeService->getAllCategoryBlog($request->query('type'));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['errCode' => -1, 'errMessage' => 'Error from server'], 200);
        }
    }

    public function handleUpdateAllCode(Request $request)
    {
        try {
            $data = $this->allCodeService->handleUpdateAllCode($request->all());
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['errCode' => -1, 'errMessage' => 'Error from server'], 200);
        }
    }

    public function getDetailAllCodeById(Request $request)
    {
        try {
            $data = $this->allCodeService->getDetailAllCodeById($request->query('id'));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['errCode' => -1, 'errMessage' => 'Error from server'], 200);
        }
    }

    public function handleDeleteAllCode(Request $request)
    {
        try {
            $data = $this->allCodeService->handleDeleteAllCode($request->input('id'));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['errCode' => -1, 'errMessage' => 'Error from server'], 200);
        }
    }

    public function getListAllCodeService(Request $request)
    {
        try {
            $data = $this->allCodeService->getListAllCodeService($request->all());
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['errCode' => -1, 'errMessage' => 'Error from server'], 200);
        }
    }
}
