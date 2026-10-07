<?php

namespace App\Http\Controllers;

use App\Models\Quiz\QuizSection;
use App\Models\Quiz\Quiz;
use Illuminate\Http\Request;

class QuizSectionController extends Controller
{
    public function index()
    {
        $quizSections = QuizSection::with('quiz')->paginate(10);
        return view('quiz_sections.index', compact('quizSections'));
    }

    public function create()
    {
        $quizzes = Quiz::all();
        return view('quiz_sections.form', compact('quizzes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'sectiontype_id' => 'required|in:Mock,Live',
            'quiz_date' => 'required|date',
            'quiz_time' => 'required',
            'duration' => 'required|integer|min:0',
            'familarization_time' => 'required|integer|min:0',
            'no_of_questions' => 'required|integer|min:1',
            'status' => 'required|boolean',
        ]);

        QuizSection::create($request->all());

        return redirect()->route('quiz_sections.index')->with('success', 'Quiz Section created successfully.');
    }

    public function edit(QuizSection $quizSection)
    {
        $quizzes = Quiz::all();
        return view('quiz_sections.form', compact('quizSection', 'quizzes'));
    }

    public function update(Request $request, QuizSection $quizSection)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'sectiontype_id' => 'required|in:Mock,Live',
            'quiz_date' => 'required|date',
            'quiz_time' => 'required',
            'duration' => 'required|integer|min:0',
            'familarization_time' => 'required|integer|min:0',
            'no_of_questions' => 'required|integer|min:1',
            'status' => 'required|boolean',
        ]);

        $quizSection->update($request->all());

        return redirect()->route('quiz_sections.index')->with('success', 'Quiz Section updated successfully.');
    }

    public function destroy(QuizSection $quizSection)
    {
        $quizSection->delete();

        return redirect()->route('quiz_sections.index')->with('success', 'Quiz Section deleted successfully.');
    }
}