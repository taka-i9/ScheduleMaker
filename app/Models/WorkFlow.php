<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'deadline',
        'memo',
        'contents_num',
        'tag1_id',
        'tag2_id',
        'tag3_id',
        'color',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'contents_num' => 'integer',
    ];
}
