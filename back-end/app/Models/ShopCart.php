<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopCart extends Model
{
    use HasFactory;
    protected $table = 'shopcarts'; // Tên bảng trong database, chỉ cần nếu tên bảng không tuân theo quy tắc đặt tên của Laravel

    protected $fillable = [
        'userId', 'productdetailsizeId', 'quantity', 'statusId'
    ];

    // Định nghĩa các mối quan hệ ở đây, ví dụ:
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'userId');
    }
}
