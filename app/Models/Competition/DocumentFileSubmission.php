<?php

namespace App\Models\Competition;

use Illuminate\Database\Eloquent\Model;

class DocumentFileSubmission extends Model
{
    protected $table = 'document_file_submissions';

    protected $fillable = [
        'contest_id',
        'applicant_id',
        'title',
        'description',
        'pdf_file',
        'status'
    ];
}
