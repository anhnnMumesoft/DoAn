<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AddressUser extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'addressusers';
    protected $fillable = [
        'user_id', 'ship_name', 'ship_address', 'ship_email', 'ship_phonenumber'
    ];
    // Định nghĩa mối quan hệ, ví dụ với User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function orders()
    {
        return $this->hasMany(OrderProduct::class, 'address_user_id');
    }

}
