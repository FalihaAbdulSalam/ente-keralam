<?php

namespace App\Models\Poll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $table = 'polls';

    protected $fillable = [
        'festival_id',
        'event_id',
        'name',
        'topic',
        'poster',
        'banner',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'about',
        'terms_condition',
        'attachment',
        'points',
        'status',
    ];

    // ✅ Relationships
    public function festival()
    {
        return $this->belongsTo(\App\Models\Quiz\Festival::class, 'festival_id');
    }

    public function event()
    {
        return $this->belongsTo(\App\Models\Quiz\Event::class, 'event_id');
    }

    public function questions()
    {
        return $this->hasMany(\App\Models\Poll\PollQuestion::class, 'poll_id');
    }
}
