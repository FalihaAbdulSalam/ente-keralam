<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    use HasFactory;

    protected $table = 'footers';

    protected $fillable = [
        'entitle',
        'maltitle',
        'footercategory_id',
        'link',
        'link_text',
        'status',
        'order_num',
    ];

}
