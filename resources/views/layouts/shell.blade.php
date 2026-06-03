<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Life Tracker')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/layouts/shell.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    @stack('styles')
</head>

<body>
    <aside class="sidebar" id="mainSidebar">
        <div class="sidebar-inner">
            <div class="sidebar-header">
                <a href="{{ (auth()->user()->role ?? null) === 'admin' ? route('admin.dashboard') : route('overview.index') }}"
                    class="sidebar-brand text-decoration-none">
                    <i class="fa-solid fa-bolt"></i>
                    <span>Life Tracker</span>
                </a>

            </div>

            <ul class="menu-list list-unstyled mb-0 ps-0">
                @yield('sidebar_primary')
            </ul>

            <ul class="menu-list list-unstyled mb-0 ps-0 sidebar-secondary-menu">
                @yield('sidebar_secondary')
            </ul>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="main-container">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-sidebar-toggle" id="mobileSidebarToggle" aria-label="Mở menu" aria-expanded="false">
                    <i class="fa-solid fa-bars"></i>
                </button>

                @yield('topbar_left')
            </div>

            <div class="topbar-actions d-flex align-items-center gap-2">
                @yield('topbar_actions')
            </div>
        </header>

        <main class="content-body">
            @yield('content')
        </main>
    </div>

    @yield('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/layouts/shell.js') }}"></script>

    @stack('scripts')
</body>

</html>
