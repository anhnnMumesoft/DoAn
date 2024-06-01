<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = ['fromDate', 'toDate', 'typeVoucherId', 'amount', 'codeVoucher'];
    protected $appends = ['typeVoucherOfVoucherData'];
    public function typeVoucher()
    {
        return $this->belongsTo(TypeVoucher::class, 'typeVoucherId', 'id');
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'voucher_id');
    }
    public function getTypeVoucherOfVoucherDataAttribute()
    {
        $typeVoucher = $this->typeVoucher()->first();
        return $typeVoucher ? $typeVoucher : null;
    }

}
