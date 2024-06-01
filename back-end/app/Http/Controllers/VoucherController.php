<?php

namespace App\Http\Controllers;

use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VoucherController extends Controller
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }


    //type voucher
    public function createNewTypeVoucher(Request $request)
    {
        $data = $request->only(['typeVoucher', 'value', 'maxValue', 'minValue']);
        $result = $this->voucherService->createNewTypeVoucher($data);
        return response()->json($result);
    }
    public function getSelectTypeVoucher()
    {
        $result = $this->voucherService->getSelectTypeVoucher();
        return response()->json($result);
    }
    public function getAllTypeVoucher(Request $request)
    {
        $data = $request->only(['limit', 'offset']);
        $result = $this->voucherService->getAllTypeVoucher($data);
        return response()->json($result);
    }
    public function getDetailTypeVoucherById(Request $request)
    {
        $result = $this->voucherService->getDetailTypeVoucherById($request->id);
        return response()->json($result);
    }
    public function updateTypeVoucher(Request $request)
    {
        $data = $request->only(['id', 'typeVoucher', 'value', 'maxValue', 'minValue']);
        $result = $this->voucherService->updateTypeVoucher($data);
        return response()->json($result);
    }
    public function deleteTypeVoucher(Request $request)
    {
        $id = $request->id; // Ensure 'id' is passed as a parameter
        $result = $this->voucherService->deleteTypeVoucher($id);
        return response()->json($result);
    }

    // Vouvher
    public function createNewVoucher(Request $request)
    {
        $data = $request->only(['fromDate', 'toDate', 'typeVoucherId', 'amount', 'codeVoucher']);
        $result = $this->voucherService->createNewVoucher($data);
        return response()->json($result);
    }
    public function getAllVoucher(Request $request)
    {
        $data = $request->only(['limit', 'offset']);
        $result = $this->voucherService->getAllVoucher($data);
        return response()->json($result);
    }
    public function getDetailVoucherById(Request $request)
    {
        $id = $request->id; // Ensure 'id' is passed as a parameter
        $result = $this->voucherService->getDetailVoucherById($id);
        return response()->json($result);
    }
    public function updateVoucher(Request $request)
    {
        $data = $request->only(['id', 'fromDate', 'toDate', 'typeVoucherId', 'amount', 'codeVoucher']);
        $result = $this->voucherService->updateVoucher($data);
        return response()->json($result);
    }
    public function deleteVoucher(Request $request)
    {
        $id = $request->id; // Ensure 'id' is passed as a parameter
        $result = $this->voucherService->deleteVoucher($id);
        return response()->json($result);
    }


}
