<?php

namespace App\Modules\Email\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailMessage extends Model
{
    protected $fillable = [
        'email_thread_id',
        'direction',
        'from',
        'to',
        'subject',
        'body',
        'headers',
    ];

    protected $casts = [
        'headers' => 'array',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(EmailThread::class, 'email_thread_id');
    }
}
