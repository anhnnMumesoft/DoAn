<?php

namespace App\Http\Controllers;

use App\Services\AddressUserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AddressUserController extends Controller
{
    protected $addressUserService;

    public function __construct(AddressUserService $addressUserService)
    {
        $this->addressUserService = $addressUserService;
    }

    public function getAllAddressUserByUserId(Request $request)
    {
        $userId = $request->userId; // Assuming you're getting the user ID from authenticated user
        $result = $this->addressUserService->getAllAddressUserByUserId($userId);
        return response()->json($result);
    }
    public function createNewAddressUser(Request $request)
    {
        $data = $request->all();
        $result = $this->addressUserService->createNewAddressUser($data);
        return response()->json($result);
    }
    public function getDetailAddressUserById (Request $request)
    {
        $id = $request->input('id');
        $result = $this->addressUserService->getDetailById($id);
        return response()->json($result);
    }
    public function deleteAddressUser(Request $request)
    {
        $data = $request->all();
        $response = $this->addressUserService->deleteAddressUser($data);
        return response()->json($response);
    }
}
