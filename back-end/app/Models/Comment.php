<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Comment extends Model
{    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'content', 'parentId', 'productId', 'userId', 'blogId', 'star', 'image'
    ];

    // Định nghĩa các quan hệ ở đây nếu cần
    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
