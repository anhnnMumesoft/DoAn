<?php

namespace App\Services;

use App\Models\ProductDetailSize;
use App\Models\ReceiptDetail;
use App\Models\OrderDetail;
use App\Models\OrderProduct;
use Exception;

class StatisticService
{
    public function getStatisticStockProduct($data)
    {
        try {
            $query = ProductDetailSize::with(['sizeData', 'productDetail.product.brandData', 'productDetail.product.categoryData', 'productDetail.product.statusData']);
            $query = ProductDetailSize::with(['sizeData','productDetail.product']);


            if (!empty($data['limit']) && isset($data['offset'])) {
                $query->limit($data['limit'])->offset($data['offset']);
            }

            $productDetailSizes = $query->get();

            foreach ($productDetailSizes as $productDetailSize) {
                $receiptDetails = ReceiptDetail::where('product_detail_size_id', $productDetailSize->id)->get();
                $orderDetails = OrderDetail::where('product_id', $productDetailSize->id)->get();

                $quantity = $receiptDetails->sum('quantity');

                foreach ($orderDetails as $orderDetail) {
                    $order = OrderProduct::find($orderDetail->order_id);
                    if ($order && $order->status_id != 'S7') {
                        $quantity -= $orderDetail->quantity;
                    }
                }

                $productDetailSize->stock = $quantity;
                $productDetailSize->productdData =$productDetailSize->productDetail->product;
                $productDetailSize->productDetaildData =$productDetailSize->productDetail;
            }

            return [
                'errCode' => 0,
                'data' => $productDetailSizes,
                'count' => ProductDetailSize::get()->count()
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
}
