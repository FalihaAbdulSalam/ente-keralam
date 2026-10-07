<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz\Quiz;
use App\Models\ActivityCategory;
use App\Models\UserActivity;
use App\Models\UserPoint;

use App\Models\Quiz\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Models\Quiz\ResponsesFromApiQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class QuizAttendaceApiController extends Controller
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
    
    public function quizresponseStore(Request $request){
           // Validation
                $validated = $request->validate([
                    'user.user_id' => 'required|integer',
                    'quiz.quiz_id' => 'required|integer',
                    'answers'      => 'required|array',
                    'answers.*.question_id' => 'required|integer',
                    'answers.*.selected_option_id' => 'nullable|integer',
                ]);

                $userId = $request->user['user_id'];
                $quizId = $request->quiz['quiz_id'];
                
                // Check if this is a final submission or just progress save
                // Final submission comes from ResultPage with is_final_submission = true
                $isFinalSubmission = $request->input('is_final_submission', false);

                DB::beginTransaction();  // ⭐ START TRANSACTION

                try {

                    // Auto detect device/browser/OS/IP
                    $ip          = $request->ip();
                    $userAgent   = $request->header('User-Agent');

                    $browser     = $this->detectBrowser($userAgent);
                    $os          = $this->detectOS($userAgent);
                    $deviceType  = $this->detectDevice($userAgent);

                    $totalscore = 0;

                    /*
                    |=====================================
                    | SAVE EACH QUIZ ANSWER
                    |=====================================
                    */
                    foreach ($request->answers as $answer) {

                        $score = $answer['selected_option_id'] ? 1 : 0;

                        // If ANY insert fails → exception → rollback
                        ResponsesFromApiQuiz::create([
                            'user_id'             => $userId,
                            'quiz_id'             => $quizId,
                            'question_id'         => $answer['question_id'],
                            'selected_option_id'  => $answer['selected_option_id'],
                            'score_per_question'  => $score,
                            'attending_date'      => $answer['attending_date'],
                            'saved_time'          => Carbon::now(),
                            'ip_address'          => $ip,
                            'device_type'         => $deviceType,
                            'browser'             => $browser,
                            'os'                  => $os,
                            'selected_at_micro'   => Carbon::now()->format('Y-m-d H:i:s.u'),
                        ]);

                        // Check if the SELECTED option is the correct answer
                        $isCorrect = $answer['selected_option_id'] 
                            ? QuestionOption::where('id', $answer['selected_option_id'])
                                ->where('answer_flag', 1)
                                ->exists()
                            : false;

                        $totalscore += $isCorrect ? 1 : 0;
                    }

                    // Fetch quiz from database
                    $quiz = Quiz::find($quizId);
                    $totalQuestions = count($request->answers);
                    $category = ActivityCategory::where('slug', 'quiz')->first();
                    $participationPoints = (int) ($category->points_per_completion ?? 0);
                    $pointsPerCorrect = $participationPoints;

                    if ($totalQuestions > 0) {
                        $pointsEarned = $participationPoints + ($totalscore * $pointsPerCorrect);
                        $quizMaxPoints = $participationPoints + ($totalQuestions * $pointsPerCorrect);
                    } else {
                        $pointsEarned = 0;
                        $quizMaxPoints = 0;
                    }
                    
                    $totalPoints = 0;
                    $isNewActivity = false;

                    /*
                    |=====================================
                    | USER ACTIVITY INSERT - ONLY ON FINAL SUBMISSION
                    | Progress saves (from QuizPage) should NOT create activity
                    |=====================================
                    */
                    if ($isFinalSubmission) {
                        $existingActivity = UserActivity::where('user_id', $userId)
                            ->where('activity_type', 'quiz')
                            ->where('activity_id', $quizId)
                            ->where('status', 'completed')
                            ->first();

                        if (!$existingActivity) {
                            $isNewActivity = true;
                            UserActivity::create([
                                'activity_category_id' => 1,
                                'activity_name'        => $quiz->name ?? 'Quiz',
                                'activity_type'        => 'quiz',
                                'activity_id'          => $quizId,
                                'points_earned'        => $pointsEarned,
                                'score'                => $totalscore,
                                'max_score'            => $totalQuestions,
                                'status'               => 'completed',
                                'completed_at'         => now(),
                                'user_id'              => $userId
                            ]);

                            /*
                            |=====================================
                            | USER TOTAL POINTS UPDATE
                            |=====================================
                            */
                            $userPoints = UserPoint::where('user_id', $userId)->first();

                            if (!$userPoints) {
                                $userPoints = UserPoint::create([
                                    'user_id' => $userId,
                                    'total_points' => 0,
                                ]);
                            }

                            // Add earned points to total
                            $userPoints->addPoints($pointsEarned);
                            $totalPoints = $userPoints->total_points;
                        } else {
                            // Get existing total points
                            $userPoints = UserPoint::where('user_id', $userId)->first();
                            $totalPoints = $userPoints->total_points ?? 0;
                            $pointsEarned = $existingActivity->points_earned;
                            
                            Log::info("Quiz activity already exists", [
                                'user_id' => $userId,
                                'quiz_id' => $quizId
                            ]);
                        }
                    } else {
                        // Progress save - just return calculated values without creating activity
                        $userPoints = UserPoint::where('user_id', $userId)->first();
                        $totalPoints = $userPoints->total_points ?? 0;
                    }

                    DB::commit();  // ⭐ COMMIT EVERYTHING

                    return response()->json([
                        'success' => true,
                        'message' => $isFinalSubmission 
                            ? ($isNewActivity ? 'Quiz completed successfully!' : 'Quiz already completed.')
                            : 'Quiz progress saved.',
                        'data' => [
                            'points_earned' => $pointsEarned,
                            'total_points' => $totalPoints,
                            'score' => $totalscore,
                            'max_score' => $totalQuestions,
                            'quiz_max_points' => $quizMaxPoints,
                            'is_new_activity' => $isNewActivity,
                            'is_final_submission' => $isFinalSubmission,
                        ]
                    ]);

                } catch (\Exception $e) {

                    DB::rollBack(); // ⭐ ROLLBACK EVERYTHING

                    // Log detailed error for admin debugging
                    Log::error("Quiz Save Error: " . $e->getMessage());

                    // Return readable message to user
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to save your quiz responses. Please try again.',
                        'error_details' => env('APP_DEBUG') ? $e->getMessage() : null
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
