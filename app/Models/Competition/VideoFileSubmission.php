<?php

namespace App\Models\Competition;

use Illuminate\Database\Eloquent\Model;

class VideoFileSubmission extends Model
{
    protected $table = 'video_file_submissions';

    protected $fillable = [
        'contest_id',
        'applicant_id',
        'video_light_path',
        'video_heavy_path',
        'status',
        'file_type',
        'upload_title',
        'remarks',
        'approved_by',
        'approved_at',
    ];
}
