<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Demo School Fee Management')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-start: #667eea;
            --primary-end: #764ba2;
        }
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-start) 0%, var(--primary-end) 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }
        .sidebar-brand h5 { color: #fff; font-weight: 700; margin: 0; font-size: 1rem; }
        .sidebar-brand p { color: rgba(255,255,255,0.7); font-size: 0.78rem; margin: 0; }
        .sidebar-nav { padding: 0.75rem 0; }
        .nav-section-label {
            color: rgba(255,255,255,0.5);
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.75rem 1.25rem 0.25rem;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.6rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.875rem;
            border-radius: 0;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.15);
        }
        .sidebar .nav-link i { font-size: 1rem; width: 1.2rem; text-align: center; }
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: #fff;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar .btn-menu { display: none; }
        .page-content { padding: 1.5rem; flex: 1; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 0.75rem; }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; border-radius: 0.75rem 0.75rem 0 0 !important; font-weight: 600; }
        .btn-primary { background: linear-gradient(135deg, var(--primary-start), var(--primary-end)); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, #5a6fd6, #6a3f96); }
        .badge-new { background-color: #17a2b8; }
        .stat-card { background: linear-gradient(135deg, var(--primary-start), var(--primary-end)); color: white; }
        .stat-card .stat-value { font-size: 1.8rem; font-weight: 700; }
        .stat-card .stat-label { font-size: 0.8rem; opacity: 0.85; }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.show { display: block; }
            .main-content { margin-left: 0; }
            .topbar .btn-menu { display: inline-flex; }
        }
        .table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; color: #6c757d; font-weight: 600; }
        .page-title { font-size: 1.25rem; font-weight: 700; color: #2d3748; }
        .alert { border-radius: 0.5rem; }
    </style>
    @stack('styles')
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h5><i class="bi bi-mortarboard-fill me-2"></i>Demo School</h5>
        <p>Management System</p>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-section-label">Students</div>
        <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Students
        </a>
        <a href="{{ route('fees.index') }}" class="nav-link {{ request()->routeIs('fees.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Fee Collection
        </a>
        <a href="{{ route('inquiries.index') }}" class="nav-link {{ request()->routeIs('inquiries.*') ? 'active' : '' }}">
            <i class="bi bi-person-plus-fill"></i> Inquiries
        </a>

        <div class="nav-section-label">Academic</div>
        <a href="{{ route('timetable.index') }}" class="nav-link {{ request()->routeIs('timetable.*') ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i> Timetable
        </a>
        <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check-fill"></i> Attendance
        </a>

        @if(auth()->user()->isAdmin())
        <div class="nav-section-label">Finance</div>
        <a href="{{ route('teachers.index') }}" class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge-fill"></i> Teachers
        </a>
        <a href="{{ route('salaries.index') }}" class="nav-link {{ request()->routeIs('salaries.*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i> Salaries
        </a>
        <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
            <i class="bi bi-receipt-cutoff"></i> Expenses
        </a>
        <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line-fill"></i> Accounts
        </a>
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph-fill"></i> Reports
        </a>
        @endif
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary btn-menu" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <span class="page-title">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('d M Y') }}
            </span>
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:30px;height:30px;font-size:0.75rem;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text small text-muted">{{ ucfirst(auth()->user()->role) }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
</script>
@stack('scripts')
</body>
</html>
