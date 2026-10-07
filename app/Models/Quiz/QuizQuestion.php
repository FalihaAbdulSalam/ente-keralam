<?php

namespace App\Models\Quiz;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_bank_id',
        'status',
        'quiz_questionscnt',
    ];

    /**
     * Relationships
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class);
    }

    /**
     * Scope to get active quiz questions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Assign multiple selected questions (checkbox selection)
     *
     * @param int $quizId
     * @param array $questionIds
     */
    public static function assignMultiple($quizId, array $questionIds)
    {
        $data = [];

        foreach ($questionIds as $questionId) {
            // Prevent duplicate entry for same quiz + question
            if (!static::where('quiz_id', $quizId)->where('question_bank_id', $questionId)->exists()) {
                $data[] = [
                    'quiz_id' => $quizId,
                    'question_bank_id' => $questionId,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($data)) {
            static::insert($data);
        }
    }
}
