<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class ResponsesFromApiQuiz extends Model
{
    protected $table='responses_from_api_quiz';
    protected $guarded = [
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}
