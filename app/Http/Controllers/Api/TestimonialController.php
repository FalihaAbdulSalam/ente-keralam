<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::where('is_active', true);

        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        $testimonials = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $testimonials
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'message' => 'required|string',
            'rating' => 'integer|min:1|max:5',
            'is_featured' => 'boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('testimonials', 'public');
            $data['avatar'] = $avatarPath;
        }

        $testimonial = Testimonial::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial created successfully',
            'data' => $testimonial
        ], 201);
    }

    public function show(string $id)
    {
        $testimonial = Testimonial::where('is_active', true)->find($id);

        if (!$testimonial) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $testimonial
        ]);
    }

    public function update(Request $request, string $id)
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'message' => 'string',
            'rating' => 'integer|min:1|max:5',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('testimonials', 'public');
            $data['avatar'] = $avatarPath;
        }

        $testimonial->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial updated successfully',
            'data' => $testimonial->fresh()
        ]);
    }

    public function destroy(string $id)
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial not found'
            ], 404);
        }

        $testimonial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial deleted successfully'
        ]);
    }
}
