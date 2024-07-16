<?php

namespace App\Services;

use App\Models\Banner;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\ValidationHelper;
class BannerService
{
    public function getAllBanner($data)
    {
        $query = Banner::where('statusId', 'S1');

        if (!empty($data['keyword'])) {
            $query->where('name', 'like', '%' . $data['keyword'] . '%');
        }

        if (!empty($data['limit']) && !empty($data['offset'])) {
            $query->limit($data['limit'])->offset($data['offset']);
        }

        $banners = $query->get();

        // Convert images from base64 to binary
        $banners->transform(function ($banner) {
            $banner->image = $banner->image;
            return $banner;
        });

        return [
            'errCode' => 0,
            'data' => $banners,
            'count' => $banners->count()
        ];
    }
    public function getDetailBanner($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Thiếu thông số bắt buộc!'
            ];
        }

        try {
            $banner = Banner::find($id);

            if (!$banner) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Banner not found!'
                ];
            }

            // Convert image from base64 to binary if it exists
            if ($banner->image) {
                $banner->image = $banner->image;
            }

            return [
                'errCode' => 0,
                'data' => $banner
            ];
        } catch (Exception $e) {
            return [
                'errCode' => -1,
                'errMessage' => 'Error from server: ' . $e->getMessage()
            ];
        }
    }
    public function updateBanner($data)
    {
        $requiredFields = [
            'id' => 'ID',
            'image' => 'hình ảnh',
            'description' => 'mô tả',
            'name' => 'tên'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
        }
        try {
            $banner = Banner::find($data['id']);

            if (!$banner) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Banner not found!'
                ];
            }

            $banner->name = $data['name'];
            $banner->description = $data['description'];
            $banner->image = $data['image'];
            $banner->save();

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
    public function createNewBanner($data)
    {
        $requiredFields = [
            'image' => 'hình ảnh',
            'description' => 'mô tả',
            'name' => 'tên'
        ];
    
        $validationResult = ValidationHelper::validateRequiredFields($data, $requiredFields);
    
        if ($validationResult) {
            return $validationResult;
        }

        try {
            $banner = new Banner([
                'name' => $data['name'],
                'description' => $data['description'],
                'image' => $data['image'],
                'statusId' => 'S1'  // Assuming 'S1' is a valid status ID
            ]);
            $banner->save();

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
    public function deleteBanner($id)
    {
        if (empty($id)) {
            return [
                'errCode' => 1,
                'errMessage' => 'Missing required parameter!'
            ];
        }

        try {
            $banner = Banner::find($id);

            if (!$banner) {
                return [
                    'errCode' => 2,
                    'errMessage' => 'Banner not found!'
                ];
            }

            $banner->delete();

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
