<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubMenu;
use App\Models\MainMenu;
use App\Models\AdminMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubMenuController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $submenus = SubMenu::with('mainmenu')->orderBy('main_menu_id')->orderBy('order')->get();

        return view('admin.submenu.index', compact('submenus', 'menus'));
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $mainmenus = MainMenu::orderBy('order')->get();

        return view('admin.submenu.form', compact('menus', 'mainmenus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'main_menu_id' => 'required|exists:main_menus,id',
            'entitle'      => 'required|string|max:255',
            'maltitle'     => 'nullable|string|max:255',
            'order'        => 'nullable|integer',
            'slug'         => 'nullable|string|max:255',
        ]);

        $validated['slug'] = $request->slug ? Str::slug($request->slug) : null;

        SubMenu::create($validated);

        return redirect()->route('admin.submenu.index')->with('success', 'Submenu added successfully!');
    }

    public function edit(SubMenu $submenu)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $mainmenus = MainMenu::orderBy('order')->get();

        return view('admin.submenu.form', compact('submenu', 'menus', 'mainmenus'));
    }

    public function update(Request $request, SubMenu $submenu)
    {
        $validated = $request->validate([
            'main_menu_id' => 'required|exists:main_menus,id',
            'entitle'      => 'required|string|max:255',
            'maltitle'     => 'nullable|string|max:255',
            'order'        => 'nullable|integer',
            'slug'         => 'nullable|string|max:255',
        ]);

        $validated['slug'] = $request->slug ? Str::slug($request->slug) : null;

        $submenu->update($validated);

        return redirect()->route('admin.submenu.index')->with('success', 'Submenu updated successfully!');
    }

    public function destroy(SubMenu $submenu)
    {
        $submenu->delete();

        return back()->with('success', 'Submenu deleted successfully!');
    }
}
