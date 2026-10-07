<?php


namespace App\Http\Controllers;

use App\Models\Quiz\Event;
use App\Models\Quiz\Festival;
use App\Models\Quiz\Eventtype;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['festival', 'eventtype'])->paginate(10);
        return view('events.index', compact('events'));
    }

    public function create()
    {
        $festivals = Festival::pluck('name', 'id');
        $eventtypes = Eventtype::pluck('name', 'id');
        return view('events.form', compact('festivals', 'eventtypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'festival_id' => 'required|exists:festivals,id',
            'eventtype_id' => 'required|exists:eventtypes,id',
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        Event::create($request->all());
        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        $festivals = Festival::pluck('name', 'id');
        $eventtypes = Eventtype::pluck('name', 'id');
        return view('events.form', compact('event', 'festivals', 'eventtypes'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'festival_id' => 'required|exists:festivals,id',
            'eventtype_id' => 'required|exists:eventtypes,id',
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $event->update($request->all());
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
