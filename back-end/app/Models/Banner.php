<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{    use SoftDeletes;
    use HasFactory;
    protected $fillable = ['description', 'name', 'statusId', 'image'];

    // Định nghĩa các mối quan hệ ở đây nếu cần
    public function someRelation()
    {
        // return $this->belongsTo(OtherModel::class);
    }
}
