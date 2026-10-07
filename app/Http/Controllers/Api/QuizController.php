<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Quiz::with('questions');

        if ($request->has('is_daily_quiz')) {
            $query->where('is_daily_quiz', $request->boolean('is_daily_quiz'));
        }

        $quizzes = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $quizzes
        ]);
    }

    /**
     * Get today's daily quiz
     */
    public function dailyQuiz()
    {
        $quiz = Quiz::with('questions')
            ->where('is_daily_quiz', true)
            ->where('quiz_date', today())
            ->where('status', 'active')
            ->first();

        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => 'No daily quiz available for today'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $quiz
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'quiz_date' => 'required|date',
            'is_daily_quiz' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.option_a' => 'required|string',
            'questions.*.option_b' => 'required|string',
            'questions.*.option_c' => 'required|string',
            'questions.*.option_d' => 'required|string',
            'questions.*.correct_answer' => 'required|in:A,B,C,D',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $quiz = Quiz::create($request->only(['title', 'description', 'quiz_date', 'is_daily_quiz']));

        foreach ($request->questions as $questionData) {
            $quiz->questions()->create($questionData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Quiz created successfully',
            'data' => $quiz->load('questions')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quiz = Quiz::with('questions')->find($id);

        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => 'Quiz not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $quiz
        ]);
    }

    /**
     * Submit quiz answer
     */
    public function submitAnswer(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:quiz_questions,id',
            'answer' => 'required|in:A,B,C,D',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $question = QuizQuestion::find($request->question_id);
        $isCorrect = $question->correct_answer === $request->answer;

        return response()->json([
            'success' => true,
            'is_correct' => $isCorrect,
            'correct_answer' => $question->correct_answer,
            'message' => $isCorrect ? 'Correct answer!' : 'Wrong answer!'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $quiz = Quiz::find($id);

        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => 'Quiz not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'quiz_date' => 'date',
            'is_daily_quiz' => 'boolean',
            'status' => 'in:active,inactive,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $quiz->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Quiz updated successfully',
            'data' => $quiz->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $quiz = Quiz::find($id);

        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => 'Quiz not found'
            ], 404);
        }

        $quiz->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quiz deleted successfully'
        ]);
    }
}
