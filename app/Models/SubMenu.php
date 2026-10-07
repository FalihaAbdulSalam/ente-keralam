<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_menu_id',
        'entitle',
        'maltitle',
        'slug',
        'order',
        'status',
    ];

    public function mainmenu()
    {
        return $this->belongsTo(MainMenu::class, 'main_menu_id');
    }
}
