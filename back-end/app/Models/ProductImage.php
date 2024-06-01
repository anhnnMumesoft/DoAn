<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $table = 'productimages';
    protected $fillable = ['caption', 'product_detail_id', 'image'];

    /**
     * Get the product detail that owns the image.
     */
    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'productdetail_id');
    }
}