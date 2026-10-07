<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use App\Models\Quiz\QuizQuestion;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuestionBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Exception;

class QuizQuestionController extends Controller
{
    /**
     * Display all quiz questions.
     */
    public function index()
    {
        // Group quiz questions by quiz_id
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $quizData = QuizQuestion::with('quiz')
            ->select('quiz_id', DB::raw('COUNT(*) as total_questions'))
            ->groupBy('quiz_id')
            ->orderBy('quiz_id')
            ->get();
    
        return view('Quiz.quiz_questions.index', compact('quizData','menus'));
    }
    

    /**
     * Show the form for adding questions to a quiz.
     */
    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $quizzes = Quiz::orderBy('name')->get();

        // Avoid duplicate mappings
        $mappedQuestionBanks = QuizQuestion::pluck('question_bank_id')->toArray();
        $questionBanks = QuestionBank::whereNotIn('id', $mappedQuestionBanks)
            ->orderBy('id', 'asc')
            ->get();

        return view('Quiz.quiz_questions.form', compact('quizzes', 'questionBanks','menus'));
    }

    /**
     * Store newly created quiz questions (bulk insert via checkboxes).
     */
    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_bank_ids' => 'required|array|min:1',
            'question_bank_ids.*' => 'exists:question_banks,id',
        ], [
            'quiz_id.required' => 'Please select a quiz.',
            'question_bank_ids.required' => 'Please select at least one question.',
        ]);

        DB::beginTransaction();

        try {
            $quizId = $request->quiz_id;
            $questionIds = $request->question_bank_ids;

            // Get current max order number
            $maxOrder = QuizQuestion::where('quiz_id', $quizId)->max('orderno') ?? 0;

            foreach ($questionIds as $index => $questionId) {
                // Prevent duplicates
                $exists = QuizQuestion::where('quiz_id', $quizId)
                    ->where('question_bank_id', $questionId)
                    ->exists();

                if (!$exists) {
                    QuizQuestion::create([
                        'quiz_id' => $quizId,
                        'question_bank_id' => $questionId,
                        'orderno' => $maxOrder + ($index + 1),
                        'status' => 1,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.quiz_questions.preview', $quizId)
                ->with('success', 'Selected questions added successfully! You can now set order numbers.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('QuizQuestion store error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', ' Something went wrong while adding questions. Please try again.');
        }
    }

    /**
     * Show the form for editing a quiz question.
     */
    public function editByQuiz($quiz_id)
    {
        try {
            $admin = Auth::guard('admin')->user();
            $menus = AdminMenu::getMenuTreeForRole($admin->role);
            $quiz = Quiz::findOrFail($quiz_id);
            $quizzes = Quiz::orderBy('name')->get();
            $questionBanks = QuestionBank::orderBy('id', 'asc')->get();
    
            $alreadyMapped = QuizQuestion::where('quiz_id', $quiz_id)
                ->pluck('question_bank_id')
                ->toArray();
            
            // Create a dummy quiz question object with quiz_id to trigger edit mode
            $quizQuestion = new QuizQuestion();
            $quizQuestion->id = $quiz_id; // Use quiz_id as the identifier
            $quizQuestion->quiz_id = $quiz_id;
    
            return view('Quiz.quiz_questions.form', compact(
                'quiz',
                'quizzes',
                'questionBanks',
                'alreadyMapped',
                'menus',
                'quizQuestion'
            ));
        } catch (\Exception $e) {
            \Log::error('QuizQuestion editByQuiz error: ' . $e->getMessage());
            return redirect()->route('admin.quiz_questions.index')
                ->with('error', ' Unable to load quiz questions.');
        }
    }
    
    

    
    /**
     * Update a quiz question.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_bank_ids' => 'nullable|array',
            'question_bank_ids.*' => 'exists:question_banks,id',
        ]);

        DB::beginTransaction();

        try {
            // The $id here is actually the quiz_id from editByQuiz route
            $quizId = $request->quiz_id;

            // Get currently mapped questions for this quiz
            $currentMappings = QuizQuestion::where('quiz_id', $quizId)
                ->pluck('question_bank_id')
                ->toArray();

            // Get selected questions from form
            $selectedQuestions = $request->question_bank_ids ?? [];

            // Find questions to remove (unchecked questions)
            $questionsToRemove = array_diff($currentMappings, $selectedQuestions);
            
            // Find questions to add (newly checked questions)
            $questionsToAdd = array_diff($selectedQuestions, $currentMappings);

            // Remove unchecked questions
            if (!empty($questionsToRemove)) {
                QuizQuestion::where('quiz_id', $quizId)
                    ->whereIn('question_bank_id', $questionsToRemove)
                    ->delete();
            }

            // Add newly checked questions
            if (!empty($questionsToAdd)) {
                // Get current max order number
                $maxOrder = QuizQuestion::where('quiz_id', $quizId)->max('orderno') ?? 0;
                
                foreach ($questionsToAdd as $index => $questionId) {
                    QuizQuestion::create([
                        'quiz_id' => $quizId,
                        'question_bank_id' => $questionId,
                        'status' => 1,
                        'orderno' => $maxOrder + ($index + 1),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.quiz_questions.preview', $quizId)
                ->with('success', 'Quiz questions updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('QuizQuestion update error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unable to update quiz questions. Please try again.');
        }
    }

    /**
     * Preview all questions for a given quiz.
     */
    public function preview($quiz_id)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $quiz = Quiz::findOrFail($quiz_id);
        $quizQuestions = QuizQuestion::where('quiz_id', $quiz_id)
            ->with('questionBank')
            ->orderBy('orderno')
            ->get();

        return view('Quiz.quiz_questions.preview', compact('quiz', 'quizQuestions','menus'));
    }

    /**
     * Update question order numbers.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'orderno' => 'required|array',
        ]);

        try {
            foreach ($request->orderno as $id => $order) {
                if (is_numeric($order)) {
                    QuizQuestion::where('id', $id)->update(['orderno' => $order]);
                }
            }

            return redirect()
                ->route('admin.quiz_questions.index')
                ->with('success', 'Order numbers updated successfully!');
        } catch (Exception $e) {
            Log::error('QuizQuestion order update error: ' . $e->getMessage());

            return redirect()
                ->route('admin.quiz_questions.index')
                ->with('error', 'Failed to update order numbers.');
        }
    }


    /**
     * Delete a quiz question.
     */
    public function destroy($id)
    {
        try {
            $quizQuestion = QuizQuestion::findOrFail($id);
            $quizQuestion->delete();

            return redirect()
                ->route('admin.quiz_questions.index')
                ->with('success', ' Quiz Question deleted successfully!');
        } catch (Exception $e) {
            Log::error('QuizQuestion delete error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', ' Failed to delete quiz question.');
        }
    }
}
