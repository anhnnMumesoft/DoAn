<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'supplier_id'];
    protected $table = 'receipts';

    public function receiptDetails()  // Keep it plural if it's one-to-many
    {
        return $this->hasMany(ReceiptDetail::class, 'receipt_id', 'id');
    }

}
