<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'questions',
        'duration',
        'no_of_options',
        'difficulty_level',
        'status',
    ];
    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id', 'id'); 
    }
}