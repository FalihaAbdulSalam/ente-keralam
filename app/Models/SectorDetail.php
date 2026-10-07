<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorDetail extends Model
{
    use HasFactory;

    // 👇 REQUIRED so create() can mass-assign values
    protected $table = 'sector_details';
    protected $fillable = [
        'entitle',
        'maltitle',
        'endescription',
        'maldescription',
        'icon',
        'rupee_icon',
        'poster',
        'status',
    ];

    // optional if table name not plural
    // protected $table = 'sector_details';
    public function getRouteKeyName()
    {
        return 'link';
    }

    public function counters()
    {
        return $this->hasMany(CountersDetails::class, 'sector_id','id');
    }
}
