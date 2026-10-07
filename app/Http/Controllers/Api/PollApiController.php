<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Poll\Poll;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class PollApiController extends Controller
{

    private function fileUrl($path, $type = null)
{
    if (!$path) {
        return null;
    }

    // 🔹 Define base folder mapping
    $basePaths = [
        'poster' => 'uploads/polls/posters/',
        'banner' => 'uploads/polls/banners/',
        'attachment' => 'uploads/polls/attachments/',
    ];

    // 🔹 If the type is known, prefix the correct folder
    if ($type && !str_starts_with($path, 'uploads/')) {
        $path = $basePaths[$type] . ltrim($path, '/');
    }

    // 🔹 Return absolute public URL
    return asset($path);
}

// =============================================//


public function Pollshow($id): JsonResponse
    {
        // ✅ Load poll with related questions & options
        $poll = Poll::with(['questions.options'])->find($id);

        if (!$poll) {
            return response()->json([
                'status' => false,
                'message' => 'Poll not found'
            ], 404);
        }

        // ✅ Map all questions & options safely
        $questions = $poll->questions
            ->filter(fn($q) => $q) // skip nulls
            ->map(function ($question) {
                $options = $question->options ?? collect();

                return [
                    'question_id' => $question->id,
                    'question_text' => $question->question_text ?? '',
                    'options' => $options->map(fn($opt) => [
                        'option_id' => $opt->id,
                        'option_name' => $opt->option_text ?? '',
                    ])->values(),
                ];
            })
            ->values();

        // ✅ Return consistent structured JSON
        return response()->json([
            'status' => true,
            'poll' => [
                'poll_id' => $poll->id,
                'festival_id' => $poll->festival_id ?? null,
                'event_id' => $poll->event_id ?? null,
                'name' => $poll->name ?? '',
                'topic' => $poll->topic ?? '',
                'poster' => $this->fileUrl($poll->poster),
                'banner' => $this->fileUrl($poll->banner),
                'start_date' => $poll->start_date,
                'end_date' => $poll->end_date,
                'start_time' => $poll->start_time,
                'end_time' => $poll->end_time,
                'about' => $poll->about ?? '',
                'terms_condition' => $poll->terms_condition ?? '',
                'attachment' => $this->fileUrl($poll->attachment),
                'points' => $poll->points ?? 0,
                'status' => $poll->status ?? 1,
                'total_questions' => $questions->count(),
                'questions' => $questions,
            ]
        ]);
    }



public function allPolls()
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        // ✅ Fetch all active polls for today
        $polls = Poll::with(['questions.options'])
            ->where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderBy('start_date', 'desc')
            ->get();

            // dd($polls);

        if ($polls->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No active polls found for today.',
                'data' => [],
            ]);
        }

        // ✅ Format all polls with question/option structure
        $data = $polls->map(function ($poll) {
            $questions = $poll->questions
                ->filter(fn($q) => $q)
                ->map(function ($q) {
                    $options = $q->options ?? collect();

                    return [
                        'question_id' => $q->id,
                        'question_text' => $q->question_text ?? '',
                        'options' => $options->map(fn($opt) => [
                            'option_id' => $opt->id,
                            'option_name' => $opt->option_text ?? '',
                        ])->values(),
                    ];
                })
                ->values();

            return [
                'poll_id' => $poll->id,
                'festival_id' => $poll->festival_id ?? null,
                'event_id' => $poll->event_id ?? null,
                'name' => $poll->name ?? '',
                'topic' => $poll->topic ?? '',
                'poster' => $this->fileUrl($poll->poster),
                'banner' => $this->fileUrl($poll->banner),
                'start_date' => $poll->start_date,
                'end_date' => $poll->end_date,
                'start_time' => $poll->start_time,
                'end_time' => $poll->end_time,
                'about' => $poll->about ?? '',
                'terms_condition' => $poll->terms_condition ?? '',
                'attachment' => $this->fileUrl($poll->attachment),
                'points' => $poll->points ?? 0,
                'status' => $poll->status ?? 1,
                'total_questions' => $questions->count(),
                'questions' => $questions,
            ];
        });

        // ✅ Return full structured JSON response
        return response()->json([
            'status' => true,
            'date' => $today,
            'count' => $data->count(),
            'data' => $data,
        ]);
    
    }
}


