<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Ente Keralam') }}</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('design/css/bootstrap.min.css') }}?v=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('design/css/fontawesome-all.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/it-source-2.css') }}?v=1.0.1">
    <link rel="stylesheet" href="{{ asset('design/css/style-34.css') }}?v=1.0">
    <!-- Bootstrap Icons -->
      <link rel="stylesheet" href="{{ asset('design/css/bootstrap-icons.css') }}?v=1.0">
      <link rel="stylesheet" href="{{asset('/jscss_assets/datatable.css')}}">


    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --sidebar-width: 260px;
            --header-height: 70px;
            --text-dark: #2c3e50;
            --text-light: #666;
            --bg-light: #f8f9fa;
            --border-color: #e0e6ed;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        /* Header Styles */
        .admin-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
            padding: 0 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--primary-color);
            cursor: pointer;
            transition: color 0.3s;
        }

        .toggle-sidebar:hover {
            color: var(--secondary-color);
        }

        .admin-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .admin-profile:hover {
            background-color: var(--bg-light);
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .profile-role {
            font-size: 11px;
            color: var(--text-light);
        }

        /* Sidebar Styles */
        .admin-sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            background: white;
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
            z-index: 999;
            transition: left 0.3s;
        }

        .admin-sidebar.collapsed {
            left: calc(var(--sidebar-width) * -1);
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }

        .menu-item {
            margin: 5px 0;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
        }

        .menu-link:hover {
            color: var(--primary-color);
            background-color: rgba(102, 126, 234, 0.05);
            border-left: 3px solid var(--primary-color);
            padding-left: 17px;
        }

        .menu-link.active {
            color: white;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-left: 3px solid white;
            padding-left: 17px;
        }

        .menu-icon {
            width: 24px;
            text-align: center;
            font-size: 16px;
        }

        .menu-submenu {
            list-style: none;
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s;
        }

        .menu-item.expanded .menu-submenu {
            max-height: 500px;
        }

        .submenu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px 10px 50px;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 13px;
        }

        .submenu-link:hover {
            color: var(--primary-color);
            background-color: rgba(102, 126, 234, 0.05);
        }

        .submenu-link.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Main Content Area */
        .admin-main {
            margin-top: var(--header-height);
            margin-left: var(--sidebar-width);
            padding: 30px;
            transition: margin-left 0.3s;
        }

        .admin-main.expanded {
            margin-left: 0;
        }

        .page-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .page-title {
            font-size: 28px;
            font-weight: 600;
            margin: 0;
            color: var(--text-dark);
        }

        .page-subtitle {
            font-size: 14px;
            color: var(--text-light);
            margin-top: 5px;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid var(--primary-color);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            font-size: 32px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .card-title {
            font-size: 14px;
            color: var(--text-light);
            margin: 10px 0;
            font-weight: 500;
        }

        .card-value {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Logout Button */
        .btn-logout {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 200px;
            }

            .admin-sidebar.collapsed {
                left: 0;
            }

            .admin-main.expanded {
                margin-left: var(--sidebar-width);
            }

            .admin-main {
                padding: 20px;
            }

            .header-right {
                gap: 15px;
            }

            .profile-info {
                display: none;
            }
        }

        /* Scrollbar Styling */
        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .admin-sidebar::-webkit-scrollbar-track {
            background: var(--bg-light);
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }

        .admin-sidebar::-webkit-scrollbar-thumb:hover {
            background: #999;
        }
    </style>

    {{-- Page-specific styles --}}
    @stack('styles')

    @yield('extra_css')
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="header-content">
            <div class="header-left">
                <button class="toggle-sidebar" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="admin-title">
                    <i class="fas fa-chart-line"></i> Admin Dashboard
                </h2>
            </div>

            <div class="header-right">
                <div class="admin-profile">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="profile-info">
                        <div class="profile-name">{{ Auth::guard('admin')->user()->name }}</div>
                        <div class="profile-role">{{ ucfirst(Auth::guard('admin')->user()->role) }}</div>
                    </div>
                </div>

                <a href="{{ route('admin.logout') }}" class="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
       @php
    $segments = request()->segments();
    $currentModule = $segments[0] === 'admin' ? ($segments[1] ?? '') : ($segments[0] ?? '');
@endphp

<ul class="sidebar-menu">
    @if(isset($menus) && count($menus) > 0)
        @foreach($menus as $menu)

            @php
                // ---- PARENT MENU ACTIVE CHECK (by route_name -> module) ----
                $parentModule = '';
                if (!empty($menu->route_name)) {
                    $parts = explode('.', $menu->route_name);          // admin.counter_details.index
                    $parentModule = $parts[1] ?? '';                   // counter_details
                }

                $isParentActive = ($parentModule === $currentModule);

                // ---- CHECK IF ANY CHILD MATCHES CURRENT MODULE ----
                $isChildActive = false;
                foreach ($menu->children as $submenu) {
                    $subParts = explode('.', $submenu->route_name);    // admin.articles.index
                    $subModule = $subParts[1] ?? '';                   // articles
                    if ($subModule === $currentModule) {
                        $isChildActive = true;
                        break;
                    }
                }

                $isMenuOpen = $isParentActive || $isChildActive;
            @endphp

            <li class="menu-item {{ $isMenuOpen ? 'active open' : '' }}" id="menu-{{ $menu->id }}">
                <a href="{{ route($menu->route_name) }}" 
                   class="menu-link {{ $isMenuOpen ? 'active' : '' }}"
                   @if(count($menu->children) > 0) onclick="toggleSubmenu(event, {{ $menu->id }})" @endif>

                    <span class="menu-icon">
                        <i class="{{ $menu->icon ?? 'fas fa-circle' }}"></i>
                    </span>
                    <span>{{ $menu->name }}</span>

                    @if(count($menu->children) > 0)
                        <span style="margin-left: auto; font-size: 12px;">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    @endif
                </a>
              
                @if(count($menu->children) > 0)
                    <ul class="menu-submenu" style="{{ $isMenuOpen ? 'display:block;' : '' }}">
                        @foreach($menu->children as $submenu)

                            @php
                                $subParts = explode('.', $submenu->route_name); // admin.counter_details.index
                                $subModule = $subParts[1] ?? '';               // counter_details
                                $isSubActive = ($subModule === $currentModule);
                            @endphp
                           
                            <li>
                                <a href="{{ Route::has($submenu->route_name) ? route($submenu->route_name) : '#' }}" 
                                   class="submenu-link {{ $isSubActive ? 'active' : '' }}">
                                    <span class="menu-icon">
                                        <i class="{{ $submenu->icon ?? 'fas fa-dot-circle' }}"></i>
                                    </span>
                                    <span>{{ $submenu->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    @else
        <li class="menu-item">
            <p style="padding: 20px; color: var(--text-light); text-align: center;">
                No menus available
            </p>
        </li>
    @endif
</ul>


    </aside>

    <!-- Main Content -->
    <main class="admin-main" id="adminMain">
        <!-- breadcrumps -->
                            @php
                                    $segments = request()->segments();  

                                    // Remove 'admin' from segments
                                    if (!empty($segments) && $segments[0] === 'admin') {
                                        array_shift($segments);
                                    }

                                    $breadcrumbs = [];
                                    $url = '/admin';

                                    foreach ($segments as $index => $segment) {

                                        // Skip numeric IDs
                                        if (is_numeric($segment)) {
                                            continue;
                                        }

                                        // Build URL path
                                        $url .= '/' . $segment;

                                        // Format label: articles → Articles, counter_details → Counter Details
                                        $label = ucwords(str_replace('_', ' ', $segment));

                                        $breadcrumbs[] = [
                                            'label' => $label,
                                            'url'   => url($url),
                                            'is_last' => $index === array_key_last($segments),
                                        ];
                                    }
                            @endphp


                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">

                                {{-- Dashboard --}}
                                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>

                                @foreach ($breadcrumbs as $crumb)

                                    @if($crumb['is_last'])
                                        <li class="breadcrumb-item active" aria-current="page">
                                            {{ $crumb['label'] }}
                                        </li>
                                    @else
                                        <li class="breadcrumb-item">
                                            <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                                        </li>
                                    @endif

                                @endforeach

                            </ol>
                        </nav>

         <!-- breadcrumps -->

        @yield('content')
    </main>
<script src="{{asset('/jscss_assets/jquery.js')}}"></script>
<script src="{{ asset('jscss_assets/popper.min.js') }}"></script>
<script src="{{ asset('jscss_assets/bootstrap-4.1.1.min.js') }}"></script>

<script src="{{asset('/jscss_assets/datatable.js')}}"></script>
    <script>
        let table = new DataTable('.table');
 
table.on('click', 'tbody tr', function () {
    let data = table.row(this).data();
 
    // alert('You clicked on ' + data[0] + "'s row");
});
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const main = document.getElementById('adminMain');
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
        }

        function toggleSubmenu(e, menuId) {
            if (e.target.closest('.menu-link').querySelectorAll('.menu-icon, span:not(.menu-icon)').length > 0) {
                e.preventDefault();
                const menuItem = document.getElementById('menu-' + menuId);
                menuItem.classList.toggle('expanded');
            }
        }

        // Close sidebar on small screens when a menu item is clicked
        if (window.innerWidth <= 768) {
            document.querySelectorAll('.menu-link').forEach(link => {
                link.addEventListener('click', function() {
                    if (!this.querySelectorAll('.menu-submenu').length) {
                        document.getElementById('adminSidebar').classList.add('collapsed');
                        document.getElementById('adminMain').classList.add('expanded');
                    }
                });
            });
        }
    </script>

    {{-- Page-specific scripts --}}
    @stack('scripts')

    @yield('extra_js')
</body>
</html>
