<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'local',
        'status_id',
        'user_id',
    ];

    /**
     * Scope to get only active districts (excluding "All")
     */
    public function scopeActive($query)
    {
        return $query->where('status_id', 1);
    }

    /**
     * Get the users in this district
     */
    public function users()
    {
        return $this->hasMany(User::class, 'district');
    }
}
