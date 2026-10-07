<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BannerCategory;
use App\Models\AdminMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class BannersController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        try {
            $banners = Banner::latest()->paginate(15);
            return view('admin.banners.index', compact('banners', 'menus'));
        } catch (Exception $e) {
            Log::error('Error fetching banners: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load banners.');
        }
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $categories = BannerCategory::where('status', 1)->get();
        return view('admin.banners.form', compact('menus','categories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'entitle' => 'required|string|max:255',
                'maltitle' => 'required|string|max:255',
                'endescription' => 'nullable|string',
                'maldescription' => 'nullable|string',
                'poster' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
                'malposter' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
                'youtubelink' => 'nullable|url|max:255',
                'link' => 'nullable|url|max:255',
                'link_text' => 'nullable|string|max:255',
                'banercategory_id' => 'required|integer',
                'order_num' => 'nullable|integer',
                'status' => 'boolean',
            ]);

            $posterFile = null;
            if ($request->hasFile('poster')) {
                $file = $request->file('poster');
                $posterFile = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
                $destination = public_path('uploads/banners');
                if (!file_exists($destination)) mkdir($destination, 0755, true);
                $file->move($destination, $posterFile);
            }

            $malposterFile = null;
            if ($request->hasFile('malposter')) {
                $file = $request->file('malposter');
                $malposterFile = time() . '_mal_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
                $destination = public_path('uploads/banners');
                if (!file_exists($destination)) mkdir($destination, 0755, true);
                $file->move($destination, $malposterFile);
            }

            $data = $validated;
            $data['poster'] = 'banners/'.$posterFile;
            $data['malposter'] = $malposterFile;
            $data['banercategory_id'] = $validated['banercategory_id'] ?? 1;
            $data['status'] = $validated['status'] ?? 1;

            Banner::create($data);

            return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
        } catch (Exception $e) {
            Log::error('Error creating banner: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create banner.');
        }
    }

    public function edit($id)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $categories = BannerCategory::where('status', 1)->get();
        try {
            $banner = Banner::findOrFail($id);
            return view('admin.banners.form', compact('banner', 'menus','categories'));
        } catch (Exception $e) {
            Log::error('Error fetching banner for edit: ' . $e->getMessage());
            return redirect()->route('admin.banners.index')->with('error', 'Banner not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'entitle' => 'required|string|max:255',
                'maltitle' => 'required|string|max:255',
                'endescription' => 'nullable|string',
                'maldescription' => 'nullable|string',
                'poster' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
                'malposter' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
                'youtubelink' => 'nullable|url|max:255',
                'link' => 'nullable|url|max:255',
                'link_text' => 'nullable|string|max:255',
                'banercategory_id' => 'required|integer',
                'order_num' => 'nullable|integer',
                'status' => 'boolean',
            ]);

            $banner = Banner::findOrFail($id);

            if ($request->hasFile('poster')) {
                $file = $request->file('poster');
                $posterFile = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
                $destination = public_path('uploads/banners');
                if (!file_exists($destination)) mkdir($destination, 0755, true);
                $file->move($destination, $posterFile);
                if ($banner->poster && file_exists(public_path('uploads/banners/' . $banner->poster))) @unlink(public_path('uploads/banners/' . $banner->poster));
                $validated['poster'] = 'banners/'.$posterFile;
            }

            if ($request->hasFile('malposter')) {
                $file = $request->file('malposter');
                $malposterFile = time() . '_mal_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
                $destination = public_path('uploads/banners');
                if (!file_exists($destination)) mkdir($destination, 0755, true);
                $file->move($destination, $malposterFile);
                if ($banner->malposter && file_exists(public_path('uploads/banners/' . $banner->malposter))) @unlink(public_path('uploads/banners/' . $banner->malposter));
                $validated['malposter'] =  'banners/'.$malposterFile;
            }

            $banner->update($validated);

            return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating banner: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update banner.');
        }
    }

    public function destroy($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            if ($banner->poster && file_exists(public_path('uploads/banners/' . $banner->poster))) @unlink(public_path('uploads/banners/' . $banner->poster));
            if ($banner->malposter && file_exists(public_path('uploads/banners/' . $banner->malposter))) @unlink(public_path('uploads/banners/' . $banner->malposter));
            $banner->delete();
            return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting banner: ' . $e->getMessage());
            return redirect()->route('admin.banners.index')->with('error', 'Failed to delete banner.');
        }
    }

}
