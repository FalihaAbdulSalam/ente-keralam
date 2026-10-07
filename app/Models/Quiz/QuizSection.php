<?php

namespace App\Models\Quiz;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'sectiontype_id',
        'quiz_date',
        'quiz_time',
        'duration',
        'familarization_time',
        'no_of_questions',
        'status',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class); 
    }

    
}