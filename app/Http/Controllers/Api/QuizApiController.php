<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityCategory;
use App\Models\Quiz\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class QuizApiController extends Controller
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
    
    public function Quizshow($id): JsonResponse
    {
        $participationPoints = (int) (ActivityCategory::where('slug', 'quiz')
            ->value('points_per_completion') ?? 0);
        $perCorrectPoints = $participationPoints;

        // Load quiz with related question banks and options
        $quiz = Quiz::with(['quizQuestions.questionBank.options'])->find($id);

        if (!$quiz) {
            return response()->json(['status' => false, 'message' => 'Quiz not found'], 404);
        }

        // Build question data
        $questions = $quiz->quizQuestions
            ->map(function ($quizQuestion) use ($quiz) {
                $question = $quizQuestion->questionBank;

                if (!$question) {
                    return null;
                }

                $correctOption = $question->options->firstWhere('answer_flag', 1);

                return [
                    'question_id' => $question->id,
                    'quiz_id' => $quiz->id,
                    'question' => $question->questions,
                    'orderno' => $quizQuestion->orderno ?? null,
                    'difficulty_level' => $question->difficulty_level ?? null,
                    'duration' => $question->duration ?? null,
                    'options' => $question->options->map(fn($opt) => [
                        'option_id' => $opt->id,
                        'option_text' => $opt->option_name,
                    ])->values(),
                    'correct_answer' => $correctOption
                        ? [
                            'answer_id' => $correctOption->id,
                            'answer_text' => $correctOption->option_name,
                        ]
                        : null,
                ];
            })
            ->filter()
            ->values();

        // Return formatted JSON
        return response()->json([
            'status' => true,
            'quiz' => [
                'quiz_id' => $quiz->id,
                'festival_id' => $quiz->festival_id,
                'event_id' => $quiz->event_id,
                'section_type' => $quiz->section_type,
                'name' => $quiz->name,
                'topic' => $quiz->topic,
                'poster' => $this->fileUrl($quiz->poster),
                'banner' => $this->fileUrl($quiz->banner),
                'about' => $quiz->about,
                'terms_condition' => $quiz->terms_condition,
                'attachment' => $this->fileUrl($quiz->attachment),
                'points' => $quiz->points ?? null,
                'participation_points' => $participationPoints,
                'per_correct_points' => $perCorrectPoints,
                'status' => $quiz->status,
                'start_date' => $quiz->start_date,
                'end_date' => $quiz->end_date,
                'start_time' => $quiz->start_time,
                'end_time' => $quiz->end_time,
                'result' => $quiz->result ?? null,
                'total_questions' => $questions->count(),
                'current_question_number' => 1,
                'timer' => $quiz->quizQuestions->first()?->questionBank?->duration ?? '05:00',
                'questions' => $questions,
            ],
        ]);
    }
    //  ============================================//
    public function allQuizzes(): JsonResponse
    {
        $participationPoints = (int) (ActivityCategory::where('slug', 'quiz')
            ->value('points_per_completion') ?? 0);
        $perCorrectPoints = $participationPoints;
       
        $now = Carbon::now();
        $today = $now->toDateString();

        // ✅ Get quizzes active for current date
        $quizzes = Quiz::with([
            'quizQuestions' => function ($query) {
                $query->orderBy('orderno', 'asc');
            },
            'quizQuestions.questionBank.options'
        ])
            ->where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            // ->whereTime('start_time', '<=', $now->format('H:i:s'))
            // ->whereTime('end_time', '>=', $now->format('H:i:s'))
            ->orderBy('start_date', 'desc')
            ->get();

        if ($quizzes->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No active quizzes found for today.',
                'data' => [],
            ], 200);
        }

        // ✅ Build full quiz data
        $quizData = $quizzes->map(function ($quiz) use ($participationPoints, $perCorrectPoints) {
            $questions = $quiz->quizQuestions
                ->filter(fn($qq) => $qq->questionBank)
                ->map(function ($quizQuestion) use ($quiz) {
                    $question = $quizQuestion->questionBank;
                    $options = $question->options ?? collect();
                    $correctOption = $options->firstWhere('answer_flag', 1);

                    return [
                        'question_id' => $question->id,
                        'quiz_id' => $quiz->id,
                        'orderno' => $quizQuestion->orderno ?? null,
                        'question_text' => $question->questions ?? '',
                        'difficulty_level' => $question->difficulty_level ?? null,
                        'duration' => $question->duration ?? '05:00',
                        'options' => $options->map(fn($opt) => [
                            'option_id' => $opt->id,
                            'option_text' => $opt->option_name,
                            'is_correct' => $opt->answer_flag ?? 0,
                        ])->values(),
                        'correct_answer' => $correctOption ? [
                            'answer_id' => $correctOption->id,
                            'answer_text' => $correctOption->option_name,
                        ] : null,
                    ];
                })
                ->values();

            return [
                'quiz_id' => $quiz->id,
                'festival_id' => $quiz->festival_id,
                'event_id' => $quiz->event_id,
                'section_type' => $quiz->section_type,
                'name' => $quiz->name,
                'topic' => $quiz->topic,
                'poster' => $this->fileUrl($quiz->poster),
                'banner' => $this->fileUrl($quiz->banner),
                'about' => $quiz->about,
                'terms_condition' => $quiz->terms_condition,
                'attachment' => $this->fileUrl($quiz->attachment),
                'points' => $quiz->points ?? null,
                'participation_points' => $participationPoints,
                'per_correct_points' => $perCorrectPoints,
                'status' => $quiz->status,
                'start_date' => $quiz->start_date,
                'end_date' => $quiz->end_date,
                'start_time' => $quiz->start_time,
                'end_time' => $quiz->end_time,
                'result' => $quiz->result ?? null,
                'total_questions' => $questions->count(),
                'timer' => $questions->first()['duration'] ?? '05:00',
                'questions' => $questions,
            ];
        });

        return response()->json([
            'status' => true,
            'count' => $quizData->count(),
            'date' => $today,
            'data' => $quizData,
        ], 200);
    
    }


// =============================================//


}
