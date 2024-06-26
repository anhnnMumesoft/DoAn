<?php

namespace App\Services;

use App\Models\OrderDetail;
use App\Models\OrderProduct;
use App\Models\AddressUser;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductDetailSize;
use App\Models\ProductImage;
use App\Models\ShopCart;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsed;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class OrderService
{   public function __construct()
{
    $this->apiContext = new ApiContext(
        new OAuthTokenCredential(
            'AXE5kGLGPq2EHy44gxuxDvf5GLMBPJoF_IUxszooHH4z2iOYmmVNIfqSyFjK3f7O8ikzAQy_LC-9amrd',     // ClientID
            'ELkqvm4YMV9k32-U6I_doNHgynKC9o3lnRl_YTTd7T8qFWbJm-esEvEfGZAAnMgj92za41vM259N7w3y'  // ClientSecret
        )
    );

    // Configure the ApiContext as needed
    $this->apiContext->setConfig([
        'mode' => 'sandbox', // Or 'live'
        // Other configuration settings
    ]);
}
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
                }else{
                    $order->voucherData=$order->voucherData;
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

            $orderDetails = OrderDetail::with(['productDetailSize.sizeData'])
                ->where('order_id', $id)
                ->get();

            foreach ($orderDetails as $detail) {
                $detail->productDetailSize = ProductDetailSize::find($detail->product_id);
                $detail->productDetail = ProductDetail::find($detail->productDetailSize->productdetail_id);
                $detail->product = Product::find($detail->productDetail->productId);
                $detail->productImage = ProductImage::where('product_detail_id', $detail->productDetail->id)
                    ->get();
//                $detail->product = Product::where('id', $detail->productDetail->productId)
//                    ->first();
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
    public function paymentOrder($data)
    {
//        try {
            $listItem = new ItemList();
            $totalPriceProduct = 0;

            foreach ($data['result'] as $result) {

                $productDetailSize = ProductDetailSize::with('sizeData')->find($result['productId']);
                $productDetail = ProductDetail::find($productDetailSize->productdetail_id);
                $product = Product::find($productDetail->productId);
                define("App\Services\EXCHANGE_RATES", [
                    'USD' => 1.0,
                    'EUR' => 0.85,
                    // Add other currencies as needed
                ]);
                $realPrice = round($result['realPrice'] / EXCHANGE_RATES['USD'], 2);

                $item = new Item();
                $item->setName($product->name . " | " . $productDetail->nameDetail . " | " . $productDetailSize->sizeData['value'])
                    ->setCurrency('USD')
                    ->setQuantity($result['quantity'])
                    ->setSku($result['productId']) // Similar to `item_id` in your JS code
                    ->setPrice($realPrice);
                $listItem->addItem($item);

                $totalPriceProduct += $realPrice * $result['quantity'];

            }

            $item = new Item();
            $item->setName("Phi ship + Voucher")
                ->setCurrency('USD')
                ->setQuantity(1)
                ->setPrice(round($data['total'] - $totalPriceProduct, 2));
            $listItem->addItem($item);

            $amount = new Amount();
            $amount->setCurrency("USD")
                ->setTotal($data['total']);

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($listItem)
                ->setDescription("This is the payment description.");

            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(url('http://localhost:5001/payment/success'))
                ->setCancelUrl(url('http://localhost:5001/payment/cancel'));

            $payer = new Payer();
            $payer->setPaymentMethod("paypal");

            $payment = new Payment();
            $payment->setIntent("sale")
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions(array($transaction));

        try {
            $payment->create($this->apiContext);
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            // PayPalConnectionException là một ví dụ, sử dụng ngoại lệ phù hợp
            error_log($ex->getData()); // Ghi lại phản hồi từ PayPal
            return [
                'errCode' => -1,
                'errMessage' => $ex->getMessage(),
            ];
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            return [
                'errCode' => -1,
                'errMessage' => 'An error occurred',
            ];
        }
            return [
                'errCode' => 0,
                'errMessage' => 'ok',
                'link' => $payment->getApprovalLink()
            ];
//        } catch (Exception $e) {
//            return [
//                'errCode' => -1,
//                'errMessage' => $e->getMessage(),
//            ];
//        }
    }
    public function executePayment($data)
    {
//        return $data;
        if (empty($data['PayerID']) || empty($data['paymentId']) || empty($data['token'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {

            $paymentId = $data['paymentId'];
            $payment = Payment::get($paymentId, $this->apiContext);

            $execution = new PaymentExecution();
            $execution->setPayerId($data['PayerID']);

            $amount = new Amount();
            $amount->setCurrency("USD")
            ->setTotal($data['total']);

            $transaction = new Transaction();
            $transaction->setAmount($amount);

            $execution->addTransaction($transaction);

            $result = $payment->execute($execution, $this->apiContext);

            if ($result) {
                $orderProduct = OrderProduct::create([
                    'address_user_id' => $data['addressUserId'],
                    'is_payment_online' => $data['isPaymentOnlien'],
                    'status_id' => 'S3',
                    'type_ship_id' => $data['typeShipId'],
                    'voucher_id' => $data['voucherId']??NULL,
                    'note' => $data['note']
                ]);


                foreach ($data['arrDataShopCart'] as $item) {

                    $item['order_id'] = $orderProduct->id;
                    $item['real_price'] = $item['realPrice']; // Thêm dòng này để sử dụng 'real_price' thay vì 'realPrice'
                    unset($item['realPrice']); // Xóa 'realPrice' khỏi $item
                    $item['product_id'] = $item['productId']; // Thêm dòng này để sử dụng 'real_price' thay vì 'realPrice'
                    unset($item['productId']); // Xóa 'realPrice' khỏi $item
                    OrderDetail::create($item);

//                    $productDetailSize = ProductDetailSize::find($item['product_id']);
//                    $productDetailSize->decrement('stock', $item['quantity']);
                }

                ShopCart::where('userId', $data['userId'])->where('statusId', 0)->delete();

                if (!empty($data['voucherId'])) {
                    VoucherUsed::where('voucherId', $data['voucherId'])
                        ->where('userId', $data['userId'])
                        ->update(['status' => 1]);
                }

                return [
                    'errCode' => 0,
                    'errMessage' => 'ok'
                ];
            }
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => $e->getMessage(),
            ];
        }
    }
    public function getAllOrdersByUser($userId)
    {
        if (empty($userId)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

//        try {
            $addressUsers = AddressUser::with(['orders.typeShipData', 'orders.voucherData', 'orders.statusOrderData'])
                ->where('user_id', $userId)
                ->get();

            foreach ($addressUsers as $addressUser) {
                foreach ($addressUser->orders as $order) {
                    if ($order->voucherData) {
                        $order->voucherData = $order->voucherData->typeVoucher;
                    } else {
                        // Handle the case where voucher is null
                        $order->voucherData = null;
                    }

                    $orderDetails = OrderDetail::where('order_id', $order->id)
                        ->get();
                    foreach ($orderDetails as $orderDetail) {
                        $orderDetail->productDetailSize = ProductDetailSize::find($orderDetail->product_id);
                        $orderDetail->productDetail = ProductDetail::find($orderDetail->productDetailSize->productdetail_id);
                        $orderDetail->product = Product::find($orderDetail->productDetail->productId);

                        $productImages = ProductImage::where('product_detail_id', $orderDetail->productDetail->id)->get();
                        foreach ($productImages as $image) {
                            $image->image = $image->image;
                        }
                        $orderDetail->productImage = $productImages;
                    }
                    $order->statusOrderData=$order->statusOrderData;
                    $order->typeShipData=$order->typeShipData;


//                    foreach ($orderDetails as $detail) {
//                        $detail->productImages->transform(function ($image) {
//                            $image->image = $image->image;
//                            return $image;
//                        });
//                    }

                    $order->orderDetails = $orderDetails;
                }
            }

            return [
                'errCode' => 0,
                'data' => $addressUsers
            ];
//        } catch (Exception $e) {
//            return [
//                'errCode' => -1,
//                'errMessage' => $e->getMessage()
//            ];
//        }
    }
    public function getAllOrdersByShipper($data)
    {
        try {
            $query = OrderProduct::orderBy('created_at', 'desc')
                ->where('shipper_id', $data['shipperId']);

            if (!empty($data['status'])) {
                if ($data['status'] == 'working') {
                    $query->where('status_id', 'S5');
                } elseif ($data['status'] == 'done') {
                    $query->where('status_id', 'S6');
                }
            }

            $orders = $query->get();

            foreach ($orders as $order) {
                $addressUser = AddressUser::where('id', $order->address_user_id)->first();
                if ($addressUser) {
                    $user = User::where('id', $addressUser->user_id)->first();
                    $order->userData = $user;
                    $order->addressUser = $addressUser;
                }
            }

            return [
                'errCode' => 0,
                'data' => $orders
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errorMessage' => $e->getMessage()
            ];
        }
    }
    public function paymentOrderVnpay($req)
    {
        try {
            $vnp_IpAddr = $req->ip();
            $vnp_TmnCode = env('VNP_TMNCODE');
            $vnp_HashSecret = env('VNP_HASHSECRET');
            $vnp_Url = env('VNP_URL');
            $vnp_Returnurl = env('VNP_RETURNURL');
            $createDate = now()->format('YmdHis');
            $vnp_TxnRef = Str::uuid()->toString();

            $vnp_Amount = $req->input('amount');
            $vnp_BankCode = $req->input('bankCode');
            $vnp_OrderInfo = $req->input('orderDescription');
            $vnp_OrderType = $req->input('orderType');
            $vnp_Locale  = $req->input('language', 'vn');
            $currCode = 'VND';

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount*100,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => $currCode,
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,

            );

            if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }
            return [
                'errCode' => 200,
                'link' => $vnp_Url
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errorMessage' => $e->getMessage()
            ];
        }
    }
    public function confirmOrderVnpay($data)
    {
        try {
            $vnp_Params = $data;
            $secureHash = Arr::pull($vnp_Params, 'vnp_SecureHash');
            Arr::forget($vnp_Params, 'vnp_SecureHashType');

            ksort($vnp_Params);
            $queryData = urldecode(http_build_query($vnp_Params));

            $secretKey =  env('VNP_HASHSECRET');
            $hmac = hash_hmac('sha512', $queryData, $secretKey);

            if ($secureHash === $hmac) {
                return [
                    'errCode' => 0,
                    'errMessage' => 'Success'
                ];
            } else {
                return [
                    'errCode' => 1,
                    'errMessage' => 'Failed'
                ];
            }
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errorMessage' => $e->getMessage()
            ];
        }
    }
    public function paymentOrderVnpaySuccess($data)
    {
        DB::beginTransaction();
        try {
            if (empty($data['addressUserId']) || empty($data['typeShipId'])) {
                return [
                    'errCode' => 1,
                    'errMessage' => 'Missing required parameter!'
                ];
            }

            $product = OrderProduct::create([
                'address_user_id' => $data['addressUserId'],
                'is_payment_online' => $data['isPaymentOnlien'],
                'status_id' => 'S3',
                'type_ship_id' => $data['typeShipId'],
                'voucher_id' => $data['voucherId'] ?? null,
                'note' => $data['note']
            ]);

            foreach ($data['arrDataShopCart'] as $item) {
                $item['order_id'] = $product->id;
            }

            $orderDetails = array_map(function ($item) use ($product) {
                return [
                    'order_id' => $product->id,  // Assuming $product->id is the ID of the newly created order
                    'product_id' => $item['productId'],
                    'quantity' => $item['quantity'],
                    'real_price' => $item['realPrice']  // Ensure this field exists in $item
                ];
            }, $data['arrDataShopCart']);
            // Insert data into OrderDetail
            OrderDetail::insert($orderDetails);

            $res = ShopCart::where('userId', $data['userId'])->where('statusId', 0)->first();
            if ($res) {
                ShopCart::where('userId', $data['userId'])->delete();

                foreach ($data['arrDataShopCart'] as $item) {
                    $productDetailSize = ProductDetailSize::find($item['productId']);
//                    $productDetailSize->decrement('stock', $item['quantity']);
                }
            }

            if (isset($data['voucherId']) && isset($data['userId'])) {
                if (!empty($data['voucherId']) && !empty($data['userId'])) {
                    $voucherUses = VoucherUsed::where('voucherId', $data['voucherId'])
                        ->where('userId', $data['userId'])
                        ->first();
                    $voucherUses->status = 1;
                    $voucherUses->save();
                }
            }

            DB::commit();
            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'errCode' => -1,
                'errMessage' => $e->getMessage()
            ];
        }
    }
    public function createNewOrder($data)
    {
        DB::beginTransaction();
        try {
            if (empty($data['addressUserId']) || empty($data['typeShipId'])) {
                return [
                    'errCode' => 1,
                    'errMessage' => 'Missing required parameter!'
                ];
            }

            $product = OrderProduct::create([
                'address_user_id' => $data['addressUserId'],
                'is_payment_online' => $data['isPaymentOnlien'],
                'status_id' => 'S3',
                'type_ship_id' => $data['typeShipId'],
                'voucher_id' => $data['voucherId'] ?? null,
                'note' => $data['note']
            ]);

            foreach ($data['arrDataShopCart'] as &$item) {
                $item['order_id'] = $product->id;
            }

            $orderDetails = array_map(function ($item) use ($product) {
                return [
                    'order_id' => $product->id,  // Assuming $product->id is the ID of the newly created order
                    'product_id' => $item['productId'],
                    'quantity' => $item['quantity'],
                    'real_price' => $item['realPrice']  // Ensure this field exists in $item
                ];
            }, $data['arrDataShopCart']);
            foreach ($orderDetails as $detail) {
                OrderDetail::create($detail);
            }

            $res = ShopCart::where('userId', $data['userId'])->where('statusId', 0)->first();
            if ($res) {
                ShopCart::where('userId', $data['userId'])->delete();

                foreach ($data['arrDataShopCart'] as $item) {
                    $productDetailSize = ProductDetailSize::find($item['productId']);
//                    $productDetailSize->decrement('stock', $item['quantity']);
                }
            }

            if (!empty($data['voucherId']) && !empty($data['userId'])) {
                $voucherUses = VoucherUsed::where('voucherId', $data['voucherId'])
                    ->where('userId', $data['userId'])
                    ->first();
                if ($voucherUses) {
                    $voucherUses->status = 1;
                    $voucherUses->save();
                }
            }

            DB::commit();
            return [
                'errCode' => 0,
                'errMessage' => 'ok'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'errCode' => -1,
                'errMessage' => $e->getMessage()
            ];
        }
    }
}
