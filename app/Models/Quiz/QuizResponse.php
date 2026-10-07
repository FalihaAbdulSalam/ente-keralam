<?php
namespace App\Models\Quiz;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_section_id',
        'question_id',
        'option_id',
        'participant_id',
        'ip_address',
        'answer_flag',
        'device_type' , 
        'browser' ,
	'os' ,
	'selected_at_micro',
    ];

    // Relationships
    public function quizSection()
    {
        return $this->belongsTo(QuizSection::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class);
    }

    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }
}
