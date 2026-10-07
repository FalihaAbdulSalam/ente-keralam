<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PollController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\ServiceListController;
use App\Http\Controllers\Api\QuizApiController;
use App\Http\Controllers\Api\PollApiController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\Api\MenuApiController;
use App\Http\Controllers\Api\ContentApiController;
use App\Models\SectorDetail;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\SectorwiseController;
use App\Http\Controllers\Api\ContestController;
use App\Http\Controllers\Api\PhotoUploadApiController;
use App\Http\Controllers\Api\ReelUploadApiController;
use App\Http\Controllers\Api\QuizAttendaceApiController;
use App\Http\Controllers\Api\GlobalSearchController;
use App\Http\Controllers\Api\DocumentUploadApiController;
use App\Http\Controllers\Api\PollResponseApiController;
// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/send-otp', [AuthController::class, 'sendOTP']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOTP']);
Route::post('/register/send-otp', [AuthController::class, 'registrationSendOTP']);
Route::post('/register/verify-otp', [AuthController::class, 'registrationVerifyOTP']);
Route::post('/forgot-password/send-otp', [AuthController::class, 'forgotPasswordSendOTP']);
Route::post('/forgot-password/verify-otp', [AuthController::class, 'forgotPasswordVerifyOTP']);
Route::post('/forgot-password/reset', [AuthController::class, 'resetPasswordWithOTP']);

// Districts (public)
Route::get('/districts', function () {
    return response()->json([
        'success' => true,
        'districts' => \App\Models\District::active()->orderBy('id')->get(['id', 'name', 'local'])
    ]);
});

Route::get('/pledge/{id}', [ServiceListController::class, 'getPledge']);
Route::get('/all_pledge', [ServiceListController::class, 'getPledgeactive']);

Route::get('/contest/{id}', [ContestController::class, 'getContestById']);
Route::get('/contests', [ContestController::class, 'getContests']);
Route::get('/contest-slug/{slug}', [ContestController::class, 'getContestBySlug']);
// Photo Upload API Routes
Route::prefix('photo-upload')->group(function () {
    // Submit or update photo
    Route::post('/', [PhotoUploadApiController::class, 'store']);

    // Get existing submission
    Route::get('/{contestId}/{applicantId}', [PhotoUploadApiController::class, 'show']);
});
// Video upload reel1
Route::get('/reel', [ReelUploadApiController::class, 'show']);
Route::post('/reel', [ReelUploadApiController::class, 'store']);
Route::put('/reel', [ReelUploadApiController::class, 'update']);
// Multipart (streaming) reel upload/update to avoid base64 memory blowups
Route::post('/reel-upload', [ReelUploadApiController::class, 'storeMultipart']);
Route::post('/reel-update', [ReelUploadApiController::class, 'updateMultipart']);
//end reel1

//*******PDF Document Upload Contest*****/
// POST /api/contest/upload/pdf - Create new
Route::post('contest/upload/pdf', [DocumentUploadApiController::class, 'addContest']);

// GET /api/contest/upload/pdf/- Show single
Route::get('contest/upload/pdf', [DocumentUploadApiController::class, 'showContest']);

// POST /api/contest/upload/pdf/update - Update (full update)
Route::post('contest/upload/pdf/update', [DocumentUploadApiController::class, 'updateContest']);
//*******End PDF Document Upload Contest*****/


// Public content routes
Route::get('/polls', [PollController::class, 'index']);
Route::get('/polls/{id}', [PollController::class, 'show']);

Route::get('/quizzes', [QuizController::class, 'index']);
Route::get('/quizzes/{id}', [QuizController::class, 'show']);
Route::get('/daily-quiz', [QuizController::class, 'dailyQuiz']);


//  Articles
Route::get('/articles', [ContentApiController::class, 'articles']);
Route::get('/articles/{id}', [ContentApiController::class, 'articleShow']);

// Article Types
Route::get('/article-types', [ContentApiController::class, 'articleTypes']);
Route::get('/article-types/{id}', [ContentApiController::class, 'articleTypeShow']);

// Sectors
Route::get('/sectors', [ContentApiController::class, 'sectors']);
Route::get('/sectors/{id}', [ContentApiController::class, 'sectorShow']);


