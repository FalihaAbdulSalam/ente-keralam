<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
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
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);

        return view('admin.dashboard', [
            'admin' => $admin,
            'menus' => $menus,
        ]);
    }

        /**
     * Show menu management page
     */
    public function manageMenus()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        return view('admin.menus.index', ['menus' => $menus]);
    }

    /**
     * Show quizzes page
     */
    public function quizzes()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        return view('admin.quizzes.index', ['menus' => $menus]);
    }

    /**
     * Show tasks page
     */
    public function tasks()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        return view('admin.tasks.index', ['menus' => $menus]);
    }

    /**
     * Show polls page
     */
    public function polls()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        return view('admin.polls.index', ['menus' => $menus]);
    }

    /**
     * Show news page
     */
    public function news()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        return view('admin.news.index', ['menus' => $menus]);
    }
}
