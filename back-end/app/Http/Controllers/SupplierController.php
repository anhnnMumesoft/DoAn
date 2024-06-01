<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\SupplierService;
class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function getAllSupplier(Request $request)
    {
        $data = $request->only(['limit', 'offset', 'keyword']);
        $result = $this->supplierService->getAllSupplier($data);
        return response()->json($result);
    }
    public function createNewSupplier(Request $request)
    {
        $data = $request->only(['name', 'address', 'phonenumber', 'email']);
        $result = $this->supplierService->createNewSupplier($data);
        return response()->json($result);
    }
    public function getDetailSupplierById(Request $request)
    {

        $result = $this->supplierService->getDetailSupplierById($request->id);
        return response()->json($result);
    }
    public function updateSupplier(Request $request)
    {
        $data = $request->only(['id', 'name', 'address', 'phonenumber', 'email']);
        $result = $this->supplierService->updateSupplier($data);
        return response()->json($result);
    }
    public function deleteSupplier(Request $request)
    {
        $id = $request->id; // Ensure 'id' is passed as a parameter
        $result = $this->supplierService->deleteSupplier($id);
        return response()->json($result);
    }
}
