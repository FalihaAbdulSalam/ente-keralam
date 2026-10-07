<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Competition\VideoFileSubmission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class ReelUploadApiController extends Controller
{
    /**
     * GET /api/reel?contest_id=X&applicant_id=Y
     * Returns existing reel data for pre-filling the React form.
     */
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'contest_id'   => 'required|integer',
                'applicant_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $reel = VideoFileSubmission::where('contest_id', $request->contest_id)
                ->where('applicant_id', $request->applicant_id)
                ->first();

            if (!$reel) {
                return response()->json([
                    'success' => true,
                    'message' => 'No submission found',
                    'data'    => null,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reel submission found',
                'data'    => [
                    'id'               => $reel->id,
                    'contest_id'       => $reel->contest_id,
                    'applicant_id'     => $reel->applicant_id,
                    'video_title'      => $reel->upload_title,
                    'remarks'          => $reel->remarks,
                    'video_light_path' => $reel->video_light_path,
                    'video_heavy_path' => $reel->video_heavy_path,
                    'video_light_url'  => $reel->video_light_path
                        ? asset('storage/' . $reel->video_light_path)
                        : null,
                    'video_heavy_url'  => $reel->video_heavy_path
                        ? asset('storage/' . $reel->video_heavy_path)
                        : null,
                    'status'           => $reel->status,
                    'submitted_at'     => $reel->submitted_at,
                    'updated_at'       => $reel->updated_at,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get Reel Submission Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching submission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/reel
     * Create new reel submission (first-time upload)
     */
    public function store(Request $request)
    {
        try {
            // Check if submission already exists
            $existing = VideoFileSubmission::where('contest_id', $request->contest_id)
                ->where('applicant_id', $request->applicant_id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submission already exists. Please use the update endpoint.',
                ], 409); // 409 Conflict
            }

            // Validation rules
            $validator = Validator::make($request->all(), [
                'contest_id'   => 'required|integer',
                'applicant_id' => 'required|integer',
                'video_title'  => 'required|string|max:200',
                'remarks'      => 'nullable|string',
                'video_file'   => 'required|string', // base64 encoded video - REQUIRED for new
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validate word count in remarks
            if ($request->filled('remarks')) {
                $wordCount = str_word_count($request->remarks);
                if ($wordCount > 300) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'remarks' => ['The remarks field must not exceed 300 words.']
                        ]
                    ], 422);
                }
            }

            // Process video file
            $videoResult = $this->processVideoFile($request->video_file, $request->applicant_id);

            if (!$videoResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $videoResult['message']
                ], 422);
            }

            // Create new record
            $reel = VideoFileSubmission::create([
                'contest_id'       => $request->contest_id,
                'applicant_id'     => $request->applicant_id,
                'upload_title'     => $request->video_title,
                'remarks'          => $request->remarks,
                'video_heavy_path' => $videoResult['video_heavy_path'],
                'video_light_path' => $videoResult['video_light_path'],
                'status'           => 1,
                'file_type'        => 2, // 2 = reel
            ]);

            Log::info("New reel created - ID: {$reel->id}, Applicant: {$request->applicant_id}");

            return response()->json([
                'success' => true,
                'message' => 'Reel uploaded successfully!',
                'data'    => $this->formatReelResponse($reel),
            ], 201); // 201 Created

        } catch (\Exception $e) {
            Log::error('Reel Upload Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/reel-upload
     * Multipart upload to prevent browser/PHP out-of-memory usage.
     * Expects: contest_id, applicant_id, video_title, remarks (optional), video (file)
     */
    public function storeMultipart(Request $request)
    {
        try {
            $existing = VideoFileSubmission::where('contest_id', $request->contest_id)
                ->where('applicant_id', $request->applicant_id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submission already exists. Please use the update endpoint.',
                ], 409);
            }

            $validator = Validator::make($request->all(), [
                'contest_id'   => 'required|integer',
                'applicant_id' => 'required|integer',
                'video_title'  => 'required|string|max:200',
                'remarks'      => 'nullable|string',
                'video'        => 'required|file|mimetypes:video/mp4|max:512000', // 500MB in KB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->filled('remarks')) {
                $wordCount = str_word_count($request->remarks);
                if ($wordCount > 300) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'remarks' => ['The remarks field must not exceed 300 words.']
                        ]
                    ], 422);
                }
            }

            /** @var UploadedFile $video */
            $video = $request->file('video');
            $videoResult = $this->storeUploadedMp4($video, (int) $request->applicant_id);
            if (!$videoResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $videoResult['message'],
                ], 422);
            }

            $reel = VideoFileSubmission::create([
                'contest_id'       => $request->contest_id,
                'applicant_id'     => $request->applicant_id,
                'upload_title'     => $request->video_title,
                'remarks'          => $request->remarks,
                'video_heavy_path' => $videoResult['video_heavy_path'],
                'video_light_path' => $videoResult['video_light_path'],
                'status'           => 1,
                'file_type'        => 2,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reel uploaded successfully!',
                'data'    => $this->formatReelResponse($reel),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Reel Multipart Upload Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/reel-update
     * Multipart update. Accepts optional video (file) to replace, without base64.
     */
    public function updateMultipart(Request $request)
    {
        try {
            // If a large upload exceeds PHP limits, Laravel may not populate the file at all.
            // Give a clearer message than "failed to upload".
            if ($request->has('video') && !$request->hasFile('video')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => [
                        'video' => ['Upload failed (file missing). This usually means the file exceeded server upload limits or could not be uploaded to server.'],
                    ],
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'contest_id'   => 'required|integer',
                'applicant_id' => 'required|integer',
                'video_title'  => 'required|string|max:200',
                'remarks'      => 'nullable|string',
                // Be a bit more permissive: some browsers send 'video/mp4', others may vary.
                // Also validate extension.
                'video'        => 'nullable|file|mimes:mp4|mimetypes:video/mp4,video/x-m4v,application/mp4|max:512000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->filled('remarks')) {
                $wordCount = str_word_count($request->remarks);
                if ($wordCount > 300) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'remarks' => ['The remarks field must not exceed 300 words.']
                        ]
                    ], 422);
                }
            }

            $reel = VideoFileSubmission::where('contest_id', $request->contest_id)
                ->where('applicant_id', $request->applicant_id)
                ->first();

            if (!$reel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submission not found. Please create a new submission first.',
                ], 404);
            }

            $videoHeavyPath = $reel->video_heavy_path;
            $videoLightPath = $reel->video_light_path;

            if ($request->hasFile('video')) {
                $video = $request->file('video');

                if (!$video->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'video' => ['The uploaded video is invalid. Please retry, or try a smaller file if your server upload limits are low.'],
                        ],
                    ], 422);
                }

                $videoResult = $this->storeUploadedMp4($video, (int) $request->applicant_id);
                if (!$videoResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $videoResult['message'],
                    ], 422);
                }

                $this->deleteVideoFiles($reel);

                $videoHeavyPath = $videoResult['video_heavy_path'];
                $videoLightPath = $videoResult['video_light_path'];
            }

            $reel->update([
                'upload_title'     => $request->video_title,
                'remarks'          => $request->remarks,
                'video_heavy_path' => $videoHeavyPath,
                'video_light_path' => $videoLightPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reel updated successfully!',
                'data'    => $this->formatReelResponse($reel->fresh()),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Reel Multipart Update Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the submission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/reel
     * Update existing reel submission (edit)
     * Video file is optional - if not provided, keeps existing video
     */
    public function update(Request $request)
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'contest_id'   => 'required|integer',
                'applicant_id' => 'required|integer',
                'video_title'  => 'required|string|max:200',
                'remarks'      => 'nullable|string',
                'video_file'   => 'nullable|string', // base64 encoded video - OPTIONAL for update
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find existing submission
            $reel = VideoFileSubmission::where('contest_id', $request->contest_id)
                ->where('applicant_id', $request->applicant_id)
                ->first();

            if (!$reel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submission not found. Please create a new submission first.',
                ], 404); // 404 Not Found
            }

            // Validate word count in remarks
            if ($request->filled('remarks')) {
                $wordCount = str_word_count($request->remarks);
                if ($wordCount > 300) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'remarks' => ['The remarks field must not exceed 300 words.']
                        ]
                    ], 422);
                }
            }

            // Keep existing video paths
            $videoHeavyPath = $reel->video_heavy_path;
            $videoLightPath = $reel->video_light_path;

            // If new video file is provided, process it
            if ($request->filled('video_file')) {
                $videoResult = $this->processVideoFile($request->video_file, $request->applicant_id);

                if (!$videoResult['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $videoResult['message']
                    ], 422);
                }

                // Delete old video files
                $this->deleteVideoFiles($reel);

                // Update with new video paths
                $videoHeavyPath = $videoResult['video_heavy_path'];
                $videoLightPath = $videoResult['video_light_path'];

                Log::info("Video replaced for reel ID: {$reel->id}, Applicant: {$request->applicant_id}");
            }

            // Update the record
            $reel->update([
                'upload_title'     => $request->video_title,
                'remarks'          => $request->remarks,
                'video_heavy_path' => $videoHeavyPath,
                'video_light_path' => $videoLightPath,
            ]);

            Log::info("Reel updated - ID: {$reel->id}, Applicant: {$request->applicant_id}");

            return response()->json([
                'success' => true,
                'message' => 'Reel updated successfully!',
                'data'    => $this->formatReelResponse($reel->fresh()),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Reel Update Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the submission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process base64 video file and store it
     * Returns array with success status and file paths or error message
     */
    private function processVideoFile($base64Video, $applicantId)
    {
        // Remove data URI scheme if present
        if (strpos($base64Video, 'data:') === 0) {
            $base64Video = substr($base64Video, strpos($base64Video, ',') + 1);
        }

        // Decode base64
        $videoData = base64_decode($base64Video);

        if ($videoData === false) {
            return [
                'success' => false,
                'message' => 'Invalid base64 video data'
            ];
        }

        // Check file size (500MB)
        $fileSize = strlen($videoData);
        $maxSize = 500 * 1024 * 1024; // 500MB in bytes

        if ($fileSize > $maxSize) {
            return [
                'success' => false,
                'message' => 'Video file size exceeds 500MB limit'
            ];
        }

        // Validate MP4 format
        if (!$this->isValidMp4($videoData)) {
            return [
                'success' => false,
                'message' => 'Only MP4 video files are allowed'
            ];
        }

        // Generate unique filename
        $filename = $applicantId . "_" . time() . "_" . uniqid() . ".mp4";

        // Store original video (heavy)
        $videoHeavyPath = 'reel1_video_heavy/' . $filename;
        Storage::disk('public')->put($videoHeavyPath, $videoData);

        // Create light version (copy for now, compress later if FFmpeg available)
        $videoLightPath = 'reel1_video_light/' . $filename;
        Storage::disk('public')->put($videoLightPath, $videoData);

        Log::info("Video stored for applicant {$applicantId}: {$filename}");

        return [
            'success' => true,
            'video_heavy_path' => $videoHeavyPath,
            'video_light_path' => $videoLightPath,
        ];
    }

    /**
     * Store an uploaded MP4 by streaming to disk (no full read into memory).
     */
    private function storeUploadedMp4(UploadedFile $video, int $applicantId): array
    {
        if (!$video->isValid()) {
            return [
                'success' => false,
                'message' => 'Invalid uploaded video file',
            ];
        }

        // Extra safety: confirm mp4 extension / guess.
        $ext = strtolower($video->getClientOriginalExtension() ?: '');
        if ($ext && $ext !== 'mp4') {
            return [
                'success' => false,
                'message' => 'Only MP4 video files are allowed',
            ];
        }

        $filename = $applicantId . "_" . time() . "_" . uniqid() . ".mp4";

        $videoHeavyPath = 'reel1_video_heavy/' . $filename;
        $videoLightPath = 'reel1_video_light/' . $filename;

        // Stream/copy the uploaded tmp file; Storage will not load whole file into PHP memory.
        $heavyStream = fopen($video->getRealPath(), 'r');
        Storage::disk('public')->put($videoHeavyPath, $heavyStream);
        if (is_resource($heavyStream)) fclose($heavyStream);

        $lightStream = fopen($video->getRealPath(), 'r');
        Storage::disk('public')->put($videoLightPath, $lightStream);
        if (is_resource($lightStream)) fclose($lightStream);

        Log::info("Multipart video stored for applicant {$applicantId}: {$filename}");

        return [
            'success' => true,
            'video_heavy_path' => $videoHeavyPath,
            'video_light_path' => $videoLightPath,
        ];
    }

    /**
     * Delete video files from storage
     */
    private function deleteVideoFiles($reel)
    {
        if ($reel->video_light_path && Storage::disk('public')->exists($reel->video_light_path)) {
            Storage::disk('public')->delete($reel->video_light_path);
            Log::info("Deleted old light video: {$reel->video_light_path}");
        }

        if ($reel->video_heavy_path && Storage::disk('public')->exists($reel->video_heavy_path)) {
            Storage::disk('public')->delete($reel->video_heavy_path);
            Log::info("Deleted old heavy video: {$reel->video_heavy_path}");
        }
    }

    /**
     * Format reel data for API response
     */
    private function formatReelResponse($reel)
    {
        return [
            'id'               => $reel->id,
            'contest_id'       => $reel->contest_id,
            'applicant_id'     => $reel->applicant_id,
            'video_title'      => $reel->upload_title,
            'remarks'          => $reel->remarks,
            'video_light_path' => $reel->video_light_path,
            'video_heavy_path' => $reel->video_heavy_path,
            'video_light_url'  => $reel->video_light_path
                ? asset('storage/' . $reel->video_light_path)
                : null,
            'video_heavy_url'  => $reel->video_heavy_path
                ? asset('storage/' . $reel->video_heavy_path)
                : null,
            'status'           => $reel->status,
            'submitted_at'     => $reel->submitted_at,
            'updated_at'       => $reel->updated_at,
        ];
    }

    /**
     * Improved MP4 file validation
     * Checks for MP4 file signature and format
     */
    private function isValidMp4($fileData)
    {
        // Check minimum length
        if (strlen($fileData) < 12) {
            return false;
        }

        // Check for MP4 file signature (ftyp)
        $signature = substr($fileData, 4, 4);

        // Common MP4 signatures
        $mp4Signatures = [
            'ftypmp4',  // MP4 Base Media v1
            'ftypisom', // ISO Base Media file (MPEG-4) v1
            'ftypM4V',  // Apple iTunes Video
            'ftypM4A',  // Apple iTunes Audio
            'ftypf4v',  // Flash MP4 Video
            'ftypavc1', // MP4 with AVC video
            'ftypiso2', // ISO Base Media v2
            'ftypmp42', // MP4 v2
            'ftypmsnv', // MPEG-4 for Sony PSP
            'ftypndas', // Nero Digital AAC Audio
        ];

        // Check if the signature matches any known MP4 signature
        foreach ($mp4Signatures as $mp4Sig) {
            if (strpos($fileData, $mp4Sig) !== false) {
                return true;
            }
        }

        // Additional check for ftyp at position 4
        if ($signature === 'ftyp') {
            return true;
        }

        // Also check for common MP4 box types
        $boxTypes = ['ftyp', 'moov', 'mdat', 'free', 'skip', 'wide', 'pnot'];
        foreach ($boxTypes as $boxType) {
            if (strpos($fileData, $boxType) !== false) {
                return true;
            }
        }

        return false;
    }
}
