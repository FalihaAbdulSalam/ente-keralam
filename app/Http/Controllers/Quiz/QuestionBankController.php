<?php

namespace App\Http\Controllers\Quiz;
use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use App\Models\Quiz\QuestionBank;
use App\Models\Quiz\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class QuestionBankController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
       $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $questions = QuestionBank::with('options')->get();
        return view('Quiz.question_banks.index', compact('questions','menus'));
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        return view('Quiz.question_banks.form',compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'questions' => 'required|string',
            'duration' => 'required|integer',
            'no_of_options' => 'required|integer',
            'difficulty_level' => 'required|string',
            'status' => 'required|boolean',
            'options.*.option_name' => 'required|string',
            'options.*.answer_flag' => 'required|boolean',
            'options.*.order_no' => 'required|integer',
        ]);

        $question = QuestionBank::create($request->only(['questions', 'duration', 'no_of_options', 'difficulty_level', 'status']));

        foreach ($request->options as $option) {
            $question->options()->create($option);
        }

        return redirect()->route('admin.question_banks.index')->with('success', 'Question added successfully.');
    }

    public function edit(QuestionBank $question)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $options = $question->options; 
        return view('Quiz.question_banks.form', compact('question', 'options','menus'));
    }

    public function update(Request $request, QuestionBank $question)
    {
        $request->validate([
            'questions' => 'required|string',
            'duration' => 'required|integer',
            'no_of_options' => 'required|integer',
            'difficulty_level' => 'required|string',
            'status' => 'required|boolean',
            'options.*.option_name' => 'required|string',
            'options.*.answer_flag' => 'required|boolean',
            'options.*.order_no' => 'required|integer',
        ]);

        $question->update($request->only(['questions', 'duration', 'no_of_options', 'difficulty_level', 'status']));
        $question->options()->delete();

        foreach ($request->options as $option) {
            $question->options()->create($option);
        }

        return redirect()->route('admin.question_banks.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(QuestionBank $question)
    {
        $question->delete();
        return redirect()->route('admin.question_banks.index')->with('success', 'Question deleted successfully.');
    }
}
