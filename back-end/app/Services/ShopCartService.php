<?php

namespace App\Services;

use App\Models\ShopCart;
use App\Models\ProductDetailSize;
use App\Models\ProductDetail;
use App\Models\ProductImage;
use App\Models\Product;
use Exception;

class ShopCartService
{
    public function getAllShopCartByUserId($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $shopCarts = ShopCart::with(['productDetailSize.sizeData', 'productDetail.product'])
                ->where('user_id', $id)
                ->where('status_id', 0)
                ->get();

            foreach ($shopCarts as $cart) {
                $productDetailSize = ProductDetailSize::with('sizeData')
                    ->find($cart->product_detail_size_id);
                $cart->productDetailSizeData = $productDetailSize;

                $productDetail = ProductDetail::find($productDetailSize->product_detail_id);
                $cart->productDetail = $productDetail;

                $productImages = ProductImage::where('product_detail_id', $productDetail->id)->get();
                foreach ($productImages as $image) {
                    $image->image = base64_decode($image->image);
                }
                $cart->productDetailImages = $productImages;

                $product = Product::find($productDetail->product_id);
                $cart->productData = $product;
            }

            return [
                'errCode' => 0,
                'data' => $shopCarts
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
}
