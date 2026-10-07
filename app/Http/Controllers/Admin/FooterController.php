<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use App\Models\FooterCategory;
use App\Models\AdminMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class FooterController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        try {
            $footers = Footer::latest()->paginate(15);
            return view('admin.footers.index', compact('footers', 'menus'));
        } catch (Exception $e) {
            Log::error('Error fetching footers: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load footers.');
        }
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $categories = FooterCategory::where('status', 1)->get();
        return view('admin.footers.form', compact('menus', 'categories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'entitle' => 'required|string|max:255',
                'maltitle' => 'required|string|max:255',
                'link' => 'nullable|url|max:255',
                'link_text' => 'nullable|string|max:255',
                'footercategory_id' => 'nullable|integer',
                'order_num' => 'nullable|integer',
                'status' => 'boolean',
            ]);

            $data = $validated;
            $data['footercategory_id'] = $validated['footercategory_id'] ?? 1;
            $data['status'] = $validated['status'] ?? 1;

            Footer::create($data);

            return redirect()->route('admin.footers.index')->with('success', 'Footer created successfully.');
        } catch (Exception $e) {
            Log::error('Error creating footer: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create footer.');
        }
    }

    public function edit($id)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $categories = FooterCategory::where('status', 1)->get();
        try {
            $footer = Footer::findOrFail($id);
            return view('admin.footers.form', compact('footer', 'menus', 'categories'));
        } catch (Exception $e) {
            Log::error('Error fetching footer for edit: ' . $e->getMessage());
            return redirect()->route('admin.footers.index')->with('error', 'Footer not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'entitle' => 'required|string|max:255',
                'maltitle' => 'required|string|max:255',
                'link' => 'nullable|url|max:255',
                'link_text' => 'nullable|string|max:255',
                'footercategory_id' => 'nullable|integer',
                'order_num' => 'nullable|integer',
                'status' => 'boolean',
            ]);

            $footer = Footer::findOrFail($id);
            $footer->update($validated);

            return redirect()->route('admin.footers.index')->with('success', 'Footer updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating footer: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update footer.');
        }
    }

    public function destroy($id)
    {
        try {
            $footer = Footer::findOrFail($id);
            $footer->delete();
            return redirect()->route('admin.footers.index')->with('success', 'Footer deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting footer: ' . $e->getMessage());
            return redirect()->route('admin.footers.index')->with('error', 'Failed to delete footer.');
        }
    }

}
