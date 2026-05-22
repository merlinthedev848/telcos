<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'module',
        'operation',
        'subject_type',
        'subject_id',
        'actor_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
