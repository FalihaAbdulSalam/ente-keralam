<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContestPoint extends Model
{
    protected $fillable = [
        'contest_id',
        'contest_type',
        'participation_points',
        'bonus_first',
        'bonus_second',
        'bonus_third',
    ];
}
