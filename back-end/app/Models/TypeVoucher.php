<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeVoucher extends Model
{
    use HasFactory;
    protected $table = 'typevouchers';
    protected $fillable = ['typeVoucher', 'value', 'maxValue', 'minValue'];
    protected $appends = ['typeVoucherData'];
    public function typeVoucherData()
    {
        return $this->belongsTo(Allcode::class, 'typeVoucher', 'code');
    }

    public function typeVoucherOfVoucherData()
    {
        return $this->hasMany(Voucher::class, 'typeVoucherId');
    }
    public function getTypeVoucherDataAttribute()
    {
        $typeVoucher = $this->typeVoucherData()->first();
        return $typeVoucher ? ['value' => $typeVoucher->value, 'code' => $typeVoucher->code] : [
            'value' => null,
            'code' => null
        ];
    }
}
