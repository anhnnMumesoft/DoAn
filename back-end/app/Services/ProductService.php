<?php

namespace App\Services;

use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductDetailSize;
use App\Models\ProductImage;
use App\Models\ReceiptDetail;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class ProductService
{
    public function createNewProduct($data)
    {
        if (empty($data['categoryId']) || empty($data['brandId']) || empty($data['image']) || empty($data['nameDetail'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        DB::beginTransaction();
        try {
            $product = Product::create([
                'name' => $data['name'],
                'contentHTML' => $data['contentHTML'],
                'contentMarkdown' => $data['contentMarkdown'],
                'statusId' => 'S1',
                'categoryId' => $data['categoryId'],
                'madeby' => $data['madeby'],
                'material' => $data['material'],
                'brandId' => $data['brandId']
            ]);

            $productDetail = ProductDetail::create([
                'productId' => $product->id,
                'description' => $data['description'],
                'originalPrice' => $data['originalPrice'],
                'discountPrice' => $data['discountPrice'],
                'nameDetail' => $data['nameDetail']
            ]);

            ProductImage::create([
                'product_detail_id' => $productDetail->id,
                'image' => $data['image']
            ]);

            ProductDetailSize::create([
                'productdetail_id' => $productDetail->id,
                'width' => $data['width'],
                'height' => $data['height'],
                'size_id' => $data['sizeId'],
                'weight' => $data['weight']
            ]);

            DB::commit();
            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getAllProductAdmin($data)
    {
        $query = Product::query();
        $queryProductCount = Product::query();

        // Include relationships
        $query->with([
            'brandData' => function ($query) {
                $query->select('value', 'code');
            },
            'categoryData' => function ($query) {
                $query->select('value', 'code');
            },
            'statusData' => function ($query) {
                $query->select('value', 'code');
            }
        ]);
        $queryProductCount->with([
            'brandData' => function ($query) {
                $query->select('value', 'code');
            },
            'categoryData' => function ($query) {
                $query->select('value', 'code');
            },
            'statusData' => function ($query) {
                $query->select('value', 'code');
            }
        ]);

        // Apply filters
        if (!empty($data['categoryId']) && $data['categoryId'] !== 'ALL') {
            $query->where('category_id', $data['categoryId']);
            $queryProductCount->where('category_id', $data['categoryId']);
        }

        if (!empty($data['brandId']) && $data['brandId'] !== 'ALL') {
            $query->where('brand_id', $data['brandId']);
            $queryProductCount->where('brand_id', $data['brandId']);
        }

        if (!empty($data['keyword'])) {
            $query->where('name', 'like', '%' . $data['keyword'] . '%');
            $queryProductCount->where('name', 'like', '%' . $data['keyword'] . '%');
        }

        // Sorting
        if (!empty($data['sortName']) && $data['sortName'] === "true") {
            $query->orderBy('name');
            $queryProductCount->orderBy('name');
        }

        // Pagination
        $limit = $data['limit'] ?? 10; // Default to 10 items per page if not set
        $offset = $data['offset'] ?? 0;

        $products = $query->skip($offset)->take($limit)->get();
        // Additional data manipulation
        foreach ($products as $product) {
            $product->load(['productDetails.productDetailSizes', 'productDetails.productImages']);
            foreach ($product->productDetails as $detail) {
                foreach ($detail->productImages as $image) {
                    $image->image = $image->image;
                }
            }
        }

        // Optional: Sort by price if requested
        if (!empty($data['sortPrice']) && $data['sortPrice'] === "true") {
            $products = $products->sortBy('price');
        }
         return ['errCode' => 0,'data' => $products,
             'count' => $queryProductCount->count()];
    }

    public function unactiveProduct($data)
    {
        if (empty($data['id'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        $product = Product::find($data['id']);

        if (!$product) {
            return [
                'errCode' => 2,
                'errMessage' => "The product isn't exist"
            ];
        }

        $product->statusId = 'S2';
        $product->save();

        return [
            'errCode' => 0,
            'errMessage' => 'ok'
        ];
    }

    public function activeProduct($data)
    {
        if (empty($data['id'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        $product = Product::find($data['id']);

        if (!$product) {
            return [
                'errCode' => 2,
                'errMessage' => "The product isn't exist"
            ];
        }

        $product->statusId = 'S1';
        $product->save();

        return [
            'errCode' => 0,
            'errMessage' => 'ok'
        ];
    }
    public function getDetailProductById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $product = Product::with(['brandData', 'categoryData', 'statusData'])
                ->findOrFail($id);

            $product->increment('view');

            $productDetails = ProductDetail::where('productId', $product->id)->get();

            foreach ($productDetails as $detail) {
                $detail->productImages = ProductImage::where('product_detail_id', $detail->id)->get();
                $detail->productDetailSizes = ProductDetailSize::with('sizeData')
                    ->where('productdetail_id', $detail->id)
                    ->get();

                foreach ($detail->productDetailSizes as $size) {
                    $receiptDetails = ReceiptDetail::where('product_detail_size_id', $size->id)->get();
                    $orderDetails = OrderDetail::where('product_id', $size->id)->get();

                    $quantity = $receiptDetails->sum('quantity') - $orderDetails->sum('quantity');

                    $size->stock = $quantity;
                }
            }

            $product->productDetails = $productDetails;

            return [
                'errCode' => 0,
                'data' => $product
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'errCode' => 2,
                'errMessage' => "The product isn't exist"
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }

    public function getAllProductDetailById($data)
    {
        if (empty($data['id']) || empty($data['limit'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        $productDetails = ProductDetail::where('productId', $data['id'])
            ->with(['productImages', 'productDetailSizes'])
            ->paginate($data['limit'], ['*'], 'page', $data['offset'] / $data['limit'] + 1);

        // Convert images from base64 to binary
        $productDetails->getCollection()->transform(function ($detail) {
            if ($detail->productImages) {
                foreach ($detail->productImages as $image) {
                    $image->image = $image->image;
                }
            }
            return $detail;
        });

        return [
            'errCode' => 0,
            'data' => $productDetails->items(),
            'count' => $productDetails->total()
        ];
    }

    public function getProductNew($limit)
    {
        return Product::with(['brandData', 'categoryData', 'statusData'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getAllProductDetailImageById($data)
    {
        if (empty($data['id']) || empty($data['limit'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        $productImages = ProductImage::where('product_detail_id', $data['id'])
            ->paginate($data['limit'], ['*'], 'page', floor($data['offset'] / $data['limit']) + 1);

        // Convert images from base64 to binary
//        $productImages->getCollection()->transform(function ($image) {
//            $image->image = $image->image;
//        });

        return [
            'errCode' => 0,
            'data' => $productImages->items(),
            'count' => $productImages->total()
        ];
    }

    public function getAllProductDetailSizeById($data)
    {
        if (empty($data['id']) || empty($data['limit']) ) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        $productSizes = ProductDetailSize::with('sizeData')
            ->where('productdetail_id', $data['id'])
            ->paginate($data['limit'], ['*'], 'page', floor($data['offset'] / $data['limit']) + 1);

        foreach ($productSizes as $size) {
            $receiptDetails = ReceiptDetail::where('product_detail_size_id', $size->id)->get();
            $orderDetails = OrderDetail::where('product_id', $size->id)->get();

            $quantity = $receiptDetails->sum('quantity') - $orderDetails->sum('quantity');

            $size->stock = $quantity;
        }

        return [
            'errCode' => 0,
            'data' => $productSizes->items(),
            'count' => $productSizes->total()
        ];
    }
    public function createNewProductDetailImage($data)
    {
        if (empty($data['image']) || empty($data['caption']) || empty($data['id'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $productImage = ProductImage::create([
                'product_detail_id' => $data['id'],
                'caption' => $data['caption'],
                'image' => $data['image']
            ]);

            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getDetailProductImageById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $productImage = ProductImage::findOrFail($id);
            $productImage->image = $productImage->image;

            return [
                'errCode' => 0,
                'data' => $productImage
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'errCode' => 2,
                'errMessage' => "Product image not found!"
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function updateProductDetailImage($data)
    {
        if (empty($data['id']) || empty($data['caption']) || empty($data['image'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $productImage = ProductImage::findOrFail($data['id']);
            $productImage->caption = $data['caption'];
            $productImage->image = $data['image'];
            $productImage->save();

            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'errCode' => 2,
                'errMessage' => 'Product Image not found!'
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function deleteProductDetailImage($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $productImage = ProductImage::findOrFail($id);
            $productImage->delete();

            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'errCode' => 2,
                'errMessage' => 'Product Image not found!'
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getDetailProductDetailSizeById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $productDetailSize = ProductDetailSize::findOrFail($id);

            return [
                'errCode' => 0,
                'data' => $productDetailSize
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'errCode' => 2,
                'errMessage' => 'Product detail size not found!'
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function updateProductDetailSize($data)
    {
        if (empty($data['id']) || empty($data['sizeId'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $productDetailSize = ProductDetailSize::findOrFail($data['id']);
            $productDetailSize->size_id = $data['sizeId'];
            $productDetailSize->width = $data['width'];
            $productDetailSize->height = $data['height'];
            $productDetailSize->weight = $data['weight'];
            $productDetailSize->save();

            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'errCode' => 2,
                'errMessage' => 'Product detail size not found!'
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function createNewProductDetailSize($data)
    {

        if (empty($data['productdetailId']) || empty($data['sizeId'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $productDetailSize = ProductDetailSize::create([
                'productdetail_id' => $data['productdetailId'],
                'size_id' => $data['sizeId'],
                'width' => $data['width'],
                'height' => $data['height'],
                'weight' => $data['weight'],
            ]);

            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function deleteProductDetailSize($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $productDetailSize = ProductDetailSize::findOrFail($id);
            $productDetailSize->delete();

            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'errCode' => 2,
                'errMessage' => 'Product detail size not found!'
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getDetailProductDetailById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $productDetail = ProductDetail::findOrFail($id);

            return [
                'errCode' => 0,
                'data' => $productDetail
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'errCode' => 2,
                'errMessage' => 'Product detail not found!'
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function updateProductDetail($data)
    {
        if (empty($data['id']) || empty($data['nameDetail']) || empty($data['originalPrice']) || empty($data['discountPrice'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $productDetail = ProductDetail::findOrFail($data['id']);
            $productDetail->nameDetail = $data['nameDetail'];
            $productDetail->originalPrice = $data['originalPrice'];
            $productDetail->discountPrice = $data['discountPrice'];
            $productDetail->description = $data['description'] ?? $productDetail->description; // Use existing description if not provided
            $productDetail->save();

            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'errCode' => 2,
                'errMessage' => 'Product not found!'
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function createNewProductDetail($data)
    {
        if (empty($data['image']) || empty($data['nameDetail']) || empty($data['originalPrice']) || empty($data['discountPrice']) || empty($data['id'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }
        try {
            $productDetail = ProductDetail::create([
                'productId' => $data['id'],
                'description' => $data['description'],
                'originalPrice' => $data['originalPrice'],
                'discountPrice' => $data['discountPrice'],
                'nameDetail' => $data['nameDetail']
            ]);

            ProductImage::create([
                'product_detail_id' => $productDetail->id,
                'image' => $data['image']
            ]);

            ProductDetailSize::create([
                'productdetail_id' => $productDetail->id,
                'width' => $data['width'],
                'height' => $data['height'],
                'size_id' => $data['sizeId'],
                'weight' => $data['weight']
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
    public function deleteProductDetail($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        DB::beginTransaction();
        try {
            $productDetail = ProductDetail::findOrFail($id);

            // Delete associated images and sizes
            ProductImage::where('product_detail_id', $id)->delete();
            ProductDetailSize::where('productdetail_id', $id)->delete();

            // Finally delete the product detail
            $productDetail->delete();

            DB::commit();
            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'errCode' => 2,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }



    // Implement other methods similarly...
}
