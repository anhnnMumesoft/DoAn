<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = [
        'shortdescription',
        'title',
        'subjectId',
        'statusId',
        'image',
        'contentMarkdown',
        'contentHTML',
        'userId',
        'view'
    ];
    protected $appends = ['subjectData'];

    public function subjectData()
    {
        return $this->belongsTo(Allcode::class, 'subjectId', 'code');
    }
    public function getSubjectDataAttribute()
    {
        $role = $this->subjectData()->first();
        return $role ? ['id' => $role->id, 'value' => $role->value, 'code' => $role->code] : [
            'value' => null,
            'code' => null
        ];
    }


}
