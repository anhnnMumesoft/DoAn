<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeShip extends Model
{
    use HasFactory;
    protected $table = 'typeships';

    protected $fillable = ['type', 'price'];

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'type_ship_id', 'id');
    }


}
