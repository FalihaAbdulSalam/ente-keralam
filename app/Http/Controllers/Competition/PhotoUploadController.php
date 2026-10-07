<?php

namespace App\Http\Controllers\Competition;

use Illuminate\Http\Request;
use App\Models\Competition\FileSubmission;
use Illuminate\Support\Facades\Storage;

class PhotoUploadController extends Controller
{
    public function index()
    {
        $contestId = 1;
        $applicantId = 101;

        // Fetch the current applicant's record
        $submission = FileSubmission::where('contest_id', $contestId)
            ->where('applicant_id', $applicantId)
            ->first();

        return view('photo_upload', compact('submission'));
    }

    public function store(Request $request)
    {
        $contestId = 1;
        $applicantId = 101;

        // Check if a record already exists
        $existing = FileSubmission::where('contest_id', $contestId)
            ->where('applicant_id', $applicantId)
            ->first();

        // Validation (photo required only for first submission)
        $rules = [
            'upload_title' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:500',
        ];

        if (!$existing) {
            $rules['photo'] = 'required|image|mimes:jpeg,png,jpg|max:4096';
        } else {
            $rules['photo'] = 'nullable|image|mimes:jpeg,png,jpg|max:4096';
        }

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all()),
                    'errors' => $e->validator->errors()
                ], 422);
            }
            throw $e;
        }

        // If record exists → update
        if ($existing) {
            $submission = $existing;
            $submission->upload_title = $validated['upload_title'];
            $submission->remarks = $validated['remarks'] ?? null;

            // Handle new image upload if given
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $uniqueName = $applicantId . '_' . time() . '_' . $photo->getClientOriginalName();

                // Delete old files
                if (!empty($submission->photo_light_path)) {
                    $oldLight = str_replace('storage/', '', $submission->photo_light_path);
                    Storage::disk('public')->delete($oldLight);
                }
                if (!empty($submission->photo_heavy_path)) {
                    $oldHeavy = str_replace('storage/', '', $submission->photo_heavy_path);
                    Storage::disk('public')->delete($oldHeavy);
                }

                // Store new files
                $photo->storeAs('photo_light', $uniqueName, 'public');
                $photo->storeAs('photo_heavy', $uniqueName, 'public');

                // Update DB paths
                $submission->photo_light_path = 'storage/photo_light/' . $uniqueName;
                $submission->photo_heavy_path = 'storage/photo_heavy/' . $uniqueName;
            }

            $submission->save();

            // Return JSON response for AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Photo details updated successfully!',
                    'photo_light_path' => $submission->photo_light_path ? asset($submission->photo_light_path) : null,
                    'data' => [
                        'upload_title' => $submission->upload_title,
                        'remarks' => $submission->remarks,
                    ]
                ]);
            }
        } else {
            // Insert new record
            $photo = $request->file('photo');
            $uniqueName = $applicantId . '_' . time() . '_' . $photo->getClientOriginalName();

            $photo->storeAs('photo_light', $uniqueName, 'public');
            $photo->storeAs('photo_heavy', $uniqueName, 'public');

            $submission = FileSubmission::create([
                'contest_id' => $contestId,
                'applicant_id' => $applicantId,
                'upload_title' => $validated['upload_title'],
                'remarks' => $validated['remarks'] ?? null,
                'photo_light_path' => 'storage/photo_light/' . $uniqueName,
                'photo_heavy_path' => 'storage/photo_heavy/' . $uniqueName,
                'status' => 1,
                'file_type' => 1,
            ]);

            // Return JSON response for AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Photo details saved successfully!',
                    'photo_light_path' => asset($submission->photo_light_path),
                    'data' => [
                        'upload_title' => $submission->upload_title,
                        'remarks' => $submission->remarks,
                    ]
                ]);
            }
        }

        return redirect()->route('photo.upload')->with('success', 'Photo details saved successfully!');
    }
}
