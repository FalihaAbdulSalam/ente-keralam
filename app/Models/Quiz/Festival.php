<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Festival extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'name',
        'status',
        'start_date',
        'end_date',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
