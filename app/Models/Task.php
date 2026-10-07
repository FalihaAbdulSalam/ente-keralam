<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'status',
        'last_date',
        'department',
        'participants_count'
    ];

    protected $casts = [
        'last_date' => 'date',
    ];
}
