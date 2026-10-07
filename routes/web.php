<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
//QUIZ
use App\Http\Controllers\Quiz\CampaignController;
use App\Http\Controllers\Quiz\EventTypeController;
use App\Http\Controllers\Quiz\FestivalController;
use App\Http\Controllers\Quiz\EventController;
use App\Http\Controllers\Quiz\QuizController;
use App\Http\Controllers\Quiz\QuizSectionController;
use App\Http\Controllers\Quiz\QuestionBankController;
use App\Http\Controllers\Quiz\QuizQuestionController;
use App\Http\Controllers\Quiz\QuestionOptionController;
use App\Http\Controllers\SectorwiseController;

use App\Models\SectorDetail;

// Social authentication routes (need session middleware)
Route::get('/api/auth/{provider}', [AuthController::class, 'socialRedirect']);
Route::get('/api/auth/{provider}/callback', [AuthController::class, 'socialCallback']);

Route::get('/cmmegaquiz', function () {
    return view('cmmegaquiz.index');
});

// Route::get('/{sectors:link}', [SectorwiseController::class, 'sectorShow'])
//     ->whereIn('sectors', ['economy', 'health', 'turism'])
//     ->name('sector.page');

// Include admin routes
require __DIR__ . '/admin.php';




// =============================UIZ MODULE===========================





// Serve React app for all routes except API
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|admin).*$');