<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'name',
        'begin_time',
        'end_time',
        'repetition_state',
        'elapsed_days',
        'memo',
        'tag1_id',
        'tag2_id',
        'tag3_id',
        'is_set_alerm',
        'alerm_time',
        'is_duplication',
        'color',
        'template_name',
    ];

    protected $casts = [
        'begin_time' => 'datetime',
        'end_time' => 'datetime',
        'reptition_state' => 'integer',
        'elapsed_days' => 'integer',
        'is_set_alerm' => 'boolean',
        'alerm_time' => 'datetime',
        'is_duplication' => 'boolean',
    ];
}
