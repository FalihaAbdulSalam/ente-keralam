<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\AdminMenu;
use App\Models\SectorDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class FAQController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        try {
            $faqs = Faq::latest()->get();
            return view('admin.faq.index', compact('faqs', 'menus'));
        } catch (Exception $e) {
            Log::error('Error fetching FAQs details: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load FAQs list.');
        }
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $sectors = SectorDetail::where('status', 1)->get();
        return view('admin.faq.form', compact('menus','sectors'));
    }

    public function store(Request $request)
{
    try {
                 $request->validate([
        'faqs' => 'required|array',
        'faqs.*.enquestion' => 'sometimes|nullable|string',
        'faqs.*.malquestion' => 'required|string',
        'faqs.*.enanswer' => 'sometimes|nullable|string',
        'faqs.*.malanswer' => 'required|string',
    ]);

    foreach ($request->faqs as $faqData) {
        Faq::create([
            'enquestion'  => $faqData['enquestion'],
            'malquestion' => $faqData['malquestion'],
            'enanswer'    => $faqData['enanswer'],
            'malanswer'   => $faqData['malanswer'],
            'user_id'     => 1,
        ]);
    }

   


        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQs added successfully.');
    } catch (Exception $e) {
        Log::error('Error adding FAQs: ' . $e->getMessage());
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Failed to add FAQs. Please try again.');
    }
}


    public function edit($id)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $sectors = SectorDetail::where('status', 1)->get();

        try {
            $faq = Faq::findOrFail($id);
            return view('admin.faq.form', compact('faq','menus','sectors'));
        } catch (Exception $e) {
            Log::error('Error fetching FAQs for edit: ' . $e->getMessage());
            return redirect()
                ->route('admin.faqs.index')
                ->with('error', 'FAQs not found.');
        }
    }

    public function update(Request $request, $id)
    {
       try {
                $validated = $request->validate([
                    'sector_id'         => 'required|integer',
                    'counter_number'    => 'required|integer',
                    'entitle'           => 'required|string|max:255',
                    'maltitle'          => 'required|string|max:255',
                    'numeric_type_icon' => 'nullable|string|max:255',
                    'icon'              => 'nullable|string|max:255',   // FA icon class
                    'icon_file'         => 'nullable|file|mimes:png,jpg,jpeg,svg,gif|max:2048',
                    'status'            => 'boolean',
                ]);

                $counter = Faq::findOrFail($id);

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
                    ->route('admin.faqs.index')
                    ->with('success', 'FAQ updated successfully.');

            } catch (Exception $e) {

                Log::error('Error updating FAQ: ' . $e->getMessage());

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Failed to update FAQ.');
            }

    }

    public function destroy($id)
    {
        try {
            $sector_detail = Faq::findOrFail($id);
            $sector_detail->delete();

            return redirect()
                ->route('admin.faqs.index')
                ->with('success', 'FAQs deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting FAQs: ' . $e->getMessage());
            return redirect()
                ->route('admin.faq.index')
                ->with('error', 'Failed to delete FAQs.');
        }
    }
}
