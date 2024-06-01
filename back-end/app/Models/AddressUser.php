<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressUser extends Model
{
    use HasFactory;
    protected $table = 'addressusers';
    protected $fillable = [
        'user_id', 'ship_name', 'ship_address', 'ship_email', 'ship_phonenumber'
    ];
    // Định nghĩa mối quan hệ, ví dụ với User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
