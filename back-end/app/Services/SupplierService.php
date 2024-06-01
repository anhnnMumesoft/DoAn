<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Exception;

class SupplierService
{
    public function getAllSupplier($data)
    {
        try {
            $query = Supplier::query();

            if (!empty($data['keyword'])) {
                $query->where('name', 'like', '%' . $data['keyword'] . '%');
            }

            if (isset($data['limit']) && isset($data['offset'])) {
                $query->limit($data['limit'])->offset($data['offset']);
            }

            $suppliers = $query->get();
            $count = $query->count();

            return [
                'errCode' => 0,
                'data' => $suppliers,
                'count' => $count
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function createNewSupplier($data)
    {
        if (empty($data['name']) || empty($data['address']) || empty($data['phonenumber']) || empty($data['email'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $supplier = Supplier::create([
                'name' => $data['name'],
                'address' => $data['address'],
                'phonenumber' => $data['phonenumber'],
                'email' => $data['email']
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
    public function getDetailSupplierById($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $supplier = Supplier::find($id);

            if (!$supplier) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Supplier not found!'
                ];
            }

            return [
                'errCode' => 0,
                'data' => $supplier
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function updateSupplier($data)
    {
        if (empty($data['id']) || empty($data['name']) || empty($data['address']) || empty($data['phonenumber']) || empty($data['email'])) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $supplier = Supplier::find($data['id']);

            if (!$supplier) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Supplier not found!'
                ];
            }

            $supplier->name = $data['name'];
            $supplier->address = $data['address'];
            $supplier->phonenumber = $data['phonenumber'];
            $supplier->email = $data['email'];
            $supplier->save();

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
    public function deleteSupplier($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $supplier = Supplier::find($id);

            if (!$supplier) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Supplier not found!'
                ];
            }

            $supplier->delete();

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
