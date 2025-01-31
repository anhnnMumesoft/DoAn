<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Supplier extends Model
{    use SoftDeletes;
    use HasFactory;
    protected $fillable = ['name', 'address', 'phonenumber', 'email'];

    // Định nghĩa các mối quan hệ ở đây nếu cần
    public function someRelation()
    {
        // return $this->hasMany(OtherModel::class);
    }
}
