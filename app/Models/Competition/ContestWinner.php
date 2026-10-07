<?php

namespace App\Models\Competition;

use Illuminate\Database\Eloquent\Model;

class ContestWinner extends Model
{
    protected $table = 'contest_winners';

    protected $fillable = [
        'contest_id',
        'user_id',
        'position',
        'point',
        'remarks'
    ];
}
