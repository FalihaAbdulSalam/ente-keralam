<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Quiz\QuizController;
use App\Http\Controllers\Quiz\QuizSectionController;
use App\Http\Controllers\Quiz\QuestionBankController;
use App\Http\Controllers\Quiz\QuizQuestionController;
use App\Http\Controllers\Quiz\QuestionOptionController;
use App\Http\Controllers\Admin\PledgeController;
use App\Http\Controllers\Poll\PollController;
use App\Http\Controllers\Admin\MainMenuController;
use App\Http\Controllers\Admin\SubMenuController;
use App\Http\Controllers\Admin\SectorDetailController;
use App\Http\Controllers\Admin\CkeditorUploadController;
use App\Http\Controllers\Admin\ArticleTypeController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CounterDetailController;
use App\Http\Controllers\Admin\CreativethoughtsController;
use App\Http\Controllers\Admin\BannersController;
use App\Http\Controllers\Admin\EmailCampaignController;
use App\Http\Controllers\Contest\ContestController;

// Admin Authentication Routes (guest-only - logged in users redirected to dashboard)
Route::middleware('admin.guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
});

// Protected Admin Routes (with authentication middleware)
Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/news', [DashboardController::class, 'news'])->name('news');
    //Route::get('/quizzes', [DashboardController::class, 'quizzes'])->name('quizzes');
    Route::get('/tasks', [DashboardController::class, 'tasks'])->name('tasks');
    //Route::get('/polls', [DashboardController::class, 'polls'])->name('polls');


    //Main Menu Management
    Route::resource('mainmenu', MainMenuController::class);
    //Main Menu Management End
    //SubMenu Management
    Route::resource('submenu', SubMenuController::class);
    //SubMenu Management End
    //Article Type
    Route::resource('articletypes', ArticleTypeController::class);
    //Article Type
    //Article 
    Route::resource('articles', ArticleController::class);
    //Article End

    // Email Campaigns
    Route::get('/email-campaigns', [EmailCampaignController::class, 'index'])->name('email-campaigns.index');
    Route::post('/email-campaigns/message', [EmailCampaignController::class, 'updateMessage'])->name('email-campaigns.message');
    Route::post('/email-campaigns/import', [EmailCampaignController::class, 'import'])->name('email-campaigns.import');
    Route::post('/email-campaigns/send', [EmailCampaignController::class, 'send'])->name('email-campaigns.send');
    Route::post('/email-campaigns/test', [EmailCampaignController::class, 'test'])->name('email-campaigns.test');
    Route::get('/email-campaigns/progress', [EmailCampaignController::class, 'progress'])->name('email-campaigns.progress');
    Route::post('/email-campaigns/{campaign}/activate', [EmailCampaignController::class, 'activate'])->name('email-campaigns.activate');
    // Email Campaigns End


//Sector Details Management
    Route::resource('sector_details', SectorDetailController::class);
    Route::post('/ckeditor/upload2', [CkeditorUploadController::class, 'upload'])->name('ckeditor.upload2');
    Route::post('/ckeditor/upload', [CkeditorUploadController::class, 'upload'])->name('ckeditor.upload');

//Sector Details Management End

    //Quiz

    Route::resource('campaigns', CampaignController::class);
    Route::resource('eventtypes', EventTypeController::class);
    Route::resource('festivals', FestivalController::class);
    Route::resource('events', EventController::class);
    Route::resource('quizzes', QuizController::class);
    Route::resource('quiz_sections', QuizSectionController::class);

    Route::prefix('question_banks')->group(function () {
        Route::get('/', [QuestionBankController::class, 'index'])->name('question_banks.index');
        Route::get('/create', [QuestionBankController::class, 'create'])->name('question_banks.create');
        Route::post('/', [QuestionBankController::class, 'store'])->name('question_banks.store');
        Route::get('/{question}/edit', [QuestionBankController::class, 'edit'])->name('question_banks.edit');
        Route::put('/{question}', [QuestionBankController::class, 'update'])->name('question_banks.update');
        Route::delete('/{question}', [QuestionBankController::class, 'destroy'])->name('question_banks.destroy');
    });

    Route::resource('quiz_questions', QuizQuestionController::class);
    Route::get('quiz_questions/preview/{quiz_id}', [QuizQuestionController::class, 'preview'])->name('quiz_questions.preview');
    Route::post('quiz_questions/updateOrder', [QuizQuestionController::class, 'updateOrder'])->name('quiz_questions.updateOrder');
    Route::get('quiz_questions/edit-by-quiz/{quiz_id}', [QuizQuestionController::class, 'editByQuiz'])->name('quiz_questions.editByQuiz');

    //Quiz End

    // ✅ Poll Management
    Route::resource('polls', PollController::class);

    // ✅ Contest Management
    Route::get('contest/essay-writing', [ContestController::class, 'essay'])
    ->name('contest.essay-writing');
    Route::get('contest/addwinner', [ContestController::class, 'addwinner'])
        ->name('contest.addwinner');
    Route::get('contest/addwinner/{contest}', [ContestController::class, 'addwinner'])
        ->name('contest.addwinner');
    Route::post('contest/winner/add', [ContestController::class, 'storeWinner'])
        ->name('contest.store-winner');
    Route::get('contest/winners', [ContestController::class, 'winners'])
        ->name('contest.winners');
    Route::get('contest/winners/{contest}', [ContestController::class, 'winners'])
        ->name('contest.winners'); 
    Route::get('contest/poem-writing', [ContestController::class, 'index'])->name('contest.index'); 
    Route::get('contest/{contest}', [ContestController::class, 'show'])->name('contest.show');            
    //Route::resource('contest', ContestController::class);
    // Optional: Status update routes
    Route::put('contest/{contest}/approve', [ContestController::class, 'approve'])
        ->name('contest.approve');
    Route::put('contest/{contest}/reject', [ContestController::class, 'reject'])
        ->name('contest.reject');

    //pledge
    Route::get('/pledge', [PledgeController::class, 'index'])->name('pledge');
    Route::get('/pledge/create', [PledgeController::class, 'create'])->name('pledge.create');
    Route::post('/pledge/store', [PledgeController::class, 'store'])->name('pledge.store');
    Route::get('/pledge/edit/{id}', [PledgeController::class, 'edit'])->name('pledge.edit');
    Route::post('/pledge/update/{id}', [PledgeController::class, 'update'])->name('pledge.update');
    Route::get('/pledge/toggle/{id}', [PledgeController::class, 'toggle'])->name('pledge.toggle');
    //pledge ends


     //Sabitha 21 November 2025
    Route::resource('counter_details', CounterDetailController::class);
    
     //Sabitha 26 November 2025
    Route::resource('creativethoughts', CreativethoughtsController::class);
    
    Route::resource('banners', BannersController::class);
    Route::resource('footers', App\Http\Controllers\Admin\FooterController::class);

     //Sabitha 02 December 2025
    Route::resource('faqs', App\Http\Controllers\Admin\FAQController::class);

    // Settings routes - Super Admin only
    Route::middleware('admin.super_admin_only')->group(function () {
        Route::get('/menus', [DashboardController::class, 'manageMenus'])->name('menus');
    });





});
