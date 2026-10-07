<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Poll::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        $polls = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $polls
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
            'department' => 'required|string|max:255',
            'last_date' => 'required|date|after:today',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('polls', 'public');
            $data['image'] = $imagePath;
        }

        $poll = Poll::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Poll created successfully',
            'data' => $poll
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $poll = Poll::find($id);

        if (!$poll) {
            return response()->json([
                'success' => false,
                'message' => 'Poll not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $poll
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $poll = Poll::find($id);

        if (!$poll) {
            return response()->json([
                'success' => false,
                'message' => 'Poll not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'department' => 'string|max:255',
            'last_date' => 'date|after:today',
            'status' => 'in:active,closed,pending',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('polls', 'public');
            $data['image'] = $imagePath;
        }

        $poll->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Poll updated successfully',
            'data' => $poll->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $poll = Poll::find($id);

        if (!$poll) {
            return response()->json([
                'success' => false,
                'message' => 'Poll not found'
            ], 404);
        }

        $poll->delete();

        return response()->json([
            'success' => true,
            'message' => 'Poll deleted successfully'
        ]);
    }
}
