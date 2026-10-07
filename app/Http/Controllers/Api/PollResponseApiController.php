<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz\Quiz;
use App\Models\UserActivity;
use App\Models\UserPoint;

use App\Models\Poll\PollResponse;
use App\Models\Poll\Poll;
use App\Models\Poll\PollQuestion;
use App\Models\Poll\PollOption;
// use App\Models\Poll\PollAnswer;


use App\Models\Quiz\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Models\Quiz\ResponsesFromApiQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PollResponseApiController extends Controller
{
    private function fileUrl($path, $type = null)
    {
        if (!$path) {
            return null;
        }
    
        // 🔹 Define base folder mapping
        $basePaths = [
            'poster' => 'uploads/quizzes/posters/',
            'banner' => 'uploads/quizzes/banners/',
            'attachment' => 'uploads/quizzes/attachments/',
        ];
    
        // 🔹 If the type is known, prefix the correct folder
        if ($type && !str_starts_with($path, 'uploads/')) {
            $path = $basePaths[$type] . ltrim($path, '/');
        }
    
        // 🔹 Return absolute public URL
        return asset($path);
    }
    
    public function pollresponseStore(Request $request){
          // ✅ VALIDATION (aligned with request usage)
           /* ===============================
         | VALIDATION
         =============================== */
        $validated = $request->validate([
            'user.user_id' => 'required|integer',
            'poll.poll_id' => 'required|integer',
            'poll_answers' => 'required|array|min:1',
            'poll_answers.*.question_id' => 'required|integer',
            'poll_answers.*.selected_option_id' => 'nullable|integer',
            'is_final_submission' => 'nullable|boolean',
        ]);
        // dd($validated);

        /* ===============================
         | EXTRACT PAYLOAD
         =============================== */
        $userId   = $request->input('user.user_id');
        $pollId   = $request->input('poll.poll_id');
        $answers  = $request->input('poll_answers');
        $isFinalSubmission = $request->boolean('is_final_submission', false);

        DB::beginTransaction();

        try {

            /* ===============================
             | DEVICE & META INFO
             =============================== */
            $ip         = $request->ip();
            $userAgent  = $request->header('User-Agent');
            $browser    = $this->detectBrowser($userAgent);
            $os         = $this->detectOS($userAgent);
            $deviceType = $this->detectDevice($userAgent);

            $totalScore = 0;

            /* ===============================
             | SAVE / UPDATE ANSWERS
             | (allows progress save)
             =============================== */
            foreach ($answers as $answer) {

                $questionId = $answer['question_id'];
                $optionId   = $answer['selected_option_id'] ?? null;

                // Check correctness
                $isCorrect = $optionId
                    ? PollOption::where('id', $optionId)
                        // ->where('answer_flag', 1)
                        ->exists()
                    : false;

                $totalScore += $isCorrect ? 1 : 0;

                // Update if already saved, else insert
                PollResponse::updateOrCreate(
                    [
                        'poll_id'          => $pollId,
                        'poll_question_id' => $questionId,
                        'participant_id'   => $userId,
                    ],
                    [
                        'poll_option_id'    => $optionId,
                        'poll_answer_flag'  => $isCorrect ? 1 : 0,
                        'ip_address'        => $ip,
                        'device_type'       => $deviceType,
                        'browser'           => $browser,
                        'os'                => $os,
                        'selected_at_micro' => (int) (microtime(true) * 1000000),
                    ]
                );
            }

            /* ===============================
             | SCORE & POINT CALCULATION
             =============================== */
            $poll = Poll::findOrFail($pollId);

            $totalQuestions = count($answers);
            $pollMaxPoints  = $poll->points ?? 10;

            $pointsEarned = $totalQuestions > 0
                ? (int) round(($totalScore / $totalQuestions) * $pollMaxPoints)
                : 0;

            $totalPoints = 0;
            $isNewActivity = false;

            /* ===============================
             | USER ACTIVITY (FINAL SUBMIT ONLY)
             =============================== */
            if ($isFinalSubmission) {

                $existingActivity = UserActivity::where([
                    'user_id' => $userId,
                    'activity_type' => 'poll',
                    'activity_id' => $pollId,
                    'status' => 'completed',
                ])->first();

                if (!$existingActivity) {

                    $isNewActivity = true;

                    UserActivity::create([
                        'activity_category_id' => 3,
                        'activity_name'        => $poll->title ?? 'Poll',
                        'activity_type'        => 'poll',
                        'activity_id'          => $pollId,
                        'points_earned'        => $pointsEarned,
                        'score'                => $totalScore,
                        'max_score'            => $totalQuestions,
                        'status'               => 'completed',
                        'completed_at'         => now(),
                        'user_id'              => $userId,
                    ]);

                    $userPoints = UserPoint::firstOrCreate(
                        ['user_id' => $userId],
                        ['total_points' => 0]
                    );

                    $userPoints->addPoints($pointsEarned);
                    $totalPoints = $userPoints->total_points;

                } else {
                    $userPoints = UserPoint::where('user_id', $userId)->first();
                    $totalPoints = $userPoints->total_points ?? 0;
                    $pointsEarned = $existingActivity->points_earned;
                }

            } else {
                // Progress save
                $userPoints = UserPoint::where('user_id', $userId)->first();
                $totalPoints = $userPoints->total_points ?? 0;
            }

            DB::commit();

            /* ===============================
             | RESPONSE
             =============================== */
            return response()->json([
                'success' => true,
                'message' => $isFinalSubmission
                    ? ($isNewActivity ? 'Poll submitted successfully.' : 'Poll already submitted.')
                    : 'Poll progress saved.',
                'data' => [
                    'points_earned' => $pointsEarned,
                    'total_points' => $totalPoints,
                    'score' => $totalScore,
                    'max_score' => $totalQuestions,
                    'poll_max_points' => $pollMaxPoints,
                    'is_new_activity' => $isNewActivity,
                    'is_final_submission' => $isFinalSubmission,
                ]
            ]);

        } catch (\Exception $e) {

            DB::rollBack();
            dd($e->getMessage());
            Log::error('Poll Save Error', [
                'error' => $e->getMessage(),
                'user_id' => $userId ?? null,
                'poll_id' => $pollId ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save poll responsess.',
                'error_details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

     private function detectBrowser($userAgent)
    {
        // dd($userAgent);
          $browsers = [
                'Edge'      => 'Edge',
                'OPR'       => 'Opera',
                'Opera'     => 'Opera',
                'Chrome'    => 'Chrome',
                'CriOS'     => 'Chrome',   // iOS Chrome
                'Firefox'   => 'Firefox',
                'Safari'    => 'Safari',
                'SamsungBrowser' => 'Samsung Internet',
                'UCBrowser' => 'UC Browser',
                'Vivaldi'   => 'Vivaldi',
                'Brave'     => 'Brave', 
                'PostmanRuntime'=>'PostmanRuntime' // Brave includes Chrome but can be differentiated later
            ];

            foreach ($browsers as $key => $value) {
                if (stripos($userAgent, $key) !== false) {
                    return $value;
                }
            }

            return 'Unknown Browser:'.$userAgent;
    }

    // ---------------------------------------------------------
    // OS Detection
    // ---------------------------------------------------------
    private function detectOS($userAgent)
    {
         $osList = [
                'Windows NT 10.0' => 'Windows 10',
                'Windows NT 6.3'  => 'Windows 8.1',
                'Windows NT 6.2'  => 'Windows 8',
                'Windows NT 6.1'  => 'Windows 7',
                'Windows NT 6.0'  => 'Windows Vista',
                'Windows NT 5.1'  => 'Windows XP',

                'Android'         => 'Android',
                'Adr'             => 'Android',      // Some browsers shorten it

                'iPhone'          => 'iOS',
                'iPad'            => 'iOS',
                'iPod'            => 'iOS',

                'Mac OS X'        => 'MacOS',
                'Macintosh'       => 'MacOS',

                'CrOS'            => 'Chrome OS',

                'Ubuntu'          => 'Ubuntu Linux',
                'Linux'           => 'Linux',

                'Windows Phone'   => 'Windows Phone',
            ];

            foreach ($osList as $key => $value) {
                if (stripos($userAgent, $key) !== false) {
                    return $value;
                }
            }

            return 'Unknown OS'.$userAgent;
    }

    // ---------------------------------------------------------
    // Device Type Detection
    // ---------------------------------------------------------
    private function detectDevice($userAgent)
    {
        if (preg_match('/Mobile|Android|iPhone/i', $userAgent)) {
            return 'Mobile';
        }
        return 'Desktop';
    }


// =============================================//


}


