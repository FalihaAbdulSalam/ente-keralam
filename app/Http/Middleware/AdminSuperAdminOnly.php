<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSuperAdminOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin || $admin->role !== 'super_admin') {
            abort(403, 'Unauthorized: Super admin access required');
        }

        return $next($request);
    }
}
