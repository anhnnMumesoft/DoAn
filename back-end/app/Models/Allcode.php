<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Allcode extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'allcodes'; // Tên bảng trong database, chỉnh sửa nếu tên khác
    protected $fillable = ['type', 'value', 'code']; // Các trường có thể được mass assigned

    public function genderData() {
        return $this->hasMany(User::class, 'genderId');
    }

    public function roleData() {
        return $this->hasMany(User::class, 'roleId');
    }

    public function categoryData() {
        return $this->hasMany(Product::class, 'categoryId');
    }

    public function brandData() {
        return $this->hasMany(Product::class, 'brandId');
    }

    public function statusData() {
        return $this->hasMany(Product::class, 'statusId');
    }

    public function subjectData() {
        return $this->hasMany(Blog::class, 'subjectId');
    }

    public function typeVoucherData() {
        return $this->hasMany(TypeVoucher::class, 'typeVoucher');
    }

    public function sizeData() {
        return $this->hasMany(ProductDetailSize::class, 'sizeId');
    }

    public function statusOrderData() {
        return $this->hasMany(OrderProduct::class, 'statusId');
    }
}
