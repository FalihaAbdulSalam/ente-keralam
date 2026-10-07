<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CountersDetails;
use App\Models\AdminMenu;
use App\Models\SectorDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class CounterDetailController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        try {
            $sectors = CountersDetails::latest()->get();
            return view('admin.counter_details.index', compact('sectors', 'menus'));
        } catch (Exception $e) {
            Log::error('Error fetching sector details: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load sector list.');
        }
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $sectors = SectorDetail::where('status', 1)->get();
        return view('admin.counter_details.form', compact('menus','sectors'));
    }

    public function store(Request $request)
{
    try {
                    $validated = $request->validate([
                'sector_id'         => 'required|integer',
                'counter_number'    => 'required|string',
                  'entitle'             => 'required|string|max:255',
                 'maltitle'             => 'required|string|max:255',
                'numeric_type_icon' => 'nullable|string|max:255',
               'icon_file'  => 'nullable|file|mimes:png,jpg,jpeg,svg,gif|max:2048',
                'status'            => 'boolean',
            ]);

            // Handle file upload
            $iconFileName = null;

            if ($request->hasFile('icon_file')) {
                $file = $request->file('icon_file');
                $iconFileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/icons'), $iconFileName);
            }else if(isset($request->icon)&&isset($request->icon)){
                    $iconFileName=$request->icon;
            }

            // Add icon_file to validated data
            $validated['icon'] = 'uploads/icons/'.$iconFileName;

            // Create the record
            $counter = CountersDetails::create($validated);



        return redirect()
            ->route('admin.counter_details.index')
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
        $sectors = SectorDetail::where('status', 1)->get();

        try {
            $counter = CountersDetails::findOrFail($id);
            return view('admin.counter_details.form', compact('counter','menus','sectors'));
        } catch (Exception $e) {
            Log::error('Error fetching sector for edit: ' . $e->getMessage());
            return redirect()
                ->route('admin.counter_details.index')
                ->with('error', 'Sector not found.');
        }
    }

    public function update(Request $request, $id)
    {
       try {
                $validated = $request->validate([
                    'sector_id'         => 'required|integer',
                    'counter_number'    => 'required|string',
                    'entitle'           => 'required|string|max:255',
                    'maltitle'          => 'required|string|max:255',
                    'numeric_type_icon' => 'nullable|string|max:255',
                    'icon'              => 'nullable|string|max:255',   // FA icon class
                    'icon_file'         => 'nullable|file|mimes:png,jpg,jpeg,svg,gif|max:2048',
                    'status'            => 'boolean',
                ]);

                $counter = CountersDetails::findOrFail($id);

                // Handle file upload
                if ($request->hasFile('icon_file')) {

                    $file = $request->file('icon_file');
                    $iconFileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/icons'), $iconFileName);

                    // Update image icon
                    $validated['icon'] = 'uploads/icons/'.$iconFileName;

                } 

                

                // Save FA icon text separately

                // Update final data
                $counter->update($validated);

                return redirect()
                    ->route('admin.counter_details.index')
                    ->with('success', 'Counter updated successfully.');

            } catch (Exception $e) {

                Log::error('Error updating counter: ' . $e->getMessage());

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Failed to update counter.');
            }

    }

    public function destroy($id)
    {
        try {
            $sector_detail = CountersDetails::findOrFail($id);
            $sector_detail->delete();

            return redirect()
                ->route('admin.counter_details.index')
                ->with('success', 'Sector deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting sector: ' . $e->getMessage());
            return redirect()
                ->route('admin.counter_details.index')
                ->with('error', 'Failed to delete sector.');
        }
    }
}
