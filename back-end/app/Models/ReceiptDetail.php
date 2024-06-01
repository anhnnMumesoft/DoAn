<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptDetail extends Model
{
    use HasFactory;
    protected $table = 'receiptdetails';

    protected $fillable = ['receipt_id', 'product_detail_size_id', 'quantity', 'price'];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }

    public function productDetailSize()
    {
        return $this->belongsTo(ProductDetailSize::class, 'product_detail_size_id', 'id');
    }

    // Define the productDetail relationship
    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class, 'product_detail_id', 'id');
    }
}
