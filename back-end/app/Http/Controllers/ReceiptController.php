<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\ReceiptService;


class ReceiptController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function createNewReceipt(Request $request)
    {
        $data = $request->only(['userId', 'supplierId', 'productDetailSizeId', 'quantity', 'price']);
        $result = $this->receiptService->createNewReceipt($data);
        return response()->json($result);
    }
    public function getAllReceipt(Request $request)
    {
        $data = $request->only(['limit', 'offset', 'keyword']);
        $result = $this->receiptService->getAllReceipt($data);
        return response()->json($result);
    }
    public function getDetailReceiptById(Request $request)
    {
        $result = $this->receiptService->getDetailReceiptById($request->id);
        return response()->json($result);
    }
    public function createNewReceiptDetail(Request $request)
    {
        $data = $request->only(['receiptId', 'productDetailSizeId', 'quantity', 'price']);
        $result = $this->receiptService->createNewReceiptDetail($data);
        return response()->json($result);
    }
}
