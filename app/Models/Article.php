<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'articletype_id',
        'sector_details_id',
        'entitle',
        'maltitle',
        'endescription',
        'maldescription',
        'encontent',
        'malcontent',
        'poster',
        'banner',
        'status',
        'articletype',
        'keywords'
    ];

    // Relationships
    public function type()
    {
        return $this->belongsTo(ArticleType::class, 'articletype_id');
    }

    public function sector()
    {
        return $this->belongsTo(SectorDetail::class, 'sector_details_id');
    }

    public function counters()
    {
        return $this->hasMany(CountersDetails::class,'sector_id', 'sector_details_id');
    }
}
