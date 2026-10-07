<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'festival_id',
        'eventtype_id',
        'name',
        'status',
    ];

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'eventtype_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
