<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignMessage extends Model
{
    protected $fillable = [
        'slug',
        'subject',
        'body',
        'button_enabled',
        'button_label',
        'button_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'button_enabled' => 'boolean',
    ];
}
