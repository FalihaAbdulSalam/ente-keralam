<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'points_per_completion',
        'icon',
    ];

    /**
     * User activities in this category
     */
    public function userActivities()
    {
        return $this->hasMany(UserActivity::class);
    }
}
