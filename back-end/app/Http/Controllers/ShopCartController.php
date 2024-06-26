<?php

namespace App\Http\Controllers;

use App\Services\ShopCartService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ShopCartController extends Controller
{
    protected $shopCartService;

    public function __construct(ShopCartService $shopCartService)
    {
        $this->shopCartService = $shopCartService;
    }

    public function getAllShopCartByUserId(Request $request)
    {
        $result = $this->shopCartService->getAllShopCartByUserId($request->id);
        return response()->json($result);
    }
    public function addShopCart(Request $request)
    {
        $data = $request->all();
        $result = $this->shopCartService->addShopCart($data);
        return response()->json($result);
    }
    public function deleteItemShopCart (Request $request)
    {
        $data = $request->only(['id']);
        $result = $this->shopCartService->deleteItem($data);
        return response()->json($result);
    }
}
