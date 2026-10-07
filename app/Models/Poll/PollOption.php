<?php

namespace App\Models\Poll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $table = 'poll_options';

    protected $fillable = [
        'poll_id',
        'poll_question_id',
        'option_text',
        'votes',
    ];

    /**
     * Each option belongs to a poll.
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }

    /**
     * Each option belongs to one question.
     */
    public function question()
    {
        return $this->belongsTo(PollQuestion::class, 'poll_question_id');
    }
}
