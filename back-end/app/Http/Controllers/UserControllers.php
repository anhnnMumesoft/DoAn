<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserControllers extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function createNewUser(Request $request)
    {
        $data = $this->userService->createUser($request->all());
        return response()->json($data);
    }

    public function updateUser(Request $request)
    {
        $data = $this->userService->updateUser($request->id,$request->all());
        return response()->json($data);
    }

    public function deleteUser(Request $request)
    {
        $data = $this->userService->deleteUser($request->id);
        return response()->json($data);
    }

    public function login(Request $request)
    {
        $data = $this->userService->handleLogin($request->all());
        return response()->json($data);
    }

    public function changePassword(Request $request)
    {
        $data = $this->userService->handleChangePassword($request->all());
        return response()->json($data);
    }

    public function getAllUser(Request $request)
    {
        $data = $this->userService->getAllUsers($request->query());
        return response()->json($data);
    }

    public function getDetailUserById(Request $request)
    {
        $id = $request->query('id');
        $data = $this->userService->getDetailUserById($id);
        return response()->json($data);
    }

    public function sendVerifyEmailUser(Request $request)
    {
        $data = $this->userService->sendVerifyEmailUser($request->all());
        return response()->json($data);
    }

    public function verifyEmailUser(Request $request)
    {
        $data = $this->userService->verifyEmailUser($request->all());
        return response()->json($data);
    }

    public function sendEmailForgotPassword(Request $request)
    {
        $data = $this->userService->sendForgotPasswordEmail($request->email);
        return response()->json($data);
    }

    public function forgotPassword(Request $request)
    {
        $data = $this->userService->forgotPassword($request->all());
        return response()->json($data);
    }

    public function checkPhonenumberEmail(Request $request)
    {
        $data = $this->userService->checkPhonenumberEmail($request->query());
        return response()->json($data);
    }
}
