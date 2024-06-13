<?php

namespace App\Services;

use App\Models\OrderDetail;
use App\Models\ReceiptDetail;
use App\Models\ShopCart;
use App\Models\ProductDetailSize;
use App\Models\ProductDetail;
use App\Models\ProductImage;
use App\Models\Product;
use Exception;
use Illuminate\Support\Arr;

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

//        try {
            $shopCarts = ShopCart::where('userId', $id)
                ->where('statusId', 0)
                ->get();

            foreach ($shopCarts as $cart) {
                $productDetailSize = ProductDetailSize::with('sizeData')
                    ->find($cart->productdetailsizeId);
                $cart->productdetailsizeData = $productDetailSize;

                $productDetail = ProductDetail::find($productDetailSize->productdetail_id);
                $cart->productDetail = $productDetail;

                $productImages = ProductImage::where('product_detail_id', $productDetail->id)->get();
                foreach ($productImages as $image) {
                    $image->image = $image->image;
                }
                $cart->productDetailImage = $productImages;

                $product = Product::find($productDetail->productId);
                $cart->productData = $product;
            }

            return [
                'errCode' => 0,
                'data' => $shopCarts
            ];
//        } catch (Exception $e) {
//            return [
//                'errCode' => -1,
//                'errMessage' => 'Error from server: ' . $e->getMessage()
//            ];
//        }
    }
    public function addShopCart($data)
    {
        if (empty($data['userId']) || empty($data['productdetailsizeId']) || empty($data['quantity'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

//        try {
            $cart = ShopCart::where('userId', $data['userId'])
                ->where('productdetailsizeId', $data['productdetailsizeId'])
                ->where('statusId', 0)
                ->first();

            $productDetailSize = ProductDetailSize::find($data['productdetailsizeId']);
            if (!$productDetailSize) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Product detail size not found'
                ];
            }

            $stock = $this->calculateStock($productDetailSize->id);

            $data['type'] = Arr::get($data, 'type', '');
            if ($cart) {
                if ($data['type'] === "UPDATE_QUANTITY") {
                    if ($data['quantity'] > $stock) {
                        return [
                            'errCode' => 2,
                            'errMessage' => "Only $stock products left",
                            'quantity' => $stock
                        ];
                    }
                    $cart->quantity = $data['quantity'];
                } else {
                    if ($cart->quantity + $data['quantity'] > $stock) {
                        return [
                            'errCode' => 2,
                            'errMessage' => "Only $stock products left",
                            'quantity' => $stock
                        ];
                    }
                    $cart->quantity += $data['quantity'];
                }
                $cart->save();
            } else {
                if ($data['quantity'] > $stock) {
                    return [
                        'errCode' => 2,
                        'errMessage' => "Only $stock products left",
                        'quantity' => $stock
                    ];
                }
                ShopCart::create([
                    'userId' => $data['userId'],
                    'productdetailsizeId' => $data['productdetailsizeId'],
                    'quantity' => $data['quantity'],
                    'statusId' => 0
                ]);
            }

            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
//        } catch (Exception $e) {
//            return [
//                'errCode' => -1,
//                'errMessage' => 'Error from server: ' . $e->getMessage()
//            ];
//        }
    }

    private function calculateStock($productDetailSizeId)
    {
        $receiptDetails = ReceiptDetail::where('product_detail_size_id', $productDetailSizeId)->get();
        $orderDetails = OrderDetail::where('product_id', $productDetailSizeId)->get();

        $stockReceived = $receiptDetails->sum('quantity');
        $stockOrdered = $orderDetails->sum('quantity');

        return $stockReceived - $stockOrdered;
    }

    public function deleteItem($data)
    {
        if (empty($data['id'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $cartItem = ShopCart::where('id', $data['id'])
                ->where('statusId', 0)
                ->first();

            if ($cartItem) {
                $cartItem->delete();
                return [
                    'errCode' => 0,
                    'errMessage' => 'ok'
                ];
            } else {
                return [
                    'errCode' => 1,
                    'errMessage' => 'Item not found or already processed'
                ];
            }
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => $e->getMessage()
            ];
        }
    }
}
