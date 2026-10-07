<?php

namespace App\Http\Controllers;

use App\Models\Quiz\Eventtype;
use Illuminate\Http\Request;

class EventTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $eventtypes = Eventtype::all();
        return view('eventtypes.index', compact('eventtypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('eventtypes.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        Eventtype::create($validated);

        return redirect()->route('eventtypes.index')->with('success', 'eventtype created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Eventtype  $eventtype
     * @return \Illuminate\Http\Response
     */
    public function edit(Eventtype $eventtype)
    {
        return view('eventtypes.form', compact('eventtype'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Eventtype  $eventtype
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Eventtype $eventtype)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $eventtype->update($validated);

        return redirect()->route('eventtypes.index')->with('success', 'eventtype updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Eventtype  $eventtype
     * @return \Illuminate\Http\Response
     */
    public function destroy(Eventtype $eventtype)
    {
        $eventtype->delete();

        return redirect()->route('eventtypes.index')->with('success', 'eventtype deleted successfully!');
    }
}
