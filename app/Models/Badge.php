<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_points',
        'max_points',
        'icon',
        'color',
        'description',
    ];

    /**
     * Get the badge for a given points value
     */
    public static function getBadgeForPoints(int $points): ?Badge
    {
        return self::where('min_points', '<=', $points)
            ->where(function ($query) use ($points) {
                $query->where('max_points', '>=', $points)
                    ->orWhereNull('max_points');
            })
            ->first();
    }

    /**
     * Users who have this badge
     */
    public function users()
    {
        return $this->hasMany(UserPoint::class, 'current_badge_id');
    }
}
