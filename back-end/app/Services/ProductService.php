<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductDetailSize;
use App\Models\ProductImage;
use App\Models\ReceiptDetail;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\ValidationHelper;

class ProductService
{
    public function createNewProduct($data)
    {
        $requiredFields = [
            'categoryId' => 'thể loại',
            'brandId' => 'thương hiệu',
            'image' => 'hình ảnh',
            'nameDetail' => 'tên chi tiết',
            'name' => 'tên',
            'contentHTML' => 'nội dung HTML',
            'contentMarkdown' => 'nội dung Markdown',
            'madeby' => 'nhà sản xuất',
            'material' => 'chất liệu',
            'description' => 'mô tả',
            'originalPrice' => 'giá gốc',
            'discountPrice' => 'giá giảm',
            'width' => 'chiều rộng',
            'height' => 'chiều cao',
            'sizeId' => 'kích thước',
            'weight' => 'trọng lượng'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
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
        } else {
            // Default sorting by created_at descending
            $query->orderBy('created_at', 'desc');
            $queryProductCount->orderBy('created_at', 'desc');
        }

        // Pagination
        $limit = $data['limit'] ?? 10; // Default to 10 items per page if not set
        $offset = $data['offset'] ?? 0;

        $products = $query->skip($offset)->take($limit)->get();
        // Additional data manipulation
        foreach ($products as $product) {
            $productDetails = ProductDetail::where('productId', $product->id)->get();

            foreach ($productDetails as $detail) {
                $detailSizes = ProductDetailSize::where('productdetail_id', $detail->id)->get();
                $detail->productDetailSize = $detailSizes;

                $images = ProductImage::where('product_detail_id', $detail->id)->get();
                foreach ($images as $image) {
                    $image->image = $image->image;
                }
                $detail->productImage = $images;
            }

            $product->productDetail = $productDetails;
            $product->price = $productDetails->first()->discountPrice ?? null;
        }

        // Optional: Sort by price if requested
        if (!empty($data['sortPrice']) && $data['sortPrice'] === "true") {
            $products = $products->sortBy('price');
        }
         return ['errCode' => 0,'data' => $products,
             'count' => $queryProductCount->count()];
    }
    public function updateProduct($data)
    {
        $requiredFields = [
            'id' => 'ID sản phẩm',
            'categoryId' => 'thể loại',
            'brandId' => 'thương hiệu'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
        }

        try {
            $product = Product::find($data['id']);
            if (!$product) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Product not found!'
                ];
            }

            $product->name = $data['name'];
            $product->material = $data['material'];
            $product->madeby = $data['madeby'];
            $product->brandId = $data['brandId'];
            $product->categoryId = $data['categoryId'];
            $product->contentMarkdown = $data['contentMarkdown'];
            $product->contentHTML = $data['contentHTML'];

            $product->save();

            return [
                'errCode' => 0,
                'errMessage' => 'Product updated successfully'
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }

