<?php
namespace App\Models\Quiz;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_name',
        'answer_flag',
        'order_no',
        'status',
    ];

    // Define relationship with QuizQuestion
    public function question()
    {
        return $this->belongsTo(QuizQuestion::class);
    }
}