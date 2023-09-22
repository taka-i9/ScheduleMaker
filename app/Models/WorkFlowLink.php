<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkFlowLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workflow_id',
        'start_id',
        'end_id',
    ];
}
