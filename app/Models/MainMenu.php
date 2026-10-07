<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'entitle',
        'maltitle',
        'slug',
        'order',
        'status',
    ];


    public function submenus()
{
    return $this->hasMany(SubMenu::class, 'main_menu_id')->orderBy('order');
}

}