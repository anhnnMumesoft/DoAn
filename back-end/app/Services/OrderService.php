<?php

namespace App\Services;

use App\Models\OrderDetail;
use App\Models\OrderProduct;
use App\Models\AddressUser;
use App\Models\Product;
use App\Models\ProductDetailSize;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\Voucher;
use Exception;

class OrderService
{
    public function getAllOrders($data)
    {
        try {
            $query = OrderProduct::with(['typeShipData', 'voucherData', 'statusOrderData'])
                ->orderBy('created_at', 'desc');

            if (!empty($data['limit']) && isset($data['offset'])) {
                $query->limit($data['limit'])->offset($data['offset']);
            }

            if (!empty($data['statusId']) && $data['statusId'] !== 'ALL') {
                $query->where('status_id', $data['statusId']);
            }

            $orders = $query->get();

            foreach ($orders as $order) {
                $addressUser = AddressUser::find($order->address_user_id);
                $shipper = User::find($order->shipper_id);

                if ($addressUser) {
                    $user = User::find($addressUser->user_id);
                    $order->userData = $user;
                    $order->addressUser = $addressUser;
                    $order->shipperData = $shipper;
                }
                if (!$order->voucherData) {
                    // No Voucher found, handle accordingly
                    $order->voucherData = new Voucher(array_fill_keys(array_keys((new Voucher)->getAttributes()), null));
                }
            }

            return [
                'errCode' => 0,
                'data' => $orders,
                'count' => $orders->count()
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getDetailOrderById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $order = OrderProduct::with(['typeShipData', 'voucherData.typeVoucher', 'statusOrderData'])
                ->findOrFail($id);

            $order->addressUser = AddressUser::find($order->address_user_id);
            $order->userData = User::find($order->addressUser->user_id);

            $orderDetails = OrderDetail::with(['productDetailSize.sizeData', 'productDetail.product'])
                ->where('order_id', $id)
                ->get();

            foreach ($orderDetails as $detail) {
                $detail->productImage = ProductImage::where('product_detail_id', $detail->product_id)
                    ->get();
                $detail->product = Product::where('id', $detail->productDetail->productId)
                    ->first();
            }

            $order->orderDetail = $orderDetails;

            return [
                'errCode' => 0,
                'data' => $order
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function updateStatusOrder($data)
    {
        if (empty($data['id']) || empty($data['statusId'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $order = OrderProduct::findOrFail($data['id']);
            $order->status_id = $data['statusId'];
            $order->save();

            // Revert stock if the order is cancelled
            if ($data['statusId'] == 'S7' && !empty($data['dataOrder']['orderDetail'])) {
                foreach ($data['dataOrder']['orderDetail'] as $detail) {
                    $productDetailSize = ProductDetailSize::findOrFail($detail['productDetailSize']['id']);
                    $productDetailSize->increment('stock', $detail['quantity']);
                }
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
}
