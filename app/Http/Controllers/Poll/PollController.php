<?php

namespace App\Http\Controllers\Poll;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use App\Models\Poll\Poll;
use App\Models\Poll\PollQuestion;
use App\Models\Poll\PollOption;
use App\Models\Quiz\Festival;
use App\Models\Quiz\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class PollController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $polls = Poll::with(['festival', 'event'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('Poll.polls.index', compact('polls', 'menus'));
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $festivals = Festival::orderBy('name')->get();
        $events = Event::orderBy('name')->get();
        return view('Poll.polls.form', compact('festivals', 'events', 'menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'festival_id' => 'required|exists:festivals,id',
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'topic' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'about' => 'nullable|string',
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
                $folder = "uploads/polls/{$field}s";
                $destination = public_path($folder);
                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0775, true);
                }
                $file->move($destination, $filename);
                $validated[$field] = "$folder/$filename";
            }
        }

        $poll = Poll::create($validated);

        if ($request->has('questions')) {
            foreach ($request->questions as $order => $qData) {
                if (!empty($qData['text'])) {
                    $question = PollQuestion::create([
                        'poll_id' => $poll->id,
                        'question_text' => $qData['text'],
                        'order' => $order + 1,
                    ]);

                    if (!empty($qData['options'])) {
                        foreach ($qData['options'] as $optText) {
                            if (trim($optText) != '') {
                                PollOption::create([
                                    'poll_id' => $poll->id,
                                    'poll_question_id' => $question->id,
                                    'option_text' => $optText,
                                    'votes' => 0,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return redirect()
            ->route('admin.polls.index')
            ->with('success', 'Poll created successfully with questions.');
    }

    public function edit(Poll $poll)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $festivals = Festival::orderBy('name')->get();
        $events = Event::orderBy('name')->get();
        $poll->load('questions.options');
        return view('Poll.polls.form', compact('poll', 'festivals', 'events', 'menus'));
    }

    public function update(Request $request, Poll $poll)
    {
        $validated = $request->validate([
            'festival_id' => 'required|exists:festivals,id',
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'topic' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'about' => 'nullable|string',
            'terms_condition' => 'nullable|string',
            'status' => 'required|boolean',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'attachment' => 'nullable|mimes:pdf,doc,docx|max:5120',
            'points' => 'nullable|integer|min:0',
        ]);

        foreach (['poster', 'banner', 'attachment'] as $field) {
            if ($request->hasFile($field)) {
                if ($poll->$field && File::exists(public_path($poll->$field))) {
                    File::delete(public_path($poll->$field));
                }
                $file = $request->file($field);
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $folder = "uploads/polls/{$field}s";
                $destination = public_path($folder);
                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0775, true);
                }
                $file->move($destination, $filename);
                $validated[$field] = "$folder/$filename";
            }
        }

        $poll->update($validated);
        $poll->questions()->delete();

        if ($request->has('questions')) {
            foreach ($request->questions as $order => $qData) {
                if (!empty($qData['text'])) {
                    $question = PollQuestion::create([
                        'poll_id' => $poll->id,
                        'question_text' => $qData['text'],
                        'order' => $order + 1,
                    ]);

                    if (!empty($qData['options'])) {
                        foreach ($qData['options'] as $optText) {
                            if (trim($optText) != '') {
                                PollOption::create([
                                    'poll_id' => $poll->id,
                                    'poll_question_id' => $question->id,
                                    'option_text' => $optText,
                                    'votes' => 0,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return redirect()
            ->route('admin.polls.index')
            ->with('success', 'Poll updated successfully with questions.');
    }

    public function destroy(Poll $poll)
    {
        foreach (['poster', 'banner', 'attachment'] as $fileField) {
            if ($poll->$fileField && File::exists(public_path($poll->$fileField))) {
                File::delete(public_path($poll->$fileField));
            }
        }

        $poll->delete();

        return redirect()
            ->route('admin.polls.index')
            ->with('success', 'Poll deleted successfully.');
    }

    public function show(Poll $poll)
    {
        $poll->load(['festival', 'event', 'questions.options']);
        return view('Poll.polls.show', compact('poll'));
    }
}
