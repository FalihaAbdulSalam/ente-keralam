<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Get user dashboard data
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Calculate profile completion if not already done
        if ($user->profile_completion_percentage === 0) {
            $user->calculateProfileCompletion();
        }

        // Get or create user points
        $userPoints = $user->points()->with('currentBadge')->first();
        
        if (!$userPoints) {
            $userPoints = UserPoint::create([
                'user_id' => $user->id,
                'total_points' => 0,
            ]);
            $userPoints->updateBadge();
            $userPoints->load('currentBadge');
        }

        // Get recent activities
        $activities = UserActivity::where('user_id', $user->id)
            ->with('activityCategory')
            ->completed()
            ->recent(10)
            ->get()
            ->values();

        $activities = $activities->map(function ($activity) {
                $pointsBreakdown = null;

                if ($activity->activity_type === 'quiz' && $activity->max_score) {
                    $pointsPerCorrect = (int) ($activity->activityCategory?->points_per_completion ?? 0);
                    $correctPoints = (int) ($activity->score ?? 0) * $pointsPerCorrect;
                    $participationPoints = max(
                        (int) ($activity->points_earned ?? 0) - $correctPoints,
                        0
                    );

                    $pointsBreakdown = [
                        'participation' => $participationPoints,
                        'per_correct' => $pointsPerCorrect,
                        'correct_points' => $correctPoints,
                        'score' => $activity->score ?? 0,
                        'max_score' => $activity->max_score ?? 0,
                        'total' => $activity->points_earned,
                    ];
                }

                return [
                    'id' => $activity->id,
                    'activity' => $activity->activityCategory->name,
                    'name' => $activity->activity_name,
                    'mark' => $activity->max_score 
                        ? "{$activity->score} / {$activity->max_score}" 
                        : $activity->status,
                    'certificate' => $activity->certificate_url,
                    'points_earned' => $activity->points_earned,
                    'points_breakdown' => $pointsBreakdown,
                    'completed_at' => $activity->completed_at?->format('M d, Y'),
                ];
            });

        // Get activity history
        $history = UserActivity::where('user_id', $user->id)
            ->orderBy('completed_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($activity) {
                return [
                    'date' => $activity->completed_at?->format('M d, Y'),
                    'text' => $this->getHistoryText($activity),
                    'icon' => $activity->activityCategory->icon ?? 'default',
                ];
            });

        return response()->json([
            'user' => [
                'name' => $user->name,
                'id' => $user->id,
                'avatar' => $user->avatar ?? '/design/assets/image.png',
                'profile_completion' => $user->profile_completion_percentage,
                'is_profile_complete' => $user->is_profile_complete,
            ],
            'stats' => [
                'total_points' => $userPoints->total_points,
                'profile_completion' => $user->profile_completion_percentage,
                'badge' => $userPoints->currentBadge ? [
                    'name' => $userPoints->currentBadge->name,
                    'icon' => $userPoints->currentBadge->icon,
                    'color' => $userPoints->currentBadge->color,
                ] : null,
                'activities_count' => $activities->count(),
            ],
            'activities' => $activities,
            'history' => $history,
        ]);
    }

    /**
     * Get user activity history
     */
    public function activities(Request $request)
    {
        $user = $request->user();
        
        $activities = UserActivity::where('user_id', $user->id)
            ->with('activityCategory')
            ->completed()
            ->orderBy('completed_at', 'desc')
            ->paginate(20);

        return response()->json($activities);
    }

    /**
     * Get user points and badge info
     */
    public function points(Request $request)
    {
        $user = $request->user();
        $userPoints = $user->points()->with('currentBadge')->first();

        if (!$userPoints) {
            $userPoints = UserPoint::create([
                'user_id' => $user->id,
                'total_points' => 0,
            ]);
            $userPoints->updateBadge();
            $userPoints->load('currentBadge');
        }

        return response()->json([
            'total_points' => $userPoints->total_points,
            'badge' => $userPoints->currentBadge,
        ]);
    }

    /**
     * Generate history text based on activity
     */
    private function getHistoryText($activity): string
    {
        $type = $activity->activityCategory->name ?? 'Activity';
        return match ($activity->activity_type) {
            'quiz' => "Participated in {$activity->activity_name}",
            'pledge' => "Made a pledge: {$activity->activity_name}",
            'poll' => "Voted in poll: {$activity->activity_name}",
            'competition' => "Entered competition: {$activity->activity_name}",
            'task' => "Completed task: {$activity->activity_name}",
            'discussion' => "Engaged in discussion: {$activity->activity_name}",
            'profile-completion' => "Completed profile information",
            default => "{$type}: {$activity->activity_name}",
        };
    }
}
