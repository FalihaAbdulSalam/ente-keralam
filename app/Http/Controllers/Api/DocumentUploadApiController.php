<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Competition\DocumentFileSubmission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class DocumentUploadApiController extends Controller
{
    public function addContest(Request $request)
    {
        //die('111');
        try {
            $existing = DocumentFileSubmission::where('contest_id', $request->contest_id)
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:300',
            'pdf_file' => 'nullable|file|mimes:pdf|max:2048',
            'contest_id' => 'required',
            'applicant_id' => 'required',]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validate word count in remarks
            if ($request->filled('description')) {
                $wordCount = str_word_count($request->description);
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
            $data = [
                'contest_id'       => $request->contest_id,
                'applicant_id'     => $request->applicant_id,
                'title'            => $request->title,
                'description'      => $request->description,
                'status'           => 0,
                    ];
            if ($request->hasFile('pdf_file')) 
            {
            $file = $request->file('pdf_file');
            $filename = time().'_'.$request->applicant_id.'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/poem'), $filename);
            $data['pdf_file'] = $filename;
            }
            $contest = DocumentFileSubmission::create($data);

            Log::info("New contest created - ID: {$contest->id}, Applicant: {$request->applicant_id}");

            return response()->json([
                'success' => true,
                'message' => 'Contest uploaded successfully!',
                'data'    => $this->formatResponse($contest),
            ], 201); // 201 Created

        } catch (\Exception $e) {
            Log::error('Contest Upload Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 500);
        }
    }   
    public function showContest(Request $request)
    {
        //die('111');
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

            $contest = DocumentFileSubmission::where('contest_id', $request->contest_id)
                ->where('applicant_id', $request->applicant_id)
                ->first();

            if (!$contest) {
                return response()->json([
                    'success' => true,
                    'message' => 'No submission found',
                    'data'    => null,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contest submission found',
                'data'    => [
                    'id'               => $contest->id,
                    'contest_id'       => $contest->contest_id,
                    'applicant_id'     => $contest->applicant_id,
                    'title'            => $contest->title,
                    'description'      => $contest->description,
                    'pdf_file' => asset('uploads/poem/' . $contest->pdf_file),
                    'status'           => $contest->status,
                    'created_at'       => $contest->created_at,
                    'updated_at'       => $contest->updated_at,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get Contest Submission Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching submission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateContest(Request $request)
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:300',
            'pdf_file' => 'nullable|file|mimes:pdf|max:2048',
            'contest_id' => 'required',
            'applicant_id' => 'required',]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find existing submission
            $contest = DocumentFileSubmission::where('contest_id', $request->contest_id)
                ->where('applicant_id', $request->applicant_id)
                ->first();

            if (!$contest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submission not found. Please create a new submission first.',
                ], 404); // 404 Not Found
            }

            // Validate word count in description
            if ($request->filled('description')) {
                $wordCount = str_word_count($request->description);
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
            $data = [
                        'title'       => $request->title,
                        'description' => $request->description,
                    ];
            if ($request->hasFile('pdf_file')) {
            // delete old file
            if ($contest->pdf_file && File::exists(public_path('uploads/poem/'.$contest->pdf_file))) {
                File::delete(public_path('uploads/poem/'.$contest->pdf_file));
            }

            $file = $request->file('pdf_file');
            $filename = time().'_'.$request->applicant_id.'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/poem'), $filename);
            $data['pdf_file'] = $filename;
            }
            // Update the record
            $contest->update($data);

            Log::info("Contest updated - ID: {$contest->id}, Applicant: {$request->applicant_id}");

            return response()->json([
                'success' => true,
                'message' => 'Contest updated successfully!',
                'data'    => $this->formatResponse($contest->fresh()),
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
private function formatResponse($contest)
    {
        return [
            'id' => $contest->id,
            'contest_id' => $contest->contest_id,
            'applicant_id' => $contest->applicant_id,
            'title' => $contest->title,
            'description' => $contest->description,
            'status' => $contest->status,
            'pdf_file' => $contest->pdf_file ? asset('uploads/poem/' . $contest->pdf_file) : null,
            'created_at' => $contest->created_at,
            'updated_at' => $contest->updated_at,
        ];
    }
}
