<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignRecipientSend extends Model
{
    protected $fillable = [
        'campaign_message_id',
        'email_recipient_id',
        'sent_at',
        'failed_at',
        'error_message',
        'last_attempted_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'last_attempted_at' => 'datetime',
    ];
}
