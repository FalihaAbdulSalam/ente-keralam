<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_points',
        'current_badge_id',
    ];

    /**
     * Get the user who owns these points
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the current badge
     */
    public function currentBadge()
    {
        return $this->belongsTo(Badge::class, 'current_badge_id');
    }

    /**
     * Add points and update badge
     */
    public function addPoints(int $points): void
    {
        $this->total_points += $points;
        $this->updateBadge();
        $this->save();
    }

    /**
     * Update the badge based on total points
     */
    public function updateBadge(): void
    {
        $badge = Badge::getBadgeForPoints($this->total_points);
        if ($badge) {
            $this->current_badge_id = $badge->id;
        }
    }
}
