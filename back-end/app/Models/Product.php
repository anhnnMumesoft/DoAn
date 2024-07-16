<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{    use SoftDeletes;
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'name', 'contentHTML', 'contentMarkdown', 'statusId', 'categoryId', 'view', 'madeby', 'material', 'brandId'
    ];
    protected $appends = ['categoryData', 'brandData','statusData'];

    public function categoryData()
    {
        return $this->belongsTo(Allcode::class, 'categoryId', 'code');
    }

    public function brandData()
    {
        return $this->belongsTo(Allcode::class, 'brandId', 'code');
    }

    public function statusData()
    {
        return $this->belongsTo(Allcode::class, 'statusId', 'code');
    }

//    public function productDetails()
//    {
//        return $this->hasMany(ProductDetail::class, 'productId');
//    }
    public function getCategoryDataAttribute()
    {
        $category = $this->categoryData()->first();
        return $category ? ['id' => $category->id, 'value' => $category->value, 'code' => $category->code] : ['value' => null, 'code' => null];
    }

    public function getBrandDataAttribute()
    {
        $brand = $this->brandData()->first();
        return $brand ? ['id' => $brand->id, 'value' => $brand->value, 'code' => $brand->code] : ['value' => null, 'code' => null];
    }

    public function getStatusDataAttribute()
    {
        $status = $this->statusData()->first();
        return $status ? ['id' => $status->id, 'value' => $status->value, 'code' => $status->code] : ['value' => null, 'code' => null];
    }
//    public function getProductDetailAttribute()
//    {
//        // Fetch the actual related data with related sub-data
//        return $this->productDetails()->with(['productDetailSizes', 'productImages'])->get();
//    }

}
