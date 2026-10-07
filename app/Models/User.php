<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'dob',
        'gender',
        'district',
        'assembly_constituency',
        'lsg_type',
        'lsg_name',
        'ward_no',
        'provider',
        'provider_id',
        'avatar',
        'address',
        'pincode',
        'profile_completion_percentage',
        'is_profile_complete',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_profile_complete' => 'boolean',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_url'];

    /**
     * Get the avatar URL attribute
     */
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return null;
        }

        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            if ($this->provider === 'facebook') {
                return null;
            }

            return $this->avatar;
        }

        if (Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        return null;
    }

    /**
     * Hide Facebook avatar URLs (they are not publicly accessible)
     */
    public function getAvatarAttribute($value)
    {
        if (!$value) {
            return $value;
        }

        if ($this->provider === 'facebook' && filter_var($value, FILTER_VALIDATE_URL)) {
            return null;
        }

        return $value;
    }

    /**
     * Get the user's points record
     */
    public function points()
    {
        return $this->hasOne(UserPoint::class);
    }

    /**
     * Get the user's activities
     */
    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Calculate and update profile completion percentage
     */
    public function calculateProfileCompletion(): int
    {
        $fields = [
            'name',
            'email',
            'phone',
            'dob',
            'gender',
            'district',
            'address',
            'pincode',
            'avatar',
        ];

        $filledFields = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filledFields++;
            }
        }

        // Ensure integer percentage so strict comparison works
        $percentage = (int) round(($filledFields / count($fields)) * 100);
        
        $this->profile_completion_percentage = $percentage;
        $wasComplete = $this->is_profile_complete;
        $this->is_profile_complete = $percentage === 100;
        
        // Award points for profile completion (only once)
        if (!$wasComplete && $this->is_profile_complete) {
            $this->awardProfileCompletionPoints();
        }
        
        $this->save();

        return $percentage;
    }

    /**
     * Award points for completing profile
     */
    protected function awardProfileCompletionPoints(): void
    {
        $category = ActivityCategory::where('slug', 'profile-completion')->first();
        
        if ($category) {
            // Create user points record if doesn't exist
            $userPoints = $this->points()->firstOrCreate(
                ['user_id' => $this->id],
                ['total_points' => 0]
            );

            // Add the activity
            $activity = UserActivity::create([
                'user_id' => $this->id,
                'activity_category_id' => $category->id,
                'activity_name' => 'Profile Completion',
                'activity_type' => 'profile-completion',
                'points_earned' => $category->points_per_completion,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Add points
            $userPoints->addPoints($category->points_per_completion);
        }
    }

    /**
     * Get the user's current badge
     */
    public function getCurrentBadge()
    {
        return $this->points ? $this->points->currentBadge : null;
    }

    /**
     * Get total points
     */
    public function getTotalPoints(): int
    {
        return $this->points ? $this->points->total_points : 0;
    }
}
