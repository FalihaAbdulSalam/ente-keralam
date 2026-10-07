<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creativethoughts extends Model
{
    use HasFactory;

    protected $table = 'creativethoughts';

    protected $guarded = [
      
    ];

    /**
     * Relationship: Each counter belongs to a sector.
     */
   
}
