<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'type',
        'name',
        'deadline',
        'required_minutes',
        'rest_minutes',
        'repetition_state',
        'memo',
        'tag1_id',
        'tag2_id',
        'tag3_id',
        'priority_level',
        'color',
        'is_done',
        'template_name',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'required_minutes' => 'integer',
        'rest_minutes' => 'integer',
        'reptition_state' => 'integer',
        'priority_level' => 'integer',
        'is_done' => 'boolean',
    ];
}
