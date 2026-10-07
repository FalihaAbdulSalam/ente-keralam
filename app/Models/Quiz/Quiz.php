<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz\Festival;
use App\Models\Quiz\Event;
use App\Models\Quiz\QuizQuestion;
use App\Models\Quiz\QuizResponse;
use App\Models\Quiz\ResponseSummary;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'festival_id',
        'event_id',
        'section_type',
        'name',
        'topic',
        'poster',
        'banner',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'about',
        'result',
        'terms_condition',
        'attachment',
        'points',
        'status',
    ];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function responses()
    {
        return $this->hasMany(QuizResponse::class);
    }

    public function summaries()
    {
        return $this->hasMany(ResponseSummary::class);
    }
}
