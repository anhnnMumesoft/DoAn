<?php

namespace App\Services;

use App\Models\TypeVoucher;
use App\Models\Voucher;
use App\Models\VoucherUsed;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VoucherService
{
    //typeVoucher
    public function createNewTypeVoucher($data)
    {
        if (empty($data['typeVoucher']) || empty($data['value']) || empty($data['maxValue']) || empty($data['minValue'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $typeVoucher = TypeVoucher::create([
                'typeVoucher' => $data['typeVoucher'],
                'value' => $data['value'],
                'maxValue' => $data['maxValue'],
                'minValue' => $data['minValue']
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
    public function getSelectTypeVoucher()
    {
        try {
            $typeVouchers = TypeVoucher::with(['typeVoucherData' => function ($query) {
                $query->select('value', 'code');
            }])->get();

            return [
                'errCode' => 0,
                'data' => $typeVouchers
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getAllTypeVoucher($data)
    {
        try {
            $query = TypeVoucher::with(['typeVoucherData' => function ($query) {
                $query->select('value', 'code');
            }]);

            if (!empty($data['limit']) && !empty($data['offset'])) {
                $query->limit($data['limit'])->offset($data['offset']);
            }

            $typeVouchers = $query->get();
            $count = $query->count();

            return [
                'errCode' => 0,
                'data' => $typeVouchers,
                'count' => $count
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getDetailTypeVoucherById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $typeVoucher = TypeVoucher::with(['typeVoucherData' => function ($query) {
                $query->select('value', 'code');
            }])->find($id);

            if (!$typeVoucher) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'TypeVoucher not found!'
                ];
            }

            return [
                'errCode' => 0,
                'data' => $typeVoucher
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function updateTypeVoucher($data)
    {
        if (empty($data['id']) || empty($data['typeVoucher']) || empty($data['value']) || empty($data['maxValue']) || empty($data['minValue'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $typeVoucher = TypeVoucher::find($data['id']);

            if (!$typeVoucher) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'TypeVoucher not found!'
                ];
            }

            $typeVoucher->typeVoucher = $data['typeVoucher'];
            $typeVoucher->value = $data['value'];
            $typeVoucher->maxValue = $data['maxValue'];
            $typeVoucher->minValue = $data['minValue'];
            $typeVoucher->save();

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
    public function deleteTypeVoucher($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $typeVoucher = TypeVoucher::find($id);

            if (!$typeVoucher) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'TypeVoucher not found!'
                ];
            }

            $typeVoucher->delete();

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
    // Voucher
    public function createNewVoucher($data)
    {
        if (empty($data['fromDate']) || empty($data['toDate']) || empty($data['typeVoucherId']) || empty($data['amount']) || empty($data['codeVoucher'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $voucher = Voucher::create([
                'fromDate' => $data['fromDate'],
                'toDate' => $data['toDate'],
                'typeVoucherId' => $data['typeVoucherId'],
                'amount' => $data['amount'],
                'codeVoucher' => $data['codeVoucher']
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
    public function getAllVoucher($data)
    {
        try {
            $query = Voucher::with([
                'typeVoucher' => function ($query) {
                    // Assuming 'typeVoucherData' is a valid relationship or method in the TypeVoucher model
                    $query->with([
                        'typeVoucherData' => function ($subQuery) {
                            $subQuery->select('value', 'code');
                        }
                    ]);
                }
            ]);

            if (!empty($data['limit']) && !empty($data['offset'])) {
                $query->limit($data['limit'])->offset($data['offset']);
            }

            $vouchers = $query->get();
            $count = $query->count();

            foreach ($vouchers as $voucher) {
                $usedCount = VoucherUsed::where('voucherId', $voucher->id)->where('status', 1)->count();
                $voucher->usedAmount = $usedCount;
            }

            return [
                'errCode' => 0,
                'data' => $vouchers,
                'count' => $count
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getDetailVoucherById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $voucher = Voucher::find($id);

            if (!$voucher) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Voucher not found!'
                ];
            }

            return [
                'errCode' => 0,
                'data' => $voucher
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function updateVoucher($data)
    {
        if (empty($data['id']) || empty($data['fromDate']) || empty($data['toDate']) || empty($data['typeVoucherId']) || empty($data['amount']) || empty($data['codeVoucher'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $voucher = Voucher::find($data['id']);

            if (!$voucher) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Voucher not found!'
                ];
            }

            $voucher->fromDate = $data['fromDate'];
            $voucher->toDate = $data['toDate'];
            $voucher->typeVoucherId = $data['typeVoucherId'];
            $voucher->amount = $data['amount'];
            $voucher->codeVoucher = $data['codeVoucher'];
            $voucher->save();

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
    public function deleteVoucher($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $voucher = Voucher::find($id);

            if (!$voucher) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Voucher not found!'
                ];
            }

            $voucher->delete();

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
