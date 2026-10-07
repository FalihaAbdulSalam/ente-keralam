<?php

namespace App\Http\Controllers;

use App\Models\Quiz\QuestionOption;
use App\Models\Quiz\QuizQuestion;
use Illuminate\Http\Request;

class QuestionOptionController extends Controller
{
    public function index()
    {
        $questionOptions = QuestionOption::with('question')->get();
        return view('question_options.index', compact('questionOptions'));
    }

    public function create()
    {
        $questions = QuizQuestion::all();
        return view('question_options.create', compact('questions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:quiz_questions,id',
            'option_name' => 'required|string|max:255',
            'answer_flag' => 'required|boolean',
            'order_no' => 'nullable|integer',
            'status' => 'required|in:1,0,2',
        ]);

        QuestionOption::create($request->all());

        return redirect()->route('question_options.index')->with('success', 'Question Option created successfully!');
    }

    public function edit($id)
    {
        $questionOption = QuestionOption::findOrFail($id);
        $questions = QuizQuestion::all();
        return view('question_options.edit', compact('questionOption', 'questions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question_id' => 'required|exists:quiz_questions,id',
            'option_name' => 'required|string|max:255',
            'answer_flag' => 'required|boolean',
            'order_no' => 'nullable|integer',
            'status' => 'required|in:1,0,2',
        ]);

        $questionOption = QuestionOption::findOrFail($id);
        $questionOption->update($request->all());

        return redirect()->route('question_options.index')->with('success', 'Question Option updated successfully!');
    }

    public function destroy($id)
    {
        $questionOption = QuestionOption::findOrFail($id);
        $questionOption->delete();

        return redirect()->route('question_options.index')->with('success', 'Question Option deleted successfully!');
    }
}