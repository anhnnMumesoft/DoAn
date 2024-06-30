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
    public function getDetailById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $addressUser = AddressUser::find($id);

            if ($addressUser) {
                return [
                    'errCode' => 0,
                    'data' => [
                        'id' => $addressUser->id,
                        'userId' => $addressUser->user_id,
                        'shipName' => $addressUser->ship_name,
                        'shipAddress' => $addressUser->ship_address,
                        'shipEmail' => $addressUser->ship_email,
                        'shipPhonenumber' => $addressUser->ship_phonenumber,
                        'createdAt' => $addressUser->created_at ? $addressUser->created_at->toDateTimeString() : null,
                        'updatedAt' => $addressUser->updated_at ? $addressUser->updated_at->toDateTimeString() : null
                    ],
                ];
            } else {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Address user not found'
                ];
            }
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => $e->getMessage()
            ];
        }
    }
    public function deleteAddressUser($data)
    {
        try {
            if (empty($data['id'])) {
                return [
                    'errCode' => 1,
                    'errMessage' => 'Missing required parameter!'
                ];
            }

            $addressUser = AddressUser::find($data['id']);

            if ($addressUser) {
                $addressUser->delete();
                return [
                    'errCode' => 0,
                    'errMessage' => 'ok'
                ];
            } else {
                return [
                    'errCode' => -1,
                    'errMessage' => 'Địa chỉ user không tìm thấy'
                ];
            }
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => $e->getMessage()
            ];
        }
    }
    public function editAddressUser($data) {
        try {
            if (!isset($data['id']) || !isset($data['shipName']) || !isset($data['shipAdress']) || !isset($data['shipEmail']) || !isset($data['shipPhonenumber'])) {
                return [
                    'errCode' => 1,
                    'errMessage' => 'Missing required parameter !'
                ];
            } else {
                $addressUser = AddressUser::find($data['id']);
                if ($addressUser) {
                    $addressUser->ship_name = $data['shipName'];
                    $addressUser->ship_phonenumber = $data['shipPhonenumber'];
                    $addressUser->ship_address = $data['shipAdress'];
                    $addressUser->ship_email = $data['shipEmail'];

                    $addressUser->save();
                    return [
                        'errCode' => 0,
                        'errMessage' => 'ok'
                    ];
                } else {
                    return [
                        'errCode' => 0,
                        'errMessage' => 'Địa chỉ người dùng không tồn tại'
                    ];
                }
            }
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => $e->getMessage()
            ];
        }
    }
}
