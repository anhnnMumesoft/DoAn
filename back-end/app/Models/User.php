<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'email', 'password', 'firstName', 'lastName', 'address', 'genderId',
        'phonenumber', 'image', 'dob', 'isActiveEmail', 'roleId', 'statusId', 'usertoken'
    ];

    protected $hidden = [
        'role_data', // Assuming this is the attribute name that gets automatically included
        'gender_data', // Assuming this is the attribute name that gets automatically included
    ];

    protected $appends = ['roleData', 'genderData'];

    public function roleData()
    {
        return $this->belongsTo(Allcode::class, 'roleId', 'code');
    }

    public function genderData()
    {
        return $this->belongsTo(Allcode::class, 'genderId', 'code');
    }

    public function getRoleDataAttribute()
    {
        $role = $this->roleData()->first();
        return $role ? ['id' => $role->id, 'value' => $role->value, 'code' => $role->code] : [
            'value' => null,
            'code' => null
        ];
    }

    public function getGenderDataAttribute()
    {
        $gender = $this->genderData()->first();
        return $gender ? ['id' => $gender->id, 'value' => $gender->value, 'code' => $gender->code] :[
            'value' => null,
            'code' => null
        ];
    }
}