    public function unactiveProduct($data)
    {
        $requiredFields = [
            'id' => 'ID sản phẩm'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
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
        $requiredFields = [
            'id' => 'ID sản phẩm'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
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
                $detail->productDetailSize = ProductDetailSize::with('sizeData')
                    ->where('productdetail_id', $detail->id)
                    ->get();

                foreach ($detail->productDetailSize as $size) {
                    $receiptDetails = ReceiptDetail::where('product_detail_size_id', $size->id)->get();
                    $orderDetails = OrderDetail::where('product_id', $size->id)->get();

                    $quantity = $receiptDetails->sum('quantity') - $orderDetails->sum('quantity');

                    $size->stock = $quantity;
                }
            }

            $product->productDetail = $productDetails;
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
        $requiredFields = [
            'id' => 'ID sản phẩm',
            'limit' => 'giới hạn'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
        }

        $productDetails = ProductDetail::where('productId', $data['id'])
            ->with(['productImages'])
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

    public function getAllProductDetailImageById($data)
    {
        $requiredFields = [
            'id' => 'ID sản phẩm',
            'limit' => 'giới hạn'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
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
        $requiredFields = [
            'id' => 'ID sản phẩm',
            'limit' => 'giới hạn'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
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
        $requiredFields = [
            'image' => 'hình ảnh',
            'caption' => 'chú thích',
            'id' => 'ID sản phẩm'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
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
        $requiredFields = [
            'id' => 'ID sản phẩm',
            'caption' => 'chú thích',
            'image' => 'hình ảnh'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
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
        $requiredFields = [
            'id' => 'ID sản phẩm',
            'sizeId' => 'kích thước'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
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

        $requiredFields = [
            'productdetailId' => 'ID chi tiết sản phẩm',
            'sizeId' => 'kích thước'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
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
        $requiredFields = [
            'id' => 'ID sản phẩm',
            'nameDetail' => 'tên chi tiết',
            'originalPrice' => 'giá gốc',
            'discountPrice' => 'giá giảm'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
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
        $requiredFields = [
            'image' => 'hình ảnh',
            'nameDetail' => 'tên chi tiết',
            'originalPrice' => 'giá gốc',
            'discountPrice' => 'giá giảm',
            'id' => 'ID sản phẩm'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
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
    public function getProductRecommend($data)
    {
        try {
            if (empty($data['userId']) || empty($data['limit'])) {
                return [
                    'errCode' => 1,
                    'errMessage' => 'Missing required parameter!'
                ];
            }

            $ratings = Comment::whereNotNull('star')->get()->groupBy('productId');
            $recommendations = collect();
            foreach ($ratings as $productId => $comments) {
                $averageRating = $comments->avg('star');

                if ($averageRating > 3) {

                    $product = Product::find($productId);
                    if ($product) {
                        $recommendations->push($product);
                    }
                }
            }


            $recommendedProducts = $recommendations->slice(0, $data['limit']);
            $processedProducts = [];
            if ($recommendedProducts->isNotEmpty()) {
                foreach ($recommendedProducts as $product1) {
                        $product = Product::with(['brandData', 'categoryData', 'statusData'])
                                            ->where('id',$product1->id)
                                            ->where('statusId', 'S1')
                                            ->first();

                    $productDetails = ProductDetail::where('productId', $product->id)->get();

                    foreach ($productDetails as $detail) {
                        $detailSizes = ProductDetailSize::where('productdetail_id', $detail->id)->get();
                        $detail->productDetailSizes = $detailSizes;

                        $images = ProductImage::where('product_detail_id', $detail->id)->get();
                        foreach ($images as $image) {
                            $image->image = $image->image;
                        }
                        $detail->productImages = $images;
                    }

                    $product->productDetail = $productDetails;
                    $product->price = $productDetails->first()->discountPrice ?? null;
                    $processedProducts[] = $product;
                }
            }

            return [
                'errCode' => 0,
                'data' => $processedProducts
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }

    public function getProductFeature($limit)
    {
        try {
            $products = Product::with(['brandData', 'categoryData', 'statusData'])
                ->where('statusId', 'S1')
                ->orderBy('view', 'desc')
                ->limit($limit)
                ->get();

            foreach ($products as $product) {
                $productDetails = ProductDetail::where('productId', $product->id)->get();

                foreach ($productDetails as $detail) {
                    $detailSizes = ProductDetailSize::where('productdetail_id', $detail->id)->get();
                    $detail->productDetailSizes = $detailSizes;

                    $images = ProductImage::where('product_detail_id', $detail->id)->get();
                    foreach ($images as $image) {
                        $image->image = $image->image;
                    }
                    $detail->productImages = $images;
                }

                $product->productDetail = $productDetails;
                $product->price = $productDetails->first()->discountPrice ?? null;
            }

            return [
                'errCode' => 0,
                'data' => $products
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getProductNew($limit)
    {
        try {
            $products = Product::with(['brandData', 'categoryData', 'statusData'])
                ->where('statusId', 'S1')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            foreach ($products as $product) {
                $productDetails = ProductDetail::where('productId', $product->id)->get();

                foreach ($productDetails as $detail) {
                    $detailSizes = ProductDetailSize::where('productdetail_id', $detail->id)->get();
                    $detail->productDetailSizes = $detailSizes;

                    $images = ProductImage::where('product_detail_id', $detail->id)->get();
                    foreach ($images as $image) {
                        $image->image = $image->image;
                    }
                    $detail->productImages = $images;
                }

                $product->productDetail = $productDetails;
                $product->price = $productDetails->first()->discountPrice ?? null;
            }

            return [
                'errCode' => 0,
                'data' => $products
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
//    public static function dynamicSortMultiple(Collection $collection, array $sortKeys): Collection
//    {
//        return $collection->sort(function ($a, $b) use ($sortKeys) {
//            foreach ($sortKeys as $key => $order) {
//                if ($a[$key] == $b[$key]) {
//                    continue;
//                }
//
//                if ($order === 'ASC') {
//                    return $a[$key] < $b[$key] ? -1 : 1;
//                } else {
//                    return $a[$key] > $b[$key] ? -1 : 1;
//                }
//            }
//
//            return 0;
//        });
//    }

    public function getAllProductUser($data)
    {
        try {
            $query = Product::with(['brandData', 'categoryData', 'statusData'])
                ->where('statusId', 'S1');
            $queryCount = Product::with(['brandData', 'categoryData', 'statusData'])
                ->where('statusId', 'S1');

            if (!empty($data['limit'])) {
                $query->limit($data['limit'])->offset($data['offset']);
            }

            if (!empty($data['categoryId']) && $data['categoryId'] !== 'ALL') {
                $query->where('categoryId', $data['categoryId']);
                $queryCount->where('categoryId', $data['categoryId']);
            }

            if (!empty($data['brandId']) && $data['brandId'] !== 'ALL') {
                $query->where('brandId', $data['brandId']);
                $queryCount->where('brandId', $data['brandId']);
            }

            if (!empty($data['sortName']) && $data['sortName'] === "true") {
                $query->orderBy('name', 'ASC');
                $queryCount->orderBy('name', 'ASC');
            }

            if (!empty($data['keyword'])) {
                $query->where('name', 'like', '%' . $data['keyword'] . '%');
                $queryCount->where('name', 'like', '%' . $data['keyword'] . '%');
            }

            $products = $query->get();

            foreach ($products as $product) {
                $productDetails = ProductDetail::where('productId', $product->id)->get();

                foreach ($productDetails as $detail) {
                    $detailSizes = ProductDetailSize::where('productdetail_id', $detail->id)->get();
                    $detail->productDetailSizes = $detailSizes;

                    $images = ProductImage::where('product_detail_id', $detail->id)->get();
                    foreach ($images as $image) {
                        $image->image = $image->image;
                    }
                    $detail->productImage = $images;
                }

                $product->productDetail = $productDetails;
                $product->price = $productDetails->first()->discountPrice ?? null;
            }
//            $sortKeys = [
//                'name' => 'ASC',
//                'price' => 'DESC'
//            ];
//
//            if (!empty($data['sortPrice']) && $data['sortPrice'] === "true") {
//                $products =self::dynamicSortMultiple($products, $sortKeys)->toArray();
//                $products= array_values($products);
//            }

            $productsArray = $products->toArray(); // Convert Collection to array
            usort($productsArray, function($a, $b) {
                return $b['price'] <=> $a['price']; // Sort in descending order by price
            });

            return [
                'errCode' => 0,
                'data' => $productsArray,
                'count' => $queryCount->count()
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }



    // Implement other methods similarly...
}
