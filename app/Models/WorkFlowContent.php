<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkFlowContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workflow_id',
        'contents_id',
        'name',
        'required_minutes',
        'rest_minutes',
        'margin_left',
        'margin_top',
        'is_done',
    ];

    protected $casts = [
        'required_minutes' => 'integer',
        'rest_minutes' => 'integer',
        'is_done' => 'boolean',
    ];
}
