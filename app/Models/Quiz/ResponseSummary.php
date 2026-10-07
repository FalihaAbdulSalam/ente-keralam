<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'quiz_section_id',
        'participant_id',
        'correct_ans_cnt',
    ];

    // Relationships
    public function quizResponse()
    {
        return $this->belongsTo(QuizResponse::class);
    }

    public function quizSection()
    {
        return $this->belongsTo(QuizSection::class);
    }

    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }
}