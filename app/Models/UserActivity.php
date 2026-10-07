<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_category_id',
        'activity_name',
        'activity_type',
        'activity_id',
        'points_earned',
        'status',
        'score',
        'max_score',
        'certificate_url',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'metadata' => 'array',
        'score' => 'decimal:2',
    ];

    /**
     * Get the user who owns this activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the activity category
     */
    public function activityCategory()
    {
        return $this->belongsTo(ActivityCategory::class);
    }

    /**
     * Scope for completed activities
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('completed_at', 'desc')->limit($limit);
    }
}
