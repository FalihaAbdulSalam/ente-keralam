<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'description',
        'quiz_date',
        'is_daily_quiz',
        'status'
    ];

    protected $casts = [
        'quiz_date' => 'date',
        'is_daily_quiz' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}
