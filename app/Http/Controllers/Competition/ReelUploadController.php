<?php

namespace App\Http\Controllers\Competition;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Competition\VideoFileSubmission;

class ReelUploadController extends Controller
{
    /**
     * Show the reel upload form.
     * Old data (if any) is loaded so fields + preview stay filled after first submission.
     */
    public function showReelForm(Request $request)
    {
        /**
         * IMPORTANT:
         * Adjust how you get $contestId and $applicantId according to your app.
         * - From route query: /reel-upload?contest_id=1&applicant_id=10
         * - Or from session / auth user.
         */
        $contestId   = $request->query('contest_id');   // or fixed / from config
        $applicantId = $request->query('applicant_id'); // or auth()->id()
        $contestId=2;
        $applicantId=103;

        // If your form does NOT pass these, you can hard-code or handle null safely.
        // But since DB requires them, better to always have valid IDs.
        // You can remove the "abort" if you’re handling differently.
        if (!$contestId || !$applicantId) {
            abort(400, 'contest_id and applicant_id are required.');
        }

        $submission = VideoFileSubmission::where('contest_id', $contestId)
            ->where('applicant_id', $applicantId)
            ->first();

        // Build URLs for preview if file exists
        $videoLightUrl = $submission && $submission->video_light_path
            ? Storage::disk('public')->url($submission->video_light_path)
            : null;

        $videoHeavyUrl = $submission && $submission->video_heavy_path
            ? Storage::disk('public')->url($submission->video_heavy_path)
            : null;

        return view('reel_upload', [
            'contestId'     => $contestId,
            'applicantId'   => $applicantId,
            'submission'    => $submission,
            'videoLightUrl' => $videoLightUrl,
            'videoHeavyUrl' => $videoHeavyUrl,
        ]);
    }

    /**
     * Handle first submission and subsequent updates.
     *
     * Rules:
     * - Title & video file mandatory on FIRST submission
     * - Remarks optional always
     * - On UPDATE, video file is optional (keep old one if not changed)
     * - New video replaces old file (old file deleted from both folders)
     * - Original in reel1_video_heavy, copy/compressed in reel_video_light
     */
    public function saveReel(Request $request)
    {
        $contestId   = $request->input('contest_id');
        $applicantId = $request->input('applicant_id');

        if (!$contestId || !$applicantId) {
            return redirect()->back()->withErrors('contest_id and applicant_id are required.');
        }

        // Check if a submission already exists (so we can decide create vs update)
        $existing = VideoFileSubmission::where('contest_id', $contestId)
            ->where('applicant_id', $applicantId)
            ->first();

        // Validation rules
        $rules = [
            'contest_id'   => 'required|integer',
            'applicant_id' => 'required|integer',
            'upload_title' => 'required|string|max:255',
            'remarks'      => 'nullable|string|max:255',
        ];

        // File is required only if there is no existing record yet
        $fileRule = $existing
            ? 'nullable|file|mimes:mp4|max:512000' // 500 MB in KB
            : 'required|file|mimes:mp4|max:512000';

        $rules['video_file'] = $fileRule;

        $validated = $request->validate($rules);

        // Keep current paths, will change only if a new file is uploaded
        $videoHeavyPath = $existing ? $existing->video_heavy_path : null;
        $videoLightPath = $existing ? $existing->video_light_path : null;

        // If user uploaded a new file, delete old files and save new ones
        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');

            // Delete old heavy file
            if ($videoHeavyPath && Storage::disk('public')->exists($videoHeavyPath)) {
                Storage::disk('public')->delete($videoHeavyPath);
            }

            // Delete old light file
            if ($videoLightPath && Storage::disk('public')->exists($videoLightPath)) {
                Storage::disk('public')->delete($videoLightPath);
            }

            // Folders (relative to storage/app/public)
            $heavyFolder = 'reel1_video_heavy';
            $lightFolder = 'reel_video_light';

            // Unique filename
            $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();

            // Store original heavy video
            $videoHeavyPath = $file->storeAs($heavyFolder, $filename, 'public');

            // Create compressed/copy in light folder.
            // Currently just copy the file; later you can plug in FFMpeg for real compression.
            $videoLightPath = $this->createCompressedCopy($videoHeavyPath, $lightFolder, $filename);
        }

        if ($existing) {
            // Update existing row
            $existing->upload_title     = $validated['upload_title'];
            $existing->remarks          = $validated['remarks'] ?? null;
            $existing->video_heavy_path = $videoHeavyPath;
            $existing->video_light_path = $videoLightPath;
            $existing->save();

            $message = 'Reel updated successfully.';
        } else {
            // Create new row
            VideoFileSubmission::create([
                'contest_id'       => $validated['contest_id'],
                'applicant_id'     => $validated['applicant_id'],
                'upload_title'     => $validated['upload_title'],
                'remarks'          => $validated['remarks'] ?? null,
                'video_heavy_path' => $videoHeavyPath,
                'video_light_path' => $videoLightPath,
                'status'           => 1,
                'file_type'        => 1, // 1 = reel (you can change as per your logic)
            ]);

            $message = 'Reel submitted successfully.';
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Create a "light" copy of the video in reel_video_light.
     *
     * For now it just copies the file from heavy path.
     * Replace this with real compression logic (e.g., FFMpeg) when needed.
     */
    protected function createCompressedCopy(string $heavyPath, string $lightFolder, string $filename): ?string
    {
        if (!Storage::disk('public')->exists($heavyPath)) {
            return null;
        }

        $sourceStream = Storage::disk('public')->get($heavyPath);
        $lightPath    = $lightFolder . '/' . $filename;

        Storage::disk('public')->put($lightPath, $sourceStream);

        return $lightPath;
    }
}
