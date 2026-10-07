<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MainMenu;

class MenuApiController extends Controller
{
    /**
     * 🔹 Get all active Main Menus with their active Submenus (nested)
     */
    public function menuTree()
    {
        $menus = MainMenu::where('status', 1)
            ->orderBy('order')
            ->with(['submenus' => function ($query) {
                $query->where('status', 1)
                      ->orderBy('order')
                      ->select('id', 'main_menu_id', 'entitle', 'maltitle', 'slug', 'order', 'status');
            }])
            ->select('id', 'entitle', 'maltitle', 'slug', 'order', 'status')
            
            ->get();

        return response()->json([
            'status' => true,
            'count'  => $menus->count(),
            'data'   => $menus
        ], 200);
    }
}
