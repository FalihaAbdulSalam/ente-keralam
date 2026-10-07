<?php

namespace App\Models\Competition;

use Illuminate\Database\Eloquent\Model;

class FileSubmission extends Model
{
    protected $table = 'file_submissions';

    protected $fillable = [
        'contest_id',
        'applicant_id',
        'photo_light_path',
        'photo_heavy_path',
        'status',
        'file_type',
        'upload_title',
        'remarks',
        'submitted_at'
    ];

    public $timestamps = false; // Disable auto timestamps

    // Relationships (if you have these models)
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function contest()
    {
        return $this->belongsTo(MasterProgramme::class, 'contest_id');
    }
}
