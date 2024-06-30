<?php

namespace App\Services;

use App\Models\Allcode;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ProductDetailSize;
use App\Models\ReceiptDetail;
use App\Models\OrderDetail;
use App\Models\OrderProduct;
use App\Models\TypeVoucher;
use App\Models\User;
use App\Models\Voucher;
use Exception;
use Illuminate\Support\Carbon;

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
    public function getCountCardStatistic()
    {
        try {
            $countUser = User::where('statusId', 'S1')->count();
            $countProduct = Product::count();
            $countReview = Comment::where('star', '>', 0)->count();
            $countOrder = OrderProduct::where('status_id', '!=', 'S7')->count();

            $data = [
                'countUser' => $countUser,
                'countProduct' => $countProduct,
                'countReview' => $countReview,
                'countOrder' => $countOrder
            ];

            return response()->json([
                'errCode' => 0,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errCode' => 2,
                'errMessage' => $e->getMessage()
            ]);
        }
    }
    public function getCountStatusOrder($data)
    {
        try {
            if (empty($data['oneDate'])) {
                return [
                    'errCode' => 1,
                    'data' => 'Missing required parameter!'
                ];
            }

            $statusOrder = Allcode::where('type', 'STATUS-ORDER')->get();
            $objectCount = [];
            $arrayLabel = [];
            $arrayValue = [];

            if ($statusOrder) {
                $orderProduct = OrderProduct::all();
                $filteredOrders = $orderProduct->filter(function ($item) use ($data) {
                    $updatedAt = Carbon::parse($item->updated_at);

                    if ($data['type'] === "month") {
                        // Adjust the date format to match the input string
                        try {
                            $date = Carbon::createFromFormat('D M d Y H:i:s e+', $data['oneDate']);
                        } catch (\Exception $e) {
                            return false; // If parsing fails, exclude this item
                        }
                        return $updatedAt->month === $date->month && $updatedAt->year === $date->year;
                    }
                    if ($data['type'] === "year") {
                        // Adjust the date format to match the input string
                        try {
                            $date = Carbon::createFromFormat('D M d Y H:i:s e+', $data['oneDate']);
                        } catch (\Exception $e) {
                            return false; // If parsing fails, exclude this item
                        }
                        return $updatedAt->year === $date->year;
                    }
                    if ($data['type'] === "day") {
                        $oneDate = Carbon::createFromFormat('D M d Y H:i:s e+', $data['oneDate']);


                        if ($oneDate && $data['twoDate'] !='null' && $data['oneDate']!=$data['twoDate']) {
                            $twoDate = Carbon::createFromFormat('D M d Y H:i:s e+', $data['twoDate']);
                            return $updatedAt->between($oneDate, $twoDate);
                        } else {
                            return $updatedAt->day === $oneDate->day && $updatedAt->month === $oneDate->month && $updatedAt->year === $oneDate->year;
                        }

                    }

                });


                foreach ($statusOrder as $status) {
                    $arrayLabel[] = $status->value;
                    $count = $filteredOrders->where('status_id', $status->code)->count();
                    $arrayValue[] = $count;
                }

                $objectCount = [
                    'arrayLabel' => $arrayLabel,
                    'arrayValue' => $arrayValue
                ];

                return [
                    'errCode' => 0,
                    'data' => $objectCount
                ];
            }
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errorMessage' => $e->getMessage()
            ];
        }
    }
    public function getStatisticByMonth($data)
    {
        try {
            if (empty($data['year'])) {
                return [
                    'errCode' => 1,
                    'data' => 'Missing required parameter!'
                ];
            }

            // Ensure orderDetails is loaded
            $orderProducts = OrderProduct::with(['typeShipData', 'voucherData.typeVoucherOfVoucherData', 'statusOrderData'])
                ->where('status_id', 'S6')
                ->get();

            $arrayMonthLabel = [];
            $arrayMonthValue = [];

            for ($i = 1; $i <= 12; $i++) {
                $arrayMonthLabel[] = "Th " . $i;
                $price = 0;
                foreach ($orderProducts as $orderProduct) {
                    $orderProduct->orderDetails = OrderDetail::where('order_id', $orderProduct->id)->get();
                }
                foreach ($orderProducts as $orderProduct) {

                    $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $orderProduct->updated_at);
                    if ($updatedAt->year == $data['year'] && $updatedAt->month == $i) {
                        // Check if orderDetails is not null and not empty
                        if ($orderProduct->orderDetails && !$orderProduct->orderDetails->isEmpty()) {
                            $totalPrice = $orderProduct->orderDetails->sum(function ($detail) {
                                return $detail->real_price * $detail->quantity;
                            });

                            if ($orderProduct->voucherId) {
                                $totalPriceProduct = $this->totalPriceDiscount($totalPrice, $orderProduct) + $orderProduct->typeShipData->price;
                            } else {
                                $totalPriceProduct = $totalPrice + $orderProduct->typeShipData->price;
                            }

                            $price += $totalPriceProduct;
                        }
                    }
                }
                $arrayMonthValue[] = $price;
            }
            return [
                'errCode' => 0,
                'data' => [
                    'arrayMonthLabel' => $arrayMonthLabel,
                    'arrayMonthValue' => $arrayMonthValue
                ]
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errorMessage' => $e->getMessage()
            ];
        }
    }
    public function getStatisticByDay($data)
    {
        try {
            if (empty($data['month']) || empty($data['year'])) {
                return [
                    'errCode' => 1,
                    'data' => 'Missing required parameter!'
                ];
            }

            $daysInMonth = Carbon::create($data['year'], $data['month'])->daysInMonth;
            $orderProducts = OrderProduct::with(['typeShipData', 'voucherData.typeVoucherOfVoucherData', 'statusOrderData'])
                ->where('status_id', 'S6')
                ->get();

            $arrayDayLabel = [];
            $arrayDayValue = [];

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = Carbon::create($data['year'], $data['month'], $i);
                $label = $date->isToday() ? "Today" : $i;
                $arrayDayLabel[] = $label;

                $price = 0;
                foreach ($orderProducts as $orderProduct) {
                    $orderProduct->orderDetails = OrderDetail::where('order_id', $orderProduct->id)->get();
                }
                foreach ($orderProducts as $orderProduct) {
                    $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $orderProduct->updated_at);
                    if ($updatedAt->year == $data['year'] && $updatedAt->month == $data['month'] && $updatedAt->day == $i) {
                        $totalPrice = $orderProduct->orderDetails->sum(function ($detail) {
                            return $detail->real_price * $detail->quantity;
                        });

                        if ($orderProduct->voucherId) {
                            $totalPriceProduct = $this->totalPriceDiscount($totalPrice, $orderProduct) + $orderProduct->typeShipData->price;
                        } else {
                            $totalPriceProduct = $totalPrice + $orderProduct->typeShipData->price;
                        }

                        $price += $totalPriceProduct;
                    }
                }
                $arrayDayValue[] = $price;
            }

            return [
                'errCode' => 0,
                'data' => [
                    'arrayDayLabel' => $arrayDayLabel,
                    'arrayDayValue' => $arrayDayValue
                ]
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errorMessage' => $e->getMessage()
            ];
        }
    }

    private function totalPriceDiscount($totalPrice, $orderProduct)
    {
        $discount = $orderProduct->voucherData; // Assuming voucherData is loaded correctly
        if ($discount->typeVoucherOfVoucherData->typeVoucher === "percent") {
            $discountAmount = ($totalPrice * $discount->typeVoucherOfVoucherData->value) / 100;
            return $totalPrice - min($discountAmount, $discount->typeVoucherOfVoucherData->maxValue);
        } else {
            return $totalPrice - $discount->typeVoucherOfVoucherData->maxValue;
        }
    }

    public function getStatisticOverturn($data)
    {
        try {
            if (!isset($data['oneDate']) || !isset($data['twoDate'])) {
                return [
                    'errCode' => 1,
                    'data' => 'Missing required parameter !'
                ];
            }

            $orderProducts = OrderProduct::where('status_id', 'S6')
                ->with(['typeShipData', 'voucherData', 'statusOrderData'])
                ->get();

            foreach ($orderProducts as $orderProduct) {
                $orderProduct->orderDetail = OrderDetail::where('order_id', $orderProduct->id)->get();
                if ($orderProduct->voucherData) {
                    $orderProduct->voucherData->typeVoucherOfVoucherData = TypeVoucher::find($orderProduct->voucherData->typeVoucherId);
                }else{
                    $orderProduct->voucherData = new Voucher(array_fill_keys(array_keys((new Voucher)->getAttributes()), null));
                }

                $totalPrice = 0;
                foreach ($orderProduct->orderDetail as $orderDetail) {
                    $totalPrice += $orderDetail->real_price * $orderDetail->quantity;
                }

                if ($orderProduct->voucherId) {
                    $orderProduct->totalpriceProduct = $this->totalPriceDiscount($totalPrice, $orderProduct) + $orderProduct->typeShipData->price;
                } else {
                    $orderProduct->totalpriceProduct = $totalPrice + $orderProduct->typeShipData->price;
                }
                $orderProduct->typeShipData=$orderProduct->typeShipData;
                $orderProduct->statusOrderData=$orderProduct->statusOrderData;
            }


            $filteredOrderProducts = $orderProducts->filter(function ($item) use ($data) {
                $updatedAt = Carbon::parse($item->updated_at);

                if ($data['type'] === "month") {
                    // Adjust the date format to match the input string
                    try {
                        $date = Carbon::createFromFormat('D M d Y H:i:s e+', $data['oneDate']);
                    } catch (\Exception $e) {
                        return false; // If parsing fails, exclude this item
                    }
                    return $updatedAt->month === $date->month && $updatedAt->year === $date->year;
                }
                if ($data['type'] === "year") {
                    // Adjust the date format to match the input string
                    try {
                        $date = Carbon::createFromFormat('D M d Y H:i:s e+', $data['oneDate']);
                    } catch (\Exception $e) {
                        return false; // If parsing fails, exclude this item
                    }
                    return $updatedAt->year === $date->year;
                }
                if ($data['type'] === "day") {
                        $oneDate = Carbon::createFromFormat('D M d Y H:i:s e+', $data['oneDate']);


                    if ($oneDate && $data['twoDate'] !='null' && $data['oneDate']!=$data['twoDate']) {
                        $twoDate = Carbon::createFromFormat('D M d Y H:i:s e+', $data['twoDate']);
                        return $updatedAt->between($oneDate, $twoDate);
                    } else {
                        return $updatedAt->day === $oneDate->day && $updatedAt->month === $oneDate->month && $updatedAt->year === $oneDate->year;
                    }

                }

            });
            $filteredOrderProductsArray = $filteredOrderProducts->values()->toArray();
            return [
                'errCode' => 0,
                'data' => $filteredOrderProductsArray
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => $e->getMessage()
            ];
        }
    }

    public function getStatisticProfit($data)
    {
        try {
            if (!isset($data['oneDate']) || !isset($data['twoDate'])) {
                return [
                    'errCode' => 1,
                    'data' => 'Missing required parameter !'
                ];
            }

            $orderProducts = OrderProduct::where('status_id', 'S6')
                ->with(['typeShipData', 'voucherData', 'statusOrderData'])
                ->get();

            foreach ($orderProducts as $orderProduct) {
                $orderProduct->orderDetail = OrderDetail::where('order_id', $orderProduct->id)->get();
                if ($orderProduct->voucherData) {
                    $orderProduct->voucherData->typeVoucherOfVoucherData = TypeVoucher::find($orderProduct->voucherData->typeVoucherId);
                }else{
                    $orderProduct->voucherData = new Voucher(array_fill_keys(array_keys((new Voucher)->getAttributes()), null));
                }

                $totalPrice = 0;
                $importPrice = 0;
                foreach ($orderProduct->orderDetail as $orderDetail) {
                    $receiptDetails = ReceiptDetail::where('product_detail_size_id', $orderDetail->product_id)->get();

                    $avgPrice = 0;
                    $avgQuantity = 0;
                    foreach ($receiptDetails as $receiptDetail) {
                        $avgPrice += $receiptDetail->quantity * $receiptDetail->price;
                        $avgQuantity += $receiptDetail->quantity;
                    }
                    $orderDetail->importPrice = round($avgPrice / $avgQuantity);
                    $importPrice += round($avgPrice / $avgQuantity) * $orderDetail->quantity;
                    $totalPrice += $orderDetail->real_price * $orderDetail->quantity;
                }
                $orderProduct->importPrice = $importPrice;
                if ($orderProduct->voucherId) {
                    $orderProduct->totalpriceProduct = $this->totalPriceDiscount($totalPrice, $orderProduct) + $orderProduct->typeShipData->price;
                    $orderProduct->profitPrice = $this->totalPriceDiscount($totalPrice, $orderProduct) + $orderProduct->typeShipData->price - $importPrice;
                } else {
                    $orderProduct->totalpriceProduct = $totalPrice + $orderProduct->typeShipData->price;
                    $orderProduct->profitPrice = ($totalPrice + $orderProduct->typeShipData->price) - $importPrice;
                }
                $orderProduct->typeShipData=$orderProduct->typeShipData;
                $orderProduct->statusOrderData=$orderProduct->statusOrderData;
            }

            $filteredOrderProducts = $orderProducts->filter(function ($item) use ($data) {
                $updatedAt = Carbon::parse($item->updated_at);

                if ($data['type'] === "month") {
                    // Adjust the date format to match the input string
                    try {
                        $date = Carbon::createFromFormat('D M d Y H:i:s e+', $data['oneDate']);
                    } catch (\Exception $e) {
                        return false; // If parsing fails, exclude this item
                    }
                    return $updatedAt->month === $date->month && $updatedAt->year === $date->year;
                }
                if ($data['type'] === "year") {
                    // Adjust the date format to match the input string
                    try {
                        $date = Carbon::createFromFormat('D M d Y H:i:s e+', $data['oneDate']);
                    } catch (\Exception $e) {
                        return false; // If parsing fails, exclude this item
                    }
                    return $updatedAt->year === $date->year;
                }
                if ($data['type'] === "day") {
                    $oneDate = Carbon::createFromFormat('D M d Y H:i:s e+', $data['oneDate']);


                    if ($oneDate && $data['twoDate'] !='null'&&$data['oneDate']!=$data['twoDate']) {
                        $twoDate = Carbon::createFromFormat('D M d Y H:i:s e+', $data['twoDate']);
                        return $updatedAt->between($oneDate, $twoDate);
                    } else {
                        return $updatedAt->day === $oneDate->day && $updatedAt->month === $oneDate->month && $updatedAt->year === $oneDate->year;
                    }

                }

            });

            $filteredOrderProductsArray = $filteredOrderProducts->values()->toArray();
            return [
                'errCode' => 0,
                'data' => $filteredOrderProductsArray
            ];
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => $e->getMessage()
            ];
        }
    }


}