// Route::get('/{sectors:link}', [ContentApiController::class, 'sectorShow']);

//sabitha 21 november 2025

$allowedLinks = [];
if (Schema::hasTable('sector_details')) {
    $allowedLinks = SectorDetail::pluck('link')->filter()->toArray();
}

if (!empty($allowedLinks)) {
    Route::get('/{sectors:link}', [SectorwiseController::class, 'sectorShow'])
        ->whereIn('sectors', $allowedLinks)
        ->name('sector.page');
}

//sabitha november 26   2025

// creativethought
Route::get('/creativethought', [ContentApiController::class, 'creativethought']);

// banner
Route::get('/banners', [ContentApiController::class, 'banners']);

// footer
Route::get('/footers', [ContentApiController::class, 'footers']);

// faq
Route::get('/faq', [ContentApiController::class, 'faq']);

//GlobalSearchController
Route::get('/searchkeyword1', [GlobalSearchController::class, 'search']);

//GlobalSearchController sectorwise
Route::get('/sectorwise', [GlobalSearchController::class, 'sectorwise']);

// sabitha

Route::get('/tasks', [TaskController::class, 'index']);
Route::get('/tasks/{id}', [TaskController::class, 'show']);

Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{id}', [NewsController::class, 'show']);

Route::get('/testimonials', [TestimonialController::class, 'index']);
Route::post('/quizresponse', [QuizAttendaceApiController::class, 'quizresponseStore']);
Route::post('/pollresponse', [PollResponseApiController::class, 'pollresponseStore']);




// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    Route::post('/user/mobile/send-otp', [AuthController::class, 'sendUpdateMobileOTP']);
    Route::post('/user/mobile/update', [AuthController::class, 'updateMobileWithOTP']);
    Route::post('/user/email/send-otp', [AuthController::class, 'sendUpdateEmailOTP']);
    Route::post('/user/email/update', [AuthController::class, 'updateEmailWithOTP']);
    
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/activities', [DashboardController::class, 'activities']);
    Route::get('/dashboard/points', [DashboardController::class, 'points']);
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::get('/profile/completion', [ProfileController::class, 'completion']);
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar']);
    
    // Quiz interactions
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submitAnswer']);
    
    // Activity tracking
    Route::post('/activities/quiz/{id}', [ActivityController::class, 'submitQuiz']);
    Route::post('/activities/pledge/{id}', [ActivityController::class, 'submitPledge']);
    Route::post('/activities/poll/{id}', [ActivityController::class, 'submitPoll']);
    Route::post('/activities/task/{id}', [ActivityController::class, 'submitTask']);
    Route::post('/activities/competition/{id}', [ActivityController::class, 'submitCompetition']);
    Route::get('/activities/check/{type}/{id}', [ActivityController::class, 'checkCompletion']);
    Route::get('/activities/summary', [ActivityController::class, 'summary']);
    
    // Profile management
    Route::post('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::post('/profile/deactivate', [ProfileController::class, 'deactivateAccount']);
    
    // Settings and Skills/Interests
    Route::get('/profile/settings', [ProfileController::class, 'getSettings']);
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings']);
    Route::get('/profile/skills-interests', [ProfileController::class, 'getSkillsInterests']);
    Route::put('/profile/skills-interests', [ProfileController::class, 'updateSkillsInterests']);
    
    // Referral system
    Route::get('/referral', [ReferralController::class, 'show']);
    
    // Admin routes (you can add role middleware later)
    Route::apiResource('admin/polls', PollController::class)->except(['index', 'show']);
    Route::apiResource('admin/quizzes', QuizController::class)->except(['index', 'show']);
    Route::apiResource('admin/tasks', TaskController::class)->except(['index', 'show']);
    Route::apiResource('admin/news', NewsController::class)->except(['index', 'show']);
    Route::apiResource('admin/testimonials', TestimonialController::class)->except(['index']);
});


Route::get('/quizdata/{id}', [QuizApiController::class, 'Quizshow']);
Route::get('/polldata/{id}', [PollApiController::class, 'Pollshow']);

Route::get('/allquizdata', [QuizApiController::class, 'allQuizzes']);
Route::get('/allpolldata', [PollApiController::class, 'allPolls']);
Route::get('/menus', [MenuApiController::class, 'menuTree']);