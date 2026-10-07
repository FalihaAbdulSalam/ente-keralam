<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SectorDetail;
use App\Models\AdminMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class SectorDetailController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        try {
            $sectors = SectorDetail::latest()->get();
            return view('admin.sector_details.index', compact('sectors', 'menus'));
        } catch (Exception $e) {
            Log::error('Error fetching sector details: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load sector list.');
        }
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        return view('admin.sector_details.form', compact('menus'));
    }

    public function store(Request $request)
{
        try {
        $validated = $request->validate([
            'entitle' => 'required|string|max:255',
            'maltitle' => 'nullable|string|max:255',
            'endescription' => 'nullable|string',
            'maldescription' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
                'poster' => 'nullable|file|mimes:png,jpg,jpeg,svg,gif|max:4096',
                'rupee_icon'=> 'nullable|file|mimes:png,jpg,jpeg,svg,gif|max:4096',
            'status' => 'boolean',
        ]);

        // Handle poster upload
        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $posterFileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/posters'), $posterFileName);
            $validated['poster'] = $posterFileName;
        }

        if ($request->hasFile('rupee_icon')) {
            $file = $request->file('rupee_icon');
            $rupee_icon = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/rupee_icon'), $rupee_icon);
            $validated['rupee_icon'] = $rupee_icon;
        }

        SectorDetail::create($validated);

        return redirect()
            ->route('admin.sector_details.index')
            ->with('success', 'Sector added successfully.');
    } catch (Exception $e) {
        Log::error('Error adding sector: ' . $e->getMessage());
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Failed to add sector. Please try again.');
    }
}


    public function edit($id)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        try {
            $sector_detail = SectorDetail::findOrFail($id);
            return view('admin.sector_details.form', compact('sector_detail','menus'));
        } catch (Exception $e) {
            Log::error('Error fetching sector for edit: ' . $e->getMessage());
            return redirect()
                ->route('admin.sector_details.index')
                ->with('error', 'Sector not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'entitle' => 'required|string|max:255',
                'maltitle' => 'nullable|string|max:255',
                'endescription' => 'nullable|string',
                'maldescription' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
                'poster' => 'nullable|file|mimes:png,jpg,jpeg,svg,gif|max:4096',
                'rupee_icon'=> 'nullable|file|mimes:png,jpg,jpeg,svg,gif|max:4096',

                'status' => 'boolean',
            ]);
            // dd( $request->all());
            $sector_detail = SectorDetail::findOrFail($id);

            // Handle poster upload on update (delete old poster if present)
            if ($request->hasFile('poster')) {
                // delete old file if exists
                if (!empty($sector_detail->poster) && file_exists(public_path('uploads/posters/' . $sector_detail->poster))) {
                    @unlink(public_path('uploads/posters/' . $sector_detail->poster));
                }

                $file = $request->file('poster');
                $posterFileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/posters'), $posterFileName);
                $validated['poster'] = $posterFileName;
            }

              if ($request->hasFile('rupee_icon')) {
                // delete old file if exists
                if (!empty($sector_detail->rupee_icon) && file_exists(public_path('uploads/rupee_icon/' . $sector_detail->rupee_icon))) {
                    @unlink(public_path('uploads/rupee_icon/' . $sector_detail->rupee_icon));
                }

                $file = $request->file('rupee_icon');
                $rupee_icon = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/rupee_icon'), $rupee_icon);
                $validated['rupee_icon'] = $rupee_icon;
            }

            $sector_detail->update($validated);

            return redirect()
                ->route('admin.sector_details.index')
                ->with('success', 'Sector updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating sector: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update sector.');
        }
    }

    public function destroy($id)
    {
        try {
            $sector_detail = SectorDetail::findOrFail($id);
            $sector_detail->delete();

            return redirect()
                ->route('admin.sector_details.index')
                ->with('success', 'Sector deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting sector: ' . $e->getMessage());
            return redirect()
                ->route('admin.sector_details.index')
                ->with('error', 'Failed to delete sector.');
        }
    }
}
