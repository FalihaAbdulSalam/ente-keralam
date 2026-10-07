<?php

namespace App\Models\Poll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollResponse extends Model
{
    use HasFactory;

    protected $table = 'poll_responses';

    protected $guarded = [];

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
