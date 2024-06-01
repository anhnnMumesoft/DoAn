<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetailSize extends Model
{
    use HasFactory;
    protected $table = 'productdetailsizes';
    protected $fillable = ['productdetail_id', 'width', 'height', 'weight', 'size_id'];
    protected $appends = ['sizeData','sizeId'];
    public function sizeData()
    {
        return $this->belongsTo(Allcode::class, 'size_id', 'code');
    }
    public function getSizeDataAttribute()
    {
        $size= $this->sizeData()->first();
        return $size ? ['id' => $size->id, 'value' => $size->value, 'code' => $size->code] : ['value' => null, 'code' => null];
    }
    public function getSizeIdAttribute()
    {
        return $this->attributes['size_id']; // Directly return the size_id from the model's attributes
    }

}
