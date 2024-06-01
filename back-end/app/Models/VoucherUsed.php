<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherUsed extends Model
{
    use HasFactory;
    protected $table = 'voucheruseds';
    protected $fillable = ['voucherId', 'userId', 'status'];

    // Define relationships if necessary
    // For example, if VoucherUsed belongs to a User and Voucher
    public function user() {
        return $this->belongsTo(User::class, 'userId');
    }

    public function voucher() {
        return $this->belongsTo(Voucher::class, 'voucherId');
    }
}
