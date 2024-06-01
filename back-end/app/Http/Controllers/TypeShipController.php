<?php

namespace App\Http\Controllers;

use App\Services\TypeShipService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TypeShipController extends Controller
{
    protected $typeShipService;

    public function __construct(TypeShipService $typeShipService)
    {
        $this->typeShipService = $typeShipService;
    }

    public function createNewTypeShip(Request $request)
    {
        $data = $request->only(['type', 'price']);
        $result = $this->typeShipService->createNewTypeShip($data);
        return response()->json($result);
    }
    public function getAllTypeship(Request $request)
    {
        $data = $request->only(['limit', 'offset', 'keyword']);
        $result = $this->typeShipService->getAllTypeship($data);
        return response()->json($result);
    }
    public function getDetailTypeshipById(Request $request)
    {
        $result = $this->typeShipService->getDetailTypeshipById($request->id);
        return response()->json($result);
    }
    public function updateTypeship(Request $request)
    {
        $data = $request->only(['id', 'type', 'price']);
        $result = $this->typeShipService->updateTypeship($data);
        return response()->json($result);
    }
    public function deleteTypeship(Request $request)
    {
        $id = $request->id; // Ensure 'id' is passed as a parameter
        $result = $this->typeShipService->deleteTypeship($id);
        return response()->json($result);
    }
}
