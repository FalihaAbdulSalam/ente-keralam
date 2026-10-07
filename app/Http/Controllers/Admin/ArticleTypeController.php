<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleType;
use App\Models\AdminMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ArticleTypeController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $articletypes = ArticleType::latest()->get();
        return view('admin.articletypes.index', compact('articletypes', 'menus'));
    }

    public function create()
    {
        return view('admin.articletypes.form');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'entitle' => 'required|string|max:255',
                'maltitle' => 'nullable|string|max:255',
                'endescription' => 'nullable|string',
                'maldescription' => 'nullable|string',
                'status' => 'boolean',
            ]);

            ArticleType::create($validated);

            return redirect()->route('admin.articletypes.index')
                ->with('success', 'Article type added successfully.');
        } catch (Exception $e) {
            Log::error('Error adding article type: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to add article type.');
        }
    }

    public function edit(ArticleType $articletype)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        return view('admin.articletypes.form', compact('articletype', 'menus'));
    }

    public function update(Request $request, ArticleType $articletype)
    {
        try {
            $validated = $request->validate([
                'entitle' => 'required|string|max:255',
                'maltitle' => 'nullable|string|max:255',
                'endescription' => 'nullable|string',
                'maldescription' => 'nullable|string',
                'status' => 'boolean',
            ]);

            $articletype->update($validated);

            return redirect()->route('admin.articletypes.index')
                ->with('success', 'Article type updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating article type: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update article type.');
        }
    }

    public function destroy(ArticleType $articletype)
    {
        try {
            $articletype->delete();
            return redirect()->route('admin.articletypes.index')
                ->with('success', 'Article type deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting article type: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete article type.');
        }
    }
}
