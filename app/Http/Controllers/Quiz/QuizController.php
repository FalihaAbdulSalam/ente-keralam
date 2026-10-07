<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\Festival;
use App\Models\Quiz\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $quizzes = Quiz::with(['festival', 'event'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('Quiz.quizzes.index', compact('quizzes', 'menus'));
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $festivals = Festival::orderBy('name')->get();
        $events = Event::orderBy('name')->get();
        return view('Quiz.quizzes.form', compact('festivals', 'events', 'menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'festival_id' => 'required|exists:festivals,id',
            'event_id' => 'required|exists:events,id',
            'section_type' => 'required|in:Mock,Live',
            'name' => 'required|string|max:255',
            'topic' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'about' => 'nullable|string',
            'result' => 'nullable|string',
            'terms_condition' => 'nullable|string',
            'status' => 'required|boolean',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'attachment' => 'nullable|mimes:pdf,doc,docx|max:5120',
            'points' => 'nullable|integer|min:0',
        ]);

        foreach (['poster', 'banner', 'attachment'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $folder = "uploads/quizzes/{$field}s";
                $destination = public_path($folder);
                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0775, true);
                }
                $file->move($destination, $filename);
                $validated[$field] = "$folder/$filename";
            }
        }

        Quiz::create($validated);

        return redirect()
            ->route('admin.quizzes.index')
            ->with('success', 'Quiz created successfully.');
    }

    public function edit(Quiz $quiz)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $festivals = Festival::orderBy('name')->get();
        $events = Event::orderBy('name')->get();
        return view('Quiz.quizzes.form', compact('quiz', 'festivals', 'events', 'menus'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'festival_id' => 'required|exists:festivals,id',
            'event_id' => 'required|exists:events,id',
            'section_type' => 'required|in:Mock,Live',
            'name' => 'required|string|max:255',
            'topic' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'about' => 'nullable|string',
            'result' => 'nullable|string',
            'terms_condition' => 'nullable|string',
            'status' => 'required|boolean',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'attachment' => 'nullable|mimes:pdf,doc,docx|max:5120',
            'points' => 'nullable|integer|min:0',
        ]);

        foreach (['poster', 'banner', 'attachment'] as $field) {
            if ($request->hasFile($field)) {
                if ($quiz->$field && File::exists(public_path($quiz->$field))) {
                    File::delete(public_path($quiz->$field));
                }
                $file = $request->file($field);
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $folder = "uploads/quizzes/{$field}s";
                $destination = public_path($folder);
                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0775, true);
                }
                $file->move($destination, $filename);
                $validated[$field] = "$folder/$filename";
            }
        }

        $quiz->update($validated);

        return redirect()
            ->route('admin.quizzes.index')
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        foreach (['poster', 'banner', 'attachment'] as $fileField) {
            if ($quiz->$fileField && File::exists(public_path($quiz->$fileField))) {
                File::delete(public_path($quiz->$fileField));
            }
        }

        $quiz->delete();

        return redirect()
            ->route('admin.quizzes.index')
            ->with('success', 'Quiz deleted successfully.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load(['festival', 'event']);
        return view('Quiz.quizzes.show', compact('quiz'));
    }
}
