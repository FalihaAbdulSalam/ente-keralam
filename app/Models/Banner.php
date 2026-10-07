<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banners';

    protected $fillable = [
        'poster',
        'malposter',
        'entitle',
        'maltitle',
        'endescription',
        'maldescription',
        'youtubelink',
        'link',
        'link_text',
        'banercategory_id',
        'status',
        'order_num',
    ];

}
