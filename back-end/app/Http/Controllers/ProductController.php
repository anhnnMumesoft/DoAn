<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function createNewProduct(Request $request)
    {
        try {
            $data = $this->productService->createNewProduct($request->all());
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['errCode' => -1, 'errMessage' => 'Error from server'], 500);
        }
    }
    public function unactiveProduct(Request $request)
    {
        try {
            $data = $this->productService->unactiveProduct($request->all());
            return response()->json($data, 200);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json([
                'errCode' => -1,
                'errMessage' => 'Error from server'
            ], 500);
        }
    }

    public function activeProduct(Request $request)
    {
        try {
            $data = $this->productService->activeProduct($request->all());
            return response()->json($data, 200);
        } catch (\Exception $error) {
            Log::error($error->getMessage());
            return response()->json([
                'errCode' => -1,
                'errMessage' => 'Error from server'
            ], 500);
        }
    }

    public function getAllProductAdmin(Request $request)
    {
//        try {
        $data = $this->productService->getAllProductAdmin($request->all());
        return response()->json($data, 200);
//        } catch (\Exception $e) {
//            return response()->json(['errCode' => -1, 'errMessage' => 'Error from server'], 500);
//        }
    }

    public function getAllProductUser(Request $request)
    {
        try {
            $data = $this->productService->getAllProductUser($request->all());
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['errCode' => -1, 'errMessage' => 'Error from server'], 500);
        }
    }
    public function updateProduct(Request $request)
    {
        $data = $request->all();
        $result = $this->productService->updateProduct($data);
        return response()->json($result);
    }

    public function getDetailProductById(Request $request)
    {
        try {
            $data = $this->productService->getDetailProductById($request->id);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['errCode' => -1, 'errMessage' => 'Error from server'], 500);
        }
    }
    public function getDetailProductImageById(Request $request)
    {
        $result = $this->productService->getDetailProductImageById($request->id);
        return response()->json($result);
    }
    public function updateProductDetailImage(Request $request)
    {
        $data = $request->only(['id', 'caption', 'image']);
        $result = $this->productService->updateProductDetailImage($data);
        return response()->json($result);
    }


    public function getAllProductDetailById(Request $request)
    {
        $data = $request->only(['id', 'limit', 'offset']);
        $result = $this->productService->getAllProductDetailById($data);
        return response()->json($result);
    }

    public function getAllProductDetailImageById(Request $request)
    {
        $data = $request->only(['id', 'limit', 'offset']);
        $result = $this->productService->getAllProductDetailImageById($data);
        return response()->json($result);
    }

    public function getAllProductDetailSizeById(Request $request)
    {
        $data = $request->only(['id', 'limit', 'offset']);
        $result = $this->productService->getAllProductDetailSizeById($data);
        return response()->json($result);
    }
    public function createNewProductDetailImage(Request $request)
    {
        $data = $request->only(['id', 'caption', 'image']);
        $result = $this->productService->createNewProductDetailImage($data);
        return response()->json($result);
    }
    public function deleteProductDetailImage(Request $request)
    {
        $result = $this->productService->deleteProductDetailImage($request->id);
        return response()->json($result);
    }
    public function getDetailProductDetailSizeById(Request $request)
    {
        $result = $this->productService->getDetailProductDetailSizeById($request->id);
        return response()->json($result);
    }
    public function updateProductDetailSize(Request $request)
    {
        $data = $request->only(['id', 'sizeId', 'width', 'height', 'weight']);
        $result = $this->productService->updateProductDetailSize($data);
        return response()->json($result);
    }
    public function createNewProductDetailSize(Request $request)
    {
        $data = $request->only(['productdetailId', 'sizeId', 'width', 'height', 'weight']);
        $result = $this->productService->createNewProductDetailSize($data);
        return response()->json($result);
    }
    public function deleteProductDetailSize(Request $request)
    {
        $result = $this->productService->deleteProductDetailSize($request->id);
        return response()->json($result);
    }
    public function getDetailProductDetailById(Request $request)
    {
        $result = $this->productService->getDetailProductDetailById($request->id);
        return response()->json($result);
    }
    public function updateProductDetail(Request $request)
    {
        $data = $request->only(['id', 'nameDetail', 'originalPrice', 'discountPrice', 'description']);
        $result = $this->productService->updateProductDetail($data);
        return response()->json($result);
    }
    public function createNewProductDetail(Request $request)
    {
        $data = $request->only(['id', 'image', 'nameDetail', 'originalPrice', 'discountPrice', 'description', 'width', 'height', 'sizeId', 'weight']);
        $result = $this->productService->createNewProductDetail($data);
        return response()->json($result);
    }
    public function deleteProductDetail(Request $request)
    {
        $result = $this->productService->deleteProductDetail($request->id);
        return response()->json($result);
    }

    public function getProductRecommend(Request $request)
    {
        $data = $request->only(['userId', 'limit']);
        $result = $this->productService->getProductRecommend($data);
        return response()->json($result);
    }
    public function getProductFeature(Request $request)
    {
        $limit = $request->input('limit', 10); // Default limit to 10 if not specified
        $result = $this->productService->getProductFeature($limit);
        return response()->json($result);
    }
    public function getProductNew(Request $request)
    {
        $limit = $request->input('limit', 10); // Default limit to 10 if not specified
        $result = $this->productService->getProductNew($limit);
        return response()->json($result);
    }



}
