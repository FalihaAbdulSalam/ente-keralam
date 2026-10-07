<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MainMenu;
use App\Models\AdminMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MainMenuController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $mainmenus = MainMenu::orderBy('order')->get();

        return view('admin.mainmenu.index', compact('mainmenus', 'menus'));
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);

        return view('admin.mainmenu.form', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entitle'   => 'required|string|max:255',
            'maltitle'  => 'nullable|string|max:255',
            'order'     => 'nullable|integer',
            'slug'      => 'nullable|string|max:255',
            'status'     => 'nullable|integer',

        ]);

        // If slug field is empty, set it as null
        $validated['slug'] = $request->slug 
            ? Str::slug($request->slug) 
            : null;

        MainMenu::create($validated);

        return redirect()->route('admin.mainmenu.index')->with('success', 'Menu added successfully!');
    }

    public function edit(MainMenu $mainmenu)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);

        return view('admin.mainmenu.form', compact('mainmenu', 'menus'));
    }

    public function update(Request $request, MainMenu $mainmenu)
    {
        $validated = $request->validate([
            'entitle'   => 'required|string|max:255',
            'maltitle'  => 'nullable|string|max:255',
            'order'     => 'nullable|integer',
            'status'     => 'nullable|integer',
            'slug'      => 'nullable|string|max:255',
        ]);

        $validated['slug'] = $request->slug 
            ? Str::slug($request->slug) 
            : null;

        $mainmenu->update($validated);

        return redirect()->route('admin.mainmenu.index')->with('success', 'Menu updated successfully!');
    }

    public function destroy(MainMenu $mainmenu)
    {
        $mainmenu->delete();

        return back()->with('success', 'Menu deleted successfully!');
    }
}
