<?php

namespace App\Services;

use App\Models\ActivityCategory;
use App\Models\ContestPoint;
use App\Models\Poll\Poll;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ActivityService
{
    /**
     * Record a user activity and award points
     *
     * @param User $user
     * @param string $activityType (quiz, pledge, poll, task, etc.)
     * @param int $activityId
     * @param string $activityName
     * @param array $additionalData (score, max_score, metadata, etc.)
     * @return UserActivity
     */
    public function recordActivity(
        User $user,
        string $activityType,
        int $activityId,
        string $activityName,
        array $additionalData = []
    ): UserActivity {
        return DB::transaction(function () use ($user, $activityType, $activityId, $activityName, $additionalData) {
            
            // Check if activity already completed
            $existingActivity = UserActivity::where('user_id', $user->id)
                ->where('activity_type', $activityType)
                ->where('activity_id', $activityId)
                ->where('status', 'completed')
                ->first();

            if ($existingActivity) {
                // Activity already completed, don't award points again
                Log::info("Activity already completed", [
                    'user_id' => $user->id,
                    'activity_type' => $activityType,
                    'activity_id' => $activityId
                ]);
                return $existingActivity;
            }

            // Get activity category
            $category = $this->getActivityCategory($activityType);
            
            // Calculate points
            $pointsEarned = $this->calculatePoints($category, $activityType, $activityId, $additionalData);

            // Create activity record
            $activity = UserActivity::create([
                'user_id' => $user->id,
                'activity_category_id' => $category->id,
                'activity_name' => $activityName,
                'activity_type' => $activityType,
                'activity_id' => $activityId,
                'points_earned' => $pointsEarned,
                'status' => 'completed',
                'score' => $additionalData['score'] ?? null,
                'max_score' => $additionalData['max_score'] ?? null,
                'certificate_url' => $additionalData['certificate_url'] ?? null,
                'completed_at' => now(),
                'metadata' => $additionalData['metadata'] ?? null,
            ]);

            // Award points to user
            $this->awardPoints($user, $pointsEarned);

            Log::info("Activity recorded successfully", [
                'user_id' => $user->id,
                'activity_type' => $activityType,
                'activity_id' => $activityId,
                'points_earned' => $pointsEarned
            ]);

            return $activity;
        });
    }

    /**
     * Get or create activity category
     */
    private function getActivityCategory(string $activityType): ActivityCategory
    {
        $categoryMap = [
            'quiz' => ['name' => 'Quiz', 'slug' => 'quiz'],
            'pledge' => ['name' => 'Pledge', 'slug' => 'pledge'],
            'poll' => ['name' => 'Poll', 'slug' => 'poll'],
            'task' => ['name' => 'Task', 'slug' => 'task'],
            'competition' => ['name' => 'Competition', 'slug' => 'competition'],
            'profile_completion' => ['name' => 'Profile Completion', 'slug' => 'profile_completion'],
        ];

        $categoryData = $categoryMap[$activityType] ?? ['name' => ucfirst($activityType), 'slug' => $activityType];

        return ActivityCategory::firstOrCreate(
            ['slug' => $categoryData['slug']],
            [
                'name' => $categoryData['name'],
                'description' => "Complete {$categoryData['name']} activities",
                'points_per_completion' => 0,
                'icon' => null,
            ]
        );
    }

    /**
     * Calculate points based on activity type and performance
     */
    private function calculatePoints(
        ActivityCategory $category,
        string $activityType,
        int $activityId,
        array $additionalData
    ): int
    {
        $basePoints = $category->points_per_completion;

        if ($activityType === 'competition') {
            if (Schema::hasTable('contest_points')) {
                $contestPoints = ContestPoint::where('contest_id', $activityId)->first();
                if ($contestPoints) {
                    return (int) $contestPoints->participation_points;
                }
            }

            return $basePoints;
        }

        if ($activityType === 'poll') {
            $poll = Poll::find($activityId);
            if ($poll && $poll->points !== null) {
                return (int) $poll->points;
            }

            return $basePoints;
        }

        if ($activityType === 'pledge') {
            $pledge = DB::table('tbl_pledge')->where('pledge_id', $activityId)->first();
            if ($pledge && $pledge->pledge_score !== null) {
                return (int) $pledge->pledge_score;
            }

            return $basePoints;
        }

        if ($activityType === 'quiz') {
            $correctAnswers = (int) ($additionalData['score'] ?? 0);
            $pointsPerCorrect = $basePoints;

            return $basePoints + ($correctAnswers * $pointsPerCorrect);
        }

        return $basePoints;
    }

    /**
     * Award points to user and update badge
     */
    private function awardPoints(User $user, int $points): void
    {
        $userPoints = $user->points()->first();

        if (!$userPoints) {
            $userPoints = UserPoint::create([
                'user_id' => $user->id,
                'total_points' => 0,
                'current_badge_id' => null,
            ]);
        }

        $userPoints->addPoints($points);
    }

    /**
     * Get user's activity summary
     */
    public function getUserActivitySummary(User $user): array
    {
        $activities = UserActivity::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('activityCategory')
            ->get();

        $summary = [
            'total_activities' => $activities->count(),
            'total_points' => $activities->sum('points_earned'),
            'by_category' => $activities->groupBy('activityCategory.name')->map(function ($categoryActivities) {
                return [
                    'count' => $categoryActivities->count(),
                    'points' => $categoryActivities->sum('points_earned'),
                ];
            }),
            'recent_activities' => $activities->sortByDesc('completed_at')->take(10)->values(),
        ];

        return $summary;
    }

    /**
     * Check if user has completed an activity
     */
    public function hasCompletedActivity(User $user, string $activityType, int $activityId): bool
    {
        return UserActivity::where('user_id', $user->id)
            ->where('activity_type', $activityType)
            ->where('activity_id', $activityId)
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Get activity completion status
     */
    public function getActivityStatus(User $user, string $activityType, int $activityId): ?UserActivity
    {
        return UserActivity::where('user_id', $user->id)
            ->where('activity_type', $activityType)
            ->where('activity_id', $activityId)
            ->first();
    }
}
