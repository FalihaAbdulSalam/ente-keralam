<?php

namespace App\Models\Poll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollQuestion extends Model
{
    use HasFactory;

    protected $table = 'poll_questions';

    protected $fillable = [
        'poll_id',
        'question_text',
        'order',
    ];

    // ✅ Relationships

    /**
     * Each question belongs to one poll.
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }

    /**
     * Each question can have multiple options.
     */
    public function options()
    {
        return $this->hasMany(PollOption::class, 'poll_question_id');
    }
}
