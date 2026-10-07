<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::where('status', 'published');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        $news = $query->orderBy('published_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'string|max:255',
            'is_featured' => 'boolean',
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
            $imagePath = $request->file('image')->store('news', 'public');
            $data['image'] = $imagePath;
        }

        $data['published_at'] = now();

        $news = News::create($data);

        return response()->json([
            'success' => true,
            'message' => 'News created successfully',
            'data' => $news
        ], 201);
    }

    public function show(string $id)
    {
        $news = News::where('status', 'published')->find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function update(Request $request, string $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'content' => 'string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'string|max:255',
            'is_featured' => 'boolean',
            'status' => 'in:published,draft,archived',
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
            $imagePath = $request->file('image')->store('news', 'public');
            $data['image'] = $imagePath;
        }

        $news->update($data);

        return response()->json([
            'success' => true,
            'message' => 'News updated successfully',
            'data' => $news->fresh()
        ]);
    }

    public function destroy(string $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found'
            ], 404);
        }

        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'News deleted successfully'
        ]);
    }
}
