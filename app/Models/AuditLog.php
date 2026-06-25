<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'changes',
        'user_id'
    ];

    protected $casts = [
        'changes' => 'array',
    ];
}
