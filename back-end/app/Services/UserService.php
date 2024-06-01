<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class UserService
{
    public function buildUrlEmail($token, $userId)
    {
        $baseUrl = config('app.url_react'); // Ensure you have 'URL_REACT' in your .env and config/app.php
        return "{$baseUrl}/verify-email?token={$token}&userId={$userId}";
    }

    public function createUser(array $data)
    {
        if (empty($data['email']) || empty($data['lastName'])) {
            return ['errCode' => 2, 'errMessage' => 'Missing required parameters!'];
        }

        if (User::where('email', $data['email'])->exists()) {
            return ['errCode' => 1, 'errMessage' => 'Your email is already in use, Please try another email!'];
        }

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'firstName' => $data['firstName'] ?? null,
            'lastName' => $data['lastName'] ?? null,
            'address' => $data['address'] ?? null,
            'roleId' => $data['roleId'] ?? null,
            'genderId' => $data['genderId'] ?? null,
            'phonenumber' => $data['phonenumber'] ?? null,
            'image' => $data['avatar'] ?? null,
            'dob' => $data['dob'] ?? null,
            'isActiveEmail' => 0,
            'statusId' => 'S1',
            'usertoken' => '',
        ]);

        return ['errCode' => 0, 'message' => 'OK'];
    }

    public function deleteUser($userId)
    {
        if (empty($userId)) {
            return ['errCode' => 1, 'errMessage' => 'Missing required parameters!'];
        }

        $user = User::find($userId);
        if (!$user) {
            return ['errCode' => 2, 'errMessage' => "The user doesn't exist"];
        }

        $user->delete();
        return ['errCode' => 0, 'message' => "The user is deleted"];
    }

    public function updateUser($userId, array $data)
    {
        if (empty($userId) || empty($data['genderId'])) {
            return ['errCode' => 2, 'errMessage' => 'Missing required parameters'];
        }

        $user = User::find($userId);
        if (!$user) {
            return ['errCode' => 1, 'errMessage' => 'User not found!'];
        }

        $user->update($data);
        return ['errCode' => 0, 'errMessage' => 'Update the user succeeds!'];
    }

    public function handleLogin(array $data)
    {
        if (empty($data['email']) || empty($data['password'])) {
            return ['errCode' => 4, 'errMessage' => 'Missing required parameters!'];
        }

        $user = User::where('email', $data['email'])->first();
        if(!$user){
            return ['errCode' => 1, 'errMessage' => "Your's email isn't exist in your system. plz try other email"];
        }
        if (!Hash::check($data['password'], $user->password)) {
            return ['errCode' => 3, 'errMessage' => 'Wrong password'];
        }

        // Generate token or use Laravel Passport for API authentication
        $token = $user->createToken('NNAStore')->plainTextToken;

        return ['errCode' => 0, 'errMessage' => 'Ok', 'user' => $user, 'accessToken' => $token];
    }

    public function handleChangePassword($userId, $newPassword, $oldPassword)
    {
        if (empty($userId) || empty($newPassword) || empty($oldPassword)) {
            return ['errCode' => 1, 'errMessage' => 'Missing required parameter!'];
        }

        $user = User::find($userId);
        if (!Hash::check($oldPassword, $user->password)) {
            return ['errCode' => 2, 'errMessage' => 'Old password is incorrect'];
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return ['errCode' => 0, 'errMessage' => 'Password updated successfully'];
    }

    public function getAllUsers($data)
    {
        $query = User::query();

        // Exclude password and image from the results
        $query->select('users.*')->with([
            'roleData' => function ($query) {
                $query->select('id', 'value', 'code');
            },
            'genderData' => function ($query) {
                $query->select('id', 'value', 'code');
            }
        ])->where('statusId', 'S1');

        // Apply filtering based on phonenumber if provided
        if (!empty($data['keyword'])) {
            $query->where('phonenumber', 'like', '%' . $data['keyword'] . '%');
        }
        // Pagination
        if (!empty($data['limit']) || !empty($data['offset'])) {
            $query->limit($data['limit'])->offset($data['offset']);
        }

        $users = $query->get();
        // Loại bỏ các thuộc tính không mong muốn từ kết quả
        $users->makeHidden(['password', 'image']);
        return [
            'errCode' => 0,
            'data' => $users,
            'count' => User::query()->get()->count(),
        ];
    }

    public function getDetailUserById($userId)
    {
        if (empty($userId)) {
            return response()->json(['errCode' => 1, 'errMessage' => 'Missing required parameters!'], 400);
        }

        $user = User::with(['roleData', 'genderData'])
            ->where('id', $userId)
            ->where('statusId', 'S1')
            ->first([
                'id',
                'firstName',
                'lastName',
                'email',
                'roleId',
                'genderId',
                'phonenumber',
                'address',
                'dob',
                'image'
            ]);

        if (!$user) {
            return ['errCode' => 2, 'errMessage' => 'User not found!'];
        }
        return ['errCode' => 0, 'data' => $user];
    }

    public function sendVerifyEmailUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return ['errCode' => 1, 'errMessage' => 'Missing required parameter!'];
        }

        $token = Str::uuid()->toString();
        $user->usertoken = $token;
        $user->save();

        $redirectLink = $this->buildUrlEmail($token, $user->id);
        Mail::to($user->email)->send(new VerifyEmail($user, $redirectLink));

        return ['errCode' => 0, 'errMessage' => 'ok'];
    }

    public function verifyEmailUser($userId, $token)
    {
        $user = User::where('id', $userId)
            ->where('usertoken', $token)
            ->first();

        if (!$user) {
            return ['errCode' => 2, 'errMessage' => 'User not found or token mismatch!'];
        }

        $user->isActiveEmail = 1;
        $user->usertoken = null; // Clear the token after verification
        $user->save();

        return ['errCode' => 0, 'errMessage' => 'Email verified successfully'];
    }

    public function sendForgotPasswordEmail($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return ['errCode' => 2, 'errMessage' => "Your email isn't exist in our system. Please try another email"];
        }

        $token = Str::uuid()->toString();
        $user->usertoken = $token;
        $user->save();

        $redirectLink = url("/verify-forgotpassword?token={$token}&userId={$user->id}");
        Mail::to($user->email)->send(new ForgotPasswordEmail($user, $redirectLink));

        return ['errCode' => 0, 'errMessage' => 'ok'];
    }

    public function forgotPassword($data)
    {
        if (empty($data['id']) || empty($data['token']) || empty($data['password'])) {
            return ['errCode' => 1, 'errMessage' => 'Missing required parameter!'];
        }

        $user = User::where('id', $data['id'])
            ->where('usertoken', $data['token'])
            ->first();

        if (!$user) {
            return ['errCode' => 2, 'errMessage' => 'User not found or token mismatch!'];
        }

        $user->password = Hash::make($data['password']);
        $user->usertoken = null; // Clear the token after use
        $user->save();

        return ['errCode' => 0, 'errMessage' => 'Password reset successfully'];
    }

    public function checkPhonenumberEmail($data)
    {
        $phoneExists = User::where('phonenumber', $data['phonenumber'])->exists();
        $emailExists = User::where('email', $data['email'])->exists();

        if ($phoneExists) {
            return ['isCheck' => true, 'errMessage' => "Số điện thoại đã tồn tại"];
        }

        if ($emailExists) {
            return ['isCheck' => true, 'errMessage' => "Email đã tồn tại"];
        }

        return ['isCheck' => false, 'errMessage' => "Hợp lệ"];
    }

}
