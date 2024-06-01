<?php

namespace App\Services;

use App\Models\TypeShip;
use Exception;

class TypeShipService
{
    public function createNewTypeShip($data)
    {
        if (empty($data['type']) || empty($data['price'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $typeShip = TypeShip::create([
                'type' => $data['type'],
                'price' => $data['price']
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
    public function getAllTypeship($data)
    {
        try {
            $query = TypeShip::query();

            if (!empty($data['limit']) && !empty($data['offset'])) {
                $query->limit($data['limit'])->offset($data['offset']);
            }

            if (!empty($data['keyword'])) {
                $query->where('type', 'like', '%' . $data['keyword'] . '%');
            }

            $typeships = $query->get();
            $count = $query->count();

            return [
                'errCode' => 0,
                'data' => $typeships,
                'count' => $count
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function getDetailTypeshipById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $typeship = TypeShip::find($id);

            if (!$typeship) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'TypeShip not found!'
                ];
            }

            return [
                'errCode' => 0,
                'data' => $typeship
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function updateTypeship($data)
    {
        if (empty($data['id']) || empty($data['type']) || empty($data['price'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $typeship = TypeShip::find($data['id']);

            if (!$typeship) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'TypeShip not found!'
                ];
            }

            $typeship->type = $data['type'];
            $typeship->price = $data['price'];
            $typeship->save();

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
    public function deleteTypeship($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $typeship = TypeShip::find($id);

            if (!$typeship) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'TypeShip not found!'
                ];
            }

            $typeship->delete();

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
