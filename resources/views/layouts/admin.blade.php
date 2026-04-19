<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị') — Shop TTM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --admin-sidebar: #0b1220;
            --admin-sidebar-hover: rgba(249, 115, 22, 0.12);
            --admin-accent: #f97316;
            --admin-accent-soft: rgba(249, 115, 22, 0.15);
            --admin-surface: #f4f6f9;
            --admin-card: #ffffff;
            --admin-border: rgba(15, 23, 42, 0.08);
            --admin-text: #0f172a;
            --admin-muted: #64748b;
        }

        body.admin-body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: var(--admin-surface);
            color: var(--admin-text);
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 268px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
        }

        .admin-brand {
            padding: 1.35rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .admin-brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            box-shadow: 0 8px 24px rgba(249, 115, 22, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            color: #0b1220;
        }

        .admin-nav {
            padding: 1rem 0.75rem;
            flex: 1;
            overflow-y: auto;
        }

        .admin-nav-label {
            font-size: 0.65rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(148, 163, 184, 0.85);
            padding: 0.5rem 0.75rem 0.35rem;
            font-weight: 600;
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 0.85rem;
            border-radius: 10px;
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.925rem;
            transition: background 0.15s ease, color 0.15s ease;
            margin-bottom: 2px;
        }

        .admin-nav-link:hover {
            background: var(--admin-sidebar-hover);
            color: #fff;
        }

        .admin-nav-link.active {
            background: var(--admin-accent-soft);
            color: #fdba74;
        }

        .admin-nav-link.active i {
            color: var(--admin-accent);
        }

        .admin-nav-link.disabled {
            opacity: 0.45;
            pointer-events: none;
        }

        .admin-main-wrap {
            margin-left: 268px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-topbar {
            background: var(--admin-card);
            border-bottom: 1px solid var(--admin-border);
            padding: 0.85rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 1px 0 rgba(15, 23, 42, 0.04);
        }

        .admin-content {
            padding: 1.5rem;
            flex: 1;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-main-wrap {
                margin-left: 0;
            }

            .admin-sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                z-index: 1035;
            }

            .admin-sidebar-backdrop.show {
                display: block;
            }
        }

        .stat-card {
            border: 1px solid var(--admin-border);
            border-radius: 14px;
            background: var(--admin-card);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }
    </style>
    @stack('styles')
</head>
<body class="admin-body">

<div class="admin-sidebar-backdrop d-lg-none" id="adminSidebarBackdrop" aria-hidden="true"></div>

<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-brand">
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none d-flex align-items-center gap-3">
            <div class="admin-brand-mark">TTM</div>
            <div>
                <div class="text-white fw-bold lh-sm" style="letter-spacing: -0.02em;">Shop TTM</div>
                <div class="text-secondary small" style="font-size: 0.7rem; letter-spacing: 0.08em;">ADMIN</div>
            </div>
        </a>
    </div>
    <nav class="admin-nav" aria-label="Menu quản trị">
        <div class="admin-nav-label">Tổng quan</div>
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            Bảng điều khiển
        </a>

        <div class="admin-nav-label mt-3">Quản lý cửa hàng</div>
        @if(Route::has('admin.categories.index'))
            <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i>
                Danh mục
            </a>
        @else
            <span class="admin-nav-link disabled" title="Thêm route trong web.php">
                <i class="bi bi-folder2-open"></i>
                Danh mục
            </span>
        @endif
        @if(Route::has('admin.products.index'))
            <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                Sản phẩm
            </a>
        @else
            <span class="admin-nav-link disabled"><i class="bi bi-box-seam"></i> Sản phẩm</span>
        @endif
        @if(Route::has('admin.orders.index'))
            <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                Đơn hàng
            </a>
        @else
            <span class="admin-nav-link disabled"><i class="bi bi-receipt"></i> Đơn hàng</span>
        @endif

        <div class="admin-nav-label mt-3">Nội dung & người dùng</div>
        @if(Route::has('admin.profile.edit'))
            <a href="{{ route('admin.profile.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                Thông tin cá nhân
            </a>
        @endif
        @if(Route::has('admin.posts.index'))
            <a href="{{ route('admin.posts.index') }}" class="admin-nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i>
                Bài viết
            </a>
        @else
            <span class="admin-nav-link disabled"><i class="bi bi-newspaper"></i> Bài viết</span>
        @endif
        {{-- @if(Route::has('admin.contacts.index'))
            <a href="{{ route('admin.contacts.index') }}" class="admin-nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                <i class="bi bi-envelope"></i>
                Liên hệ
            </a>
        @else
            <span class="admin-nav-link disabled"><i class="bi bi-envelope"></i> Liên hệ</span>
        @endif --}}
        @if(Route::has('admin.users.index'))
            <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                Tài khoản người dùng
            </a>
        @else
            <span class="admin-nav-link disabled"><i class="bi bi-people"></i> Tài khoản người dùng</span>
        @endif
    </nav>
    <div class="p-3 mt-auto border-top border-secondary border-opacity-25">
        <a href="{{ route('home') }}" class="admin-nav-link py-2 mb-0">
            <i class="bi bi-house-door"></i>
            Về cửa hàng
        </a>
    </div>
</aside>

<div class="admin-main-wrap">
    <header class="admin-topbar d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2 min-w-0">
            <button type="button" class="btn btn-light border d-lg-none" id="adminSidebarToggle" aria-label="Mở menu">
                <i class="bi bi-list"></i>
            </button>
            <div class="min-w-0">
                <h1 class="h5 mb-0 fw-semibold text-truncate">@yield('page_title', 'Bảng điều khiển')</h1>
                @hasSection('breadcrumb')
                    <nav class="small text-muted mt-1" aria-label="breadcrumb">@yield('breadcrumb')</nav>
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <span class="small text-muted d-none d-sm-inline text-end">
                {{ Auth::user()->name ?? 'Admin' }}
            </span>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
                </button>
            </form>
        </div>
    </header>

    <main class="admin-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        var sidebar = document.getElementById('adminSidebar');
        var backdrop = document.getElementById('adminSidebarBackdrop');
        var toggle = document.getElementById('adminSidebarToggle');
        function close() {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        }
        if (toggle && sidebar && backdrop) {
            toggle.addEventListener('click', function () {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            });
            backdrop.addEventListener('click', close);
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 992) close();
            });
        }
    })();
</script>
@stack('scripts')
</body>
</html>
