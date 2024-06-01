<?php

namespace App\Http\Controllers;

use App\Services\BannerService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BannerController extends Controller
{
    protected $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    public function getAllBanner(Request $request)
    {
        $data = $request->only(['limit', 'offset', 'keyword']);
        $result = $this->bannerService->getAllBanner($data);
        return response()->json($result);
    }
    public function getDetailBanner(Request $request)
    {
        $id = $request->id; // Ensure 'id' is passed as a query parameter
        $result = $this->bannerService->getDetailBanner($id);
        return response()->json($result);
    }
    public function updateBanner(Request $request)
    {
        $data = $request->only(['id', 'name', 'description', 'image']);
        $result = $this->bannerService->updateBanner($data);
        return response()->json($result);
    }
    public function createNewBanner(Request $request)
    {
        $data = $request->only(['name', 'description', 'image']);
        $result = $this->bannerService->createNewBanner($data);
        return response()->json($result);
    }
    public function deleteBanner(Request $request)
    {
        $id = $request->id; // Ensure 'id' is passed as a parameter
        $result = $this->bannerService->deleteBanner($id);
        return response()->json($result);
    }

}
