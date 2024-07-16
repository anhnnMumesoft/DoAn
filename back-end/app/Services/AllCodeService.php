<?php

namespace App\Services;

use App\Models\Allcode;
use App\Models\Blog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\ValidationHelper;

class AllCodeService
{
    public function handleCreateNewAllCode($data)
    {
        if (empty($data['type']) || empty($data['value']) || empty($data['code'])) {
            return ['errCode' => 1, 'errMessage' => 'Thiếu thông số bắt buộc!'];
        }

        $existingCode = Allcode::where('code', $data['code'])->first();
        if ($existingCode) {
            return ['errCode' => 2, 'errMessage' => 'Mã code đã tồn tại!'];
        }

        Allcode::create([
            'type' => $data['type'],
            'value' => $data['value'],
            'code' => $data['code']
        ]);

        return ['errCode' => 0, 'errMessage' => 'ok'];
    }

    public function getAllCodeService($typeInput)
    {
        if (empty($typeInput)) {
            return ['errCode' => 1, 'errMessage' => 'Thiếu thông số bắt buộc!'];
        }

        $allcode = Allcode::where('type', $typeInput)->get();
        return ['errCode' => 0, 'data' => $allcode];
    }

    public function handleUpdateAllCode($data)
    {
        if (empty($data['value']) || empty($data['code']) || empty($data['id'])) {
            return ['errCode' => 1, 'errMessage' => 'Thiếu thông số bắt buộc!'];
        }

        $allcode = Allcode::find($data['id']);
        if (!$allcode) {
            throw new ModelNotFoundException('Allcode not found');
        }

        $allcode->update([
            'value' => $data['value'],
            'code' => $data['code']
        ]);

        return ['errCode' => 0, 'errMessage' => 'ok'];
    }

    public function getDetailAllCodeById($id)
    {
        if (empty($id)) {
            return ['errCode' => 1, 'errMessage' => 'Thiếu thông số bắt buộc!'];
        }

        $allcode = Allcode::find($id);
        if (!$allcode) {
            throw new ModelNotFoundException('Allcode not found');
        }

        return ['errCode' => 0, 'data' => $allcode];
    }

    public function handleDeleteAllCode($allcodeId)
    {
        if (empty($allcodeId)) {
            return ['errCode' => 1, 'errMessage' => 'Thiếu thông số bắt buộc!'];
        }

        $allcode = Allcode::find($allcodeId);
        if (!$allcode) {
            return ['errCode' => 2, 'errMessage' => 'allCode không tồn tại'];
        }

        $allcode->delete();
        return ['errCode' => 0, 'message' => 'AllCode đã bị xóa'];
    }

    public function getListAllCodeService($data)
    {
        $query = Allcode::query();
        $queryCount = Allcode::query();

        if (!empty($data['type'])) {
            $query->where('type', $data['type']);
            $queryCount->where('type', $data['type']);
        }

        if (!empty($data['keyword'])) {
            $query->where('value', 'like', '%' . $data['keyword'] . '%');
            $queryCount->where('value', 'like', '%' . $data['keyword'] . '%');
        }

        if (!empty($data['limit']) || !empty($data['offset'])) {
            $query->skip($data['offset'])->take($data['limit']);
        }

        $allcodes = $query->get();
        return ['errCode' => 0, 'data' => $allcodes, 'count' => $queryCount->get()->count()];
    }

    public function getAllCategoryBlog($typeInput)
    {
        if (empty($typeInput)) {
            return ['errCode' => 1, 'errMessage' => 'Thiếu thông số bắt buộc!'];
        }

        $allcodes = Allcode::where('type', $typeInput)->get();
        foreach ($allcodes as $code) {
            $blogs = Blog::where('subjectId', $code->code)->get();
            $code->countPost = $blogs->count();
        }

        return ['errCode' => 0, 'data' => $allcodes];
    }
}
