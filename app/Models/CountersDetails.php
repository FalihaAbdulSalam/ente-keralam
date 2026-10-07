<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountersDetails extends Model
{
    use HasFactory;

    protected $table = 'counters_in_page';

    protected $fillable = [
        'sector_id',
        'counter_number',
          'entitle',
        'maltitle',
        'numeric_type_icon',
        'icon',
        'status',
    ];

    /**
     * Relationship: Each counter belongs to a sector.
     */
    public function sector()
    {
        return $this->belongsTo(SectorDetail::class, 'sector_id', 'id');
    }
    public function articles()
    {
        return $this->belongsTo(Article::class, 'sector_id', 'sector_details_id');
    }
}
