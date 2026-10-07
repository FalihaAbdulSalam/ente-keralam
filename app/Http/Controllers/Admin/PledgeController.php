<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminMenu;
use Illuminate\Support\Facades\Auth;

class PledgeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the admin dashboard
     */
   public function index()
    {
        //dd(hii);
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $pledges = DB::table('tbl_pledge')->orderBy('pledge_id', 'desc')->get();
        return view('admin.pledge.index', compact('pledges','menus'));
    }

    public function create()
    {
$admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        return view('admin.pledge.create', compact('menus'));
    }
    
    public function store(Request $request)
{
    $request->validate([
        'pledge_title' => 'required|string|max:255',
        'pledge_description' => 'nullable|string',
        'pledge_content' => 'nullable|string',
        'pledge_startDate' => 'nullable|date',
        'pledge_endDate' => 'nullable|date',
        'pledge_score' => 'required|numeric',
        'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $bannerPath = null;
    $posterPath = null;

    if ($request->hasFile('banner')) {
        $banner = $request->file('banner');
        $encryptedName = md5(uniqid(rand(), true)) . '.' . $banner->getClientOriginalExtension();
        $banner->storeAs('uploads/pledge/banners', $encryptedName, 'public');
        $bannerPath = 'uploads/pledge/banners/' . $encryptedName;
    }

    if ($request->hasFile('poster')) {
        $poster = $request->file('poster');
        $encryptedName = md5(uniqid(rand(), true)) . '.' . $poster->getClientOriginalExtension();
        $poster->storeAs('uploads/pledge/posters', $encryptedName, 'public');
        $posterPath = 'uploads/pledge/posters/' . $encryptedName;
    }

    DB::table('tbl_pledge')->insert([
        'pledge_title' => $request->pledge_title,
        'pledge_description' => $request->pledge_description,
        'pledge_content' => $request->pledge_content,
        'pledge_startDate' => $request->pledge_startDate,
        'pledge_endDate' => $request->pledge_endDate,
        'pledge_score' => $request->pledge_score,
        'banner' => $bannerPath,
        'poster' => $posterPath,
        'pledge_status' => 1,
        'created_at' => now(),
    ]);

    return redirect()->route('admin.pledge.index')->with('success', 'Pledge added successfully.');
}

    public function edit($id)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $pledge = DB::table('tbl_pledge')->where('pledge_id', $id)->first();
        return view('admin.pledge.edit', compact('pledge','menus'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'pledge_title' => 'required|string|max:255',
        'pledge_description' => 'nullable|string',
        'pledge_content' => 'nullable|string',
        'pledge_startDate' => 'nullable|date',
        'pledge_endDate' => 'nullable|date',
        'pledge_score' => 'required|numeric',
        'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $pledge = DB::table('tbl_pledge')->where('pledge_id', $id)->first();

    $bannerPath = $pledge->banner;
    $posterPath = $pledge->poster;

    if ($request->hasFile('banner')) {
        $banner = $request->file('banner');
        $encryptedName = md5(uniqid(rand(), true)) . '.' . $banner->getClientOriginalExtension();
        $banner->storeAs('uploads/pledge/banners', $encryptedName, 'public');
        $bannerPath = 'uploads/pledge/banners/' . $encryptedName;
    }

    if ($request->hasFile('poster')) {
        $poster = $request->file('poster');
        $encryptedName = md5(uniqid(rand(), true)) . '.' . $poster->getClientOriginalExtension();
        $poster->storeAs('uploads/pledge/posters', $encryptedName, 'public');
        $posterPath = 'uploads/pledge/posters/' . $encryptedName;
    }

    DB::table('tbl_pledge')->where('pledge_id', $id)->update([
        'pledge_title' => $request->pledge_title,
        'pledge_description' => $request->pledge_description,
        'pledge_content' => $request->pledge_content,
        'pledge_startDate' => $request->pledge_startDate,
        'pledge_endDate' => $request->pledge_endDate,
        'pledge_score' => $request->pledge_score,
        'banner' => $bannerPath,
        'poster' => $posterPath,
        'updated_at' => now(),
    ]);

    return redirect('admin/pledge')->with('success', 'Pledge updated successfully.');
}

    public function toggle($id)
    {
        $pledge = DB::table('tbl_pledge')->where('pledge_id', $id)->first();
        $newStatus = $pledge->pledge_status ? 0 : 1;

        DB::table('tbl_pledge')->where('pledge_id', $id)->update(['pledge_status' => $newStatus]);
        return redirect()->back()->with('success', 'Pledge status updated.');
    }
}
