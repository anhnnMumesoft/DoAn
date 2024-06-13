<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'orderdetails'; // Tên bảng trong database, chỉ cần nếu tên bảng không phải là số nhiều của tên model

    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'real_price'
    ];

    // Định nghĩa các mối quan hệ ở đây nếu cần
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(OrderProduct::class, 'order_id');
    }
    public function productDetailSize()
    {
        return $this->belongsTo(ProductDetailSize::class, 'product_id', 'id');
    }
    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class, 'product_id', 'id');
    }
}
