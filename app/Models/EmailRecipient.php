<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailRecipient extends Model
{
    protected $fillable = [
        'source_sl_no',
        'name',
        'mobile',
        'whatsapp',
        'email',
        'category',
        'source',
        'email_sent',
        'email_sent_at',
    ];

    protected $casts = [
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
    ];
}
