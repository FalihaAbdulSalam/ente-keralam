<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'status',
        'department',
        'last_date',
        'total_votes'
    ];

    protected $casts = [
        'last_date' => 'date',
    ];
}
