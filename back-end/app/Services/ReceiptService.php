<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductDetailSize;
use App\Models\Receipt;
use App\Models\ReceiptDetail;
use App\Models\Supplier;
use App\Models\User;
use Exception;

class ReceiptService
{
    public function createNewReceipt($data)
{
    if (empty($data['userId']) || empty($data['supplierId']) || empty($data['productDetailSizeId']) || empty($data['quantity']) || empty($data['price'])) {
        return [
            'errCode' => 2,
            'errMessage' => 'Missing required parameter!'
        ];
    }

    try {
        // Lấy thông tin ProductDetail dựa trên productDetailSizeId
        $productDetailSize = ProductDetailSize::find($data['productDetailSizeId']);
        if (!$productDetailSize) {
            return [
                'errCode' => 1,
                'errMessage' => 'ProductDetailSize not found!'
            ];
        }

        $productDetail = ProductDetail::withTrashed()->find($productDetailSize->productdetail_id);
        if (!$productDetail) {
            return [
                'errCode' => 1,
                'errMessage' => 'ProductDetail not found!'
            ];
        }

        // So sánh giá nhập với giá bán (discountPrice)
        if ($data['price'] > $productDetail->discountPrice) {
            return [
                'errCode' => 2,
                'errMessage' => 'Giá nhập cao hơn giá bán, vui lòng điều chỉnh lại giá bán!'
            ];
        }

        // Tạo mới Receipt
        $receipt = Receipt::create([
            'user_id' => $data['userId'],
            'supplier_id' => $data['supplierId']
        ]);

        if ($receipt) {
            // Tạo mới ReceiptDetail
            ReceiptDetail::create([
                'receipt_id' => $receipt->id,
                'product_detail_size_id' => $data['productDetailSizeId'],
                'quantity' => $data['quantity'],
                'price' => $data['price'],
            ]);
        }

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

    public function getAllReceipt($data)
    {
        try {
            $query = Receipt::query();

            if (!empty($data['limit']) && isset($data['offset'])) {
                $query->limit($data['limit'])->offset($data['offset']);
            }

            // If keyword search is needed, adjust the query accordingly
            // if (!empty($data['keyword'])) {
            //     $query->where('name', 'like', '%' . $data['keyword'] . '%');
            // }

            $receipts = $query->get();

            foreach ($receipts as $receipt) {
                $receipt->userData = User::withTrashed()->find($receipt->user_id);
                $receipt->supplierData = Supplier::withTrashed()->find($receipt->supplier_id);
            }

            return [
                'errCode' => 0,
                'data' => $receipts,
                'count' => $receipts->count()
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getDetailReceiptById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            // Update the relationship name in the with() method
            $receipt = Receipt::with(['receiptDetails.productDetailSize.sizeData', 'receiptDetails.productDetail.product'])
                ->findOrFail($id);

            // Update any other references to the old relationship name
            foreach ($receipt->receiptDetails as $detail) {  // Updated to receiptDetails
                $productDetailSize = ProductDetailSize::with('sizeData')
                    ->find($detail->product_detail_size_id);
                if ($productDetailSize) {
                    $detail->productDetailSizeData = $productDetailSize;
                    $detail->productDetailData = ProductDetail::find($productDetailSize->productdetail_id);
                    $detail->productData = Product::find($detail->productDetailData->productId);
                }
            }

            return [
                'errCode' => 0,
                'data' => $receipt
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function createNewReceiptDetail($data)
{
    if (empty($data['receiptId']) || empty($data['productDetailSizeId']) || empty($data['quantity']) || empty($data['price'])) {
        return [
            'errCode' => 2,
            'errMessage' => 'Missing required parameter!'
        ];
    }

    try {
        // Lấy thông tin ProductDetail dựa trên productDetailSizeId
        $productDetailSize = ProductDetailSize::find($data['productDetailSizeId']);
        if (!$productDetailSize) {
            return [
                'errCode' => 1,
                'errMessage' => 'ProductDetailSize not found!'
            ];
        }

        $productDetail = ProductDetail::withTrashed()->find($productDetailSize->productdetail_id);
        if (!$productDetail) {
            return [
                'errCode' => 1,
                'errMessage' => 'ProductDetail not found!'
            ];
        }

        // So sánh giá nhập với giá bán (discountPrice)
        if ($data['price'] > $productDetail->discountPrice) {
            return [
                'errCode' => 2,
                'errMessage' => 'Giá nhập cao hơn giá bán, vui lòng điều chỉnh lại giá bán!'
            ];
        }

        // Tạo mới ReceiptDetail
        ReceiptDetail::create([
            'receipt_id' => $data['receiptId'],
            'product_detail_size_id' => $data['productDetailSizeId'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
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
}
