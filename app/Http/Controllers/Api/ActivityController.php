<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContestPoint;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ActivityController extends Controller
{
    protected $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Submit quiz completion
     */
    public function submitQuiz(Request $request, $id)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:1000',
            'max_score' => 'required|integer|min:1|max:1000',
            'quiz_title' => 'required|string|max:255',
            'answers' => 'sometimes|array',
        ]);

        try {
            $user = $request->user();
            
            // Sanitize inputs
            $quizTitle = strip_tags(trim($validated['quiz_title']));
            $score = (int) $validated['score'];
            $maxScore = (int) $validated['max_score'];
            $quizId = (int) $id;
            
            $activity = $this->activityService->recordActivity(
                $user,
                'quiz',
                $quizId,
                $quizTitle,
                [
                    'score' => $score,
                    'max_score' => $maxScore,
                    'metadata' => [
                        'quiz_id' => $quizId,
                        'answers' => $validated['answers'] ?? [],
                        'completion_time' => now()->toDateTimeString(),
                    ]
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Quiz completed successfully!',
                'data' => [
                    'activity' => $activity,
                    'points_earned' => $activity->points_earned,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Quiz submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record quiz completion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit pledge completion
     */
    public function submitPledge(Request $request, $id)
    {
        $validated = $request->validate([
            'pledge_title' => 'required|string|max:255',
        ]);

        try {
            $user = $request->user();
            
            // Sanitize inputs
            $pledgeTitle = strip_tags(trim($validated['pledge_title']));
            $pledgeId = (int) $id;
            
            $activity = $this->activityService->recordActivity(
                $user,
                'pledge',
                $pledgeId,
                $pledgeTitle,
                [
                    'metadata' => [
                        'completion_time' => now()->toDateTimeString(),
                    ]
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Pledge completed successfully!',
                'data' => [
                    'activity' => $activity,
                    'points_earned' => $activity->points_earned,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Pledge submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record pledge completion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit poll vote
     */
    public function submitPoll(Request $request, $id)
    {
        $validated = $request->validate([
            'poll_title' => 'required_without:poll_name|string|max:255',
            'poll_name' => 'required_without:poll_title|string|max:255',
            'option_id' => 'nullable|integer|min:1',
        ]);

        try {
            $user = $request->user();
            
            // Sanitize inputs
            $pollTitle = strip_tags(trim($validated['poll_title'] ?? $validated['poll_name']));
            $optionId = isset($validated['option_id']) ? (int) $validated['option_id'] : null;
            $pollId = (int) $id;

            $metadata = [
                'completion_time' => now()->toDateTimeString(),
            ];

            if ($optionId !== null) {
                $metadata['option_id'] = $optionId;
            }
            
            $activity = $this->activityService->recordActivity(
                $user,
                'poll',
                $pollId,
                $pollTitle,
                [
                    'metadata' => $metadata,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Poll vote recorded successfully!',
                'data' => [
                    'activity' => $activity,
                    'points_earned' => $activity->points_earned,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Poll submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record poll vote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit task completion
     */
    public function submitTask(Request $request, $id)
    {
        $validated = $request->validate([
            'task_title' => 'required|string|max:255',
            'proof_url' => 'sometimes|string|url|max:500',
        ]);

        try {
            $user = $request->user();
            
            // Sanitize inputs
            $taskTitle = strip_tags(trim($validated['task_title']));
            $proofUrl = isset($validated['proof_url']) ? filter_var($validated['proof_url'], FILTER_SANITIZE_URL) : null;
            $taskId = (int) $id;
            
            $activity = $this->activityService->recordActivity(
                $user,
                'task',
                $taskId,
                $taskTitle,
                [
                    'metadata' => [
                        'proof_url' => $proofUrl,
                        'completion_time' => now()->toDateTimeString(),
                    ]
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Task completed successfully!',
                'data' => [
                    'activity' => $activity,
                    'points_earned' => $activity->points_earned,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Task submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record task completion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit competition completion
     */
    public function submitCompetition(Request $request, $id)
    {
        $validated = $request->validate([
            'contest_name' => 'required|string|max:255',
            'contest_type' => 'required|string|in:Reel,Photo,Video,Essay,Poem',
            'score' => 'required|integer|min:0|max:1000',
        ]);

        try {
            $user = $request->user();
            
            // Sanitize inputs
            $contestName = strip_tags(trim($validated['contest_name']));
            $contestType = strip_tags(trim($validated['contest_type']));
            $score = (int) $validated['score'];
            $contestId = (int) $id;
            
            // Verify the contest exists and score matches (security check)
            // This prevents users from awarding themselves arbitrary points
            $contest = $this->verifyContest($contestId, $score);
            if (!$contest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid contest or score mismatch',
                ], 400);
            }
            
            // Check if user has already been awarded points for this contest
            $existingActivity = $this->activityService->hasCompletedActivity($user, 'competition', $contestId);
            if ($existingActivity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Points already awarded for this competition',
                ], 409); // 409 Conflict
            }
            
            $activity = $this->activityService->recordActivity(
                $user,
                'competition',
                $contestId,
                $contestName,
                [
                    'score' => $score,
                    'max_score' => $score,
                    'metadata' => [
                        'contest_type' => $contestType,
                        'completion_time' => now()->toDateTimeString(),
                    ]
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Competition submission recorded successfully!',
                'data' => [
                    'activity' => $activity,
                    'points_earned' => $activity->points_earned,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Competition submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record competition submission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify contest exists and score matches
     * Prevents users from submitting arbitrary scores
     */
    private function verifyContest($contestId, $score)
    {
        if (!Schema::hasTable('contest_points')) {
            return false;
        }

        $contestPoints = ContestPoint::where('contest_id', $contestId)->first();
        if (!$contestPoints) {
            return false;
        }

        if ((int) $contestPoints->participation_points !== (int) $score) {
            return false;
        }

        return $contestPoints->toArray();
    }

    /**
     * Check if user has completed an activity
     */
    public function checkCompletion(Request $request, $type, $id)
    {
        try {
            $user = $request->user();
            
            $hasCompleted = $this->activityService->hasCompletedActivity($user, $type, $id);
            $activity = $this->activityService->getActivityStatus($user, $type, $id);

            return response()->json([
                'success' => true,
                'data' => [
                    'has_completed' => $hasCompleted,
                    'activity' => $activity,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Check completion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check completion status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's activity summary
     */
    public function summary(Request $request)
    {
        try {
            $user = $request->user();
            $summary = $this->activityService->getUserActivitySummary($user);

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            Log::error('Get summary failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get activity summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
