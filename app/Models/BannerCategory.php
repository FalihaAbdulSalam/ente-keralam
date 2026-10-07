<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status_id',
        'user_id',
    ];
    public function subcomponent_cat(){
        return $this->hasMany(SubCatTransaction::class,'sub_cat_id','id');

    }
}
