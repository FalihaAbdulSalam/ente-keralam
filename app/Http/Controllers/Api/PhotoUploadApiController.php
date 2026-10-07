<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Competition\FileSubmission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PhotoUploadApiController extends Controller
{
    /**
     * Handle the API request from React frontend with Base64 image
     * POST /api/photo-upload
     */
    public function store(Request $request)
    {
        // Validate incoming JSON data
        $validator = Validator::make($request->all(), [
            'contest_id' => 'required|integer',
            'applicant_id' => 'required|integer',
            'upload_title' => 'required|string|max:255',
            'imageBase64' => 'required|string',
            'imageName' => 'required|string',
            'imageType' => 'required|string|in:image/jpeg,image/jpg,image/png',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $contestId = $request->contest_id;
            $applicantId = $request->applicant_id;

            // Check if a record already exists
            $existing = FileSubmission::where('contest_id', $contestId)
                ->where('applicant_id', $applicantId)
                ->first();

            if ($existing) {
                return $this->updateSubmission($request, $existing);
            } else {
                return $this->createSubmission($request);
            }

        } catch (\Exception $e) {
            \Log::error('Photo upload error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new submission from Base64 image
     */
    private function createSubmission(Request $request)
    {
        $contestId = $request->contest_id;
        $applicantId = $request->applicant_id;
        
        // Decode Base64 image
        $imageData = $request->imageBase64;
        
        // Remove data URI prefix if present
        if (strpos($imageData, 'data:image') === 0) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
        }
        
        // Decode base64
        $decodedImage = base64_decode($imageData);
        
        if ($decodedImage === false) {
            \Log::error('Base64 decode failed');
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid base64 image data'
            ], 400);
        }

        // Generate unique filename
        $extension = $this->getExtensionFromMimeType($request->imageType);
        $originalName = pathinfo($request->imageName, PATHINFO_FILENAME);
        $uniqueName = $applicantId . '_' . time() . '_' . Str::slug($originalName) . '.' . $extension;

        // Get absolute paths
        $storagePath = storage_path('app/public');
        $lightPath = $storagePath . '/photo_light';
        $heavyPath = $storagePath . '/photo_heavy';
        
        // Create directories if they don't exist
        if (!is_dir($lightPath)) {
            mkdir($lightPath, 0775, true);
            \Log::info('Created photo_light directory');
        }
        if (!is_dir($heavyPath)) {
            mkdir($heavyPath, 0775, true);
            \Log::info('Created photo_heavy directory');
        }

        // Full file paths
        $lightFilePath = $lightPath . '/' . $uniqueName;
        $heavyFilePath = $heavyPath . '/' . $uniqueName;

        \Log::info('Attempting to save files', [
            'filename' => $uniqueName,
            'decoded_size' => strlen($decodedImage),
            'light_full_path' => $lightFilePath,
            'heavy_full_path' => $heavyFilePath,
            'light_dir_writable' => is_writable($lightPath),
            'heavy_dir_writable' => is_writable($heavyPath)
        ]);

        // Save files using file_put_contents
        try {
            $lightResult = file_put_contents($lightFilePath, $decodedImage);
            $heavyResult = file_put_contents($heavyFilePath, $decodedImage);
            
            \Log::info('File save results', [
                'light_bytes_written' => $lightResult,
                'heavy_bytes_written' => $heavyResult,
                'light_exists' => file_exists($lightFilePath),
                'heavy_exists' => file_exists($heavyFilePath)
            ]);
            
            if ($lightResult === false || $heavyResult === false) {
                throw new \Exception('Failed to write files to disk');
            }
            
            // Set permissions
            chmod($lightFilePath, 0644);
            chmod($heavyFilePath, 0644);
            
        } catch (\Exception $e) {
            \Log::error('File save exception: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save file: ' . $e->getMessage()
            ], 500);
        }

        // Create database record
        $submission = FileSubmission::create([
            'contest_id' => $contestId,
            'applicant_id' => $applicantId,
            'upload_title' => $request->upload_title,
            'remarks' => $request->remarks ?? null,
            'photo_light_path' => 'storage/photo_light/' . $uniqueName,
            'photo_heavy_path' => 'storage/photo_heavy/' . $uniqueName,
            'status' => 1,
            'file_type' => 1,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Photo uploaded successfully!',
            'data' => [
                'id' => $submission->id,
                'contest_id' => $submission->contest_id,
                'applicant_id' => $submission->applicant_id,
                'upload_title' => $submission->upload_title,
                'remarks' => $submission->remarks,
                'photo_light_url' => asset($submission->photo_light_path),
                'photo_heavy_url' => asset($submission->photo_heavy_path),
                'submitted_at' => $submission->submitted_at,
            ]
        ], 201);
    }

    /**
     * Update existing submission
     */
    private function updateSubmission(Request $request, $submission)
    {
        $applicantId = $request->applicant_id;

        // Update text fields
        $submission->upload_title = $request->upload_title;
        $submission->remarks = $request->remarks ?? null;

        // Handle new image if provided
        if ($request->has('imageBase64') && !empty($request->imageBase64)) {
            $imageData = $request->imageBase64;
            
            if (strpos($imageData, 'data:image') === 0) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }
            
            $decodedImage = base64_decode($imageData);
            
            if ($decodedImage === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid base64 image data'
                ], 400);
            }

            $extension = $this->getExtensionFromMimeType($request->imageType);
            $originalName = pathinfo($request->imageName, PATHINFO_FILENAME);
            $uniqueName = $applicantId . '_' . time() . '_' . Str::slug($originalName) . '.' . $extension;

            $storagePath = storage_path('app/public');
            $lightPath = $storagePath . '/photo_light';
            $heavyPath = $storagePath . '/photo_heavy';
            
            $lightFilePath = $lightPath . '/' . $uniqueName;
            $heavyFilePath = $heavyPath . '/' . $uniqueName;

            // Delete old files
            if (!empty($submission->photo_light_path)) {
                $oldLightFile = storage_path('app/public/' . str_replace('storage/', '', $submission->photo_light_path));
                if (file_exists($oldLightFile)) {
                    unlink($oldLightFile);
                }
            }
            if (!empty($submission->photo_heavy_path)) {
                $oldHeavyFile = storage_path('app/public/' . str_replace('storage/', '', $submission->photo_heavy_path));
                if (file_exists($oldHeavyFile)) {
                    unlink($oldHeavyFile);
                }
            }

            // Save new files
            file_put_contents($lightFilePath, $decodedImage);
            file_put_contents($heavyFilePath, $decodedImage);
            
            chmod($lightFilePath, 0644);
            chmod($heavyFilePath, 0644);

            $submission->photo_light_path = 'storage/photo_light/' . $uniqueName;
            $submission->photo_heavy_path = 'storage/photo_heavy/' . $uniqueName;
        }

        $submission->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Photo updated successfully!',
            'data' => [
                'id' => $submission->id,
                'contest_id' => $submission->contest_id,
                'applicant_id' => $submission->applicant_id,
                'upload_title' => $submission->upload_title,
                'remarks' => $submission->remarks,
                'photo_light_url' => asset($submission->photo_light_path),
                'photo_heavy_url' => asset($submission->photo_heavy_path),
                'submitted_at' => $submission->submitted_at,
            ]
        ], 200);
    }

    /**
     * Get submission details
     */
    public function show($contestId, $applicantId)
    {
        $submission = FileSubmission::where('contest_id', $contestId)
            ->where('applicant_id', $applicantId)
            ->first();

        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $submission->id,
                'contest_id' => $submission->contest_id,
                'applicant_id' => $submission->applicant_id,
                'upload_title' => $submission->upload_title,
                'remarks' => $submission->remarks,
                'photo_light_url' => asset($submission->photo_light_path),
                'photo_heavy_url' => asset($submission->photo_heavy_path),
                'status' => $submission->status,
                'submitted_at' => $submission->submitted_at,
            ]
        ], 200);
    }

    /**
     * Helper: Get file extension from MIME type
     */
    private function getExtensionFromMimeType($mimeType)
    {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
        ];

        return $mimeMap[$mimeType] ?? 'jpg';
    }
}