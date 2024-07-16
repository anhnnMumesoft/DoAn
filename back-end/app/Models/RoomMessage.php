<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class RoomMessage extends Model
{    use SoftDeletes;
    use HasFactory;
    protected $fillable = ['userOne', 'userTwo'];
}
