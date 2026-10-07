<?php

namespace App\Http\Controllers;

use App\Models\Quiz\Festival;
use App\Models\Quiz\Campaign;
use Illuminate\Http\Request;

class FestivalController extends Controller
{
    public function index()
    {
        $festivals = Festival::with('campaign')->get(); // Eager load campaigns
        return view('festivals.index', compact('festivals'));
    }

    public function create()
    {
        $campaigns = Campaign::pluck('name', 'id'); // Fetch campaigns as key-value pairs
        return view('festivals.form', compact('campaigns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'campaign_id' => 'required|exists:campaigns,id',
        ]);

        Festival::create($request->all());
        return redirect()->route('festivals.index')->with('success', 'Festival created successfully.');
    }

    public function edit(Festival $festival)
    {
        $campaigns = Campaign::pluck('name', 'id');
        return view('festivals.form', compact('festival', 'campaigns'));
    }

    public function update(Request $request, Festival $festival)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'campaign_id' => 'required|exists:campaigns,id',
        ]);

        $festival->update($request->all());
        return redirect()->route('festivals.index')->with('success', 'Festival updated successfully.');
    }
}
