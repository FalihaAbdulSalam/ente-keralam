<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Creativethoughts;
use App\Models\AdminMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class CreativethoughtsController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        try {
              $creativethoughts = Creativethoughts::latest()->paginate(15);
              return view('admin.creativethoughts.index', compact('creativethoughts', 'menus'));
        } catch (Exception $e) {
            // dd($e);
              Log::error('Error fetching creative thoughts: ' . $e->getMessage());
              return redirect()->back()->with('error', 'Unable to load creative thoughts list.');
        }
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
            return view('admin.creativethoughts.form', compact('menus'));
    }

    public function store(Request $request)
{
    try {
                        $validated = $request->validate([
                    'entitle'   => 'required|string|max:255',
                    'maltitle'  => 'required|string|max:255',
                    'poster'    => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
                    'order_num' => 'nullable|integer',
                    'status'    => 'boolean',
                ]);

                // Handle file upload
                $posterFileName = null;
                if ($request->hasFile('poster')) {
                    $file = $request->file('poster');
                    $posterFileName = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
                    $destination = public_path('uploads/creativethoughts');
                    if (!file_exists($destination)) {
                        mkdir($destination, 0755, true);
                    }
                    $file->move($destination, $posterFileName);
                }

                $data = $validated;
                $data['poster'] = 'creativethoughts/'.$posterFileName;

                Creativethoughts::create($data);

                return redirect()
                    ->route('admin.creativethoughts.index')
                    ->with('success', 'Creative thought added successfully.');
    } catch (Exception $e) {
            Log::error('Error adding creative thought: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to add creative thought. Please try again.'. $e->getMessage());
    }
}


    public function edit($id)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        try {
            $creativethought = Creativethoughts::findOrFail($id);
            return view('admin.creativethoughts.form', compact('creativethought','menus'));
        } catch (Exception $e) {
            Log::error('Error fetching creative thought for edit: ' . $e->getMessage());
            return redirect()
                ->route('admin.creativethoughts.index')
                ->with('error', 'Creative thought not found.');
        }
    }

    public function update(Request $request, $id)
    {
       try {
                $validated = $request->validate([
                    'entitle'   => 'required|string|max:255',
                    'maltitle'  => 'required|string|max:255',
                    'poster'    => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
                    'order_num' => 'nullable|integer',
                    'status'    => 'boolean',
                ]);

                $creativethought = Creativethoughts::findOrFail($id);

                // Handle file upload
                if ($request->hasFile('poster')) {
                    $file = $request->file('poster');
                    $posterFileName = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
                    $destination = public_path('uploads/creativethoughts');
                    if (!file_exists($destination)) {
                        mkdir($destination, 0755, true);
                    }
                    $file->move($destination, $posterFileName);

                    // delete old file if exists
                    if ($creativethought->poster && file_exists(public_path('uploads/creativethoughts/' . $creativethought->poster))) {
                        @unlink(public_path('uploads/creativethoughts/' . $creativethought->poster));
                    }

                    $validated['poster'] = 'creativethoughts/'.$posterFileName;
                }

                $creativethought->update($validated);

                return redirect()
                    ->route('admin.creativethoughts.index')
                    ->with('success', 'Creative thought updated successfully.');

            } catch (Exception $e) {
                Log::error('Error updating creative thought: ' . $e->getMessage());

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Failed to update creative thought.');
            }

    }

    public function destroy($id)
    {
        try {
            $creativethought = Creativethoughts::findOrFail($id);
            // delete poster file if exists
            if ($creativethought->poster && file_exists(public_path('uploads/creativethoughts/' . $creativethought->poster))) {
                @unlink(public_path('uploads/creativethoughts/' . $creativethought->poster));
            }
            $creativethought->delete();

            return redirect()
                ->route('admin.creativethoughts.index')
                ->with('success', 'Creative thought deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting creative thought: ' . $e->getMessage());
            return redirect()
                ->route('admin.creativethoughts.index')
                ->with('error', 'Failed to delete creative thought.');
        }
    }
}
