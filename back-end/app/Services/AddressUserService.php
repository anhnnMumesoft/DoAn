<?php

namespace App\Services;

use App\Models\AddressUser;
use Exception;

class AddressUserService
{
    public function getAllAddressUserByUserId($userId)
    {
        if (empty($userId)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $addresses = AddressUser::where('user_id', $userId)->get();
            // Chuyển đổi dữ liệu trả về sang định dạng mới
            $formattedAddresses = $addresses->map(function ($address) {
                return [
                    'id' => $address->id,
                    'userId' => $address->user_id,
                    'shipName' => $address->ship_name,
                    'shipAddress' => $address->ship_address,
                    'shipEmail' => $address->ship_email,
                    'shipPhonenumber' => $address->ship_phonenumber,
                    'createdAt' => $address->created_at ? $address->created_at->toDateTimeString() : null,
                    'updatedAt' => $address->updated_at ? $address->updated_at->toDateTimeString() : null
                ];
            });
            return [
                'errCode' => 0,
                'data' => $formattedAddresses
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function createNewAddressUser($data)
    {
        if (empty($data['userId'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            AddressUser::create([
                'user_id' => $data['userId'],
                'ship_name' => $data['shipName'],
                'ship_address' => $data['shipAdress'],
                'ship_email' => $data['shipEmail'],
                'ship_phonenumber' => $data['shipPhonenumber'],
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
}
