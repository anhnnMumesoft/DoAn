<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;
    protected $table = 'productdetails';

    protected $fillable = [
        'productId',
        'nameDetail',
        'originalPrice',
        'discountPrice',
        'description'
    ];
    protected $appends = ['productDetailSize'];
    public function product()
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_detail_id');
    }
    public function productDetailSizes()
    {
        return $this->hasMany(ProductDetailSize::class, 'productdetail_id');
    }
    public function getProductDetailSizeAttribute()
    {
        return $this->productDetailSizes()->get();
    }
}
