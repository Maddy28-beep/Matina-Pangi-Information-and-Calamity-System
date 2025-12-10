<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Barangay Matina Pangi Information System')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-green: #3AB795;
            --secondary-green: #7AE582;
            --light-green: #e8f5f1;
            --dark-green: #2d9575;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f9f7 0%, #e8f5f1 50%, #f0f9f6 100%);
            background-attachment: fixed;
        }
        
        /* Top Navbar with Gradient */
        .navbar-top {
            background: linear-gradient(90deg, var(--primary-green), var(--secondary-green));
            box-shadow: 0 2px 8px rgba(58, 183, 149, 0.2);
            padding: 0.75rem 0;
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-weight: 600;
            color: white !important;
            font-size: 1.1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-right: 10px;
        }
        
        /* Sidebar with Glass Effect */
        .sidebar {
            position: fixed;
            top: 72px;
            left: 0;
            bottom: 0;
            width: 260px;
            background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(255,255,255,0.98));
            backdrop-filter: blur(10px);
            box-shadow: 2px 0 12px rgba(0,0,0,0.08);
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
            animation: slideInLeft 0.4s ease-out;
        }
        
        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .sidebar .nav-link {
            color: #495057;
            padding: 12px 20px;
            border-radius: 10px;
            margin: 4px 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--light-green);
            color: var(--primary-green);
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(58, 183, 149, 0.15);
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(90deg, var(--primary-green), var(--secondary-green));
            color: white;
            box-shadow: 0 4px 12px rgba(58, 183, 149, 0.3);
            border-left: 4px solid var(--secondary-green);
        }
        
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--secondary-green);
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 10px rgba(122, 229, 130, 0.5);
        }
        
        .sidebar .nav-link i {
            font-size: 1.1rem;
            width: 20px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            margin-top: 72px;
            padding: 24px;
            min-height: calc(100vh - 72px);
        }
        
        /* Cards with Hover Animation */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 8px 20px rgba(58, 183, 149, 0.15);
        }
        
        .card-header {
            background: white;
            border-bottom: 2px solid #f0f0f0;
            border-radius: 12px 12px 0 0 !important;
            padding: 16px 20px;
            font-weight: 600;
        }
        
        /* Stat Cards with Glow Effect */
        .stat-card {
            border-left: 4px solid var(--primary-green);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent, rgba(58, 183, 149, 0.05));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 12px 24px rgba(58, 183, 149, 0.2);
            border-left-color: var(--secondary-green);
        }
        
        .stat-card:hover::before {
            opacity: 1;
        }
        
        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--light-green), rgba(122, 229, 130, 0.2));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-green);
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover .stat-icon {
            transform: rotate(5deg) scale(1.1);
            box-shadow: 0 4px 12px rgba(58, 183, 149, 0.3);
        }
        
        /* Buttons with Animation */
        .btn-success {
            background: linear-gradient(90deg, var(--primary-green), var(--secondary-green));
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(58, 183, 149, 0.2);
        }
        
        .btn-success:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 16px rgba(58, 183, 149, 0.3);
        }
        
        .btn-outline-success {
            color: var(--primary-green);
            border-color: var(--primary-green);
            transition: all 0.3s ease;
        }
        
        .btn-outline-success:hover {
            background: linear-gradient(90deg, var(--primary-green), var(--secondary-green));
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(58, 183, 149, 0.25);
        }
        
        /* Badges */
        .badge-active {
            background-color: #28a745;
        }
        
        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge-deceased {
            background-color: #dc3545;
        }
        
        .badge-reallocated {
            background-color: #0dcaf0;
        }
        
        .badge-pwd {
            background-color: #6f42c1;
        }
        
        .badge-senior {
            background-color: #20c997;
        }
        
        .badge-teen {
            background-color: #0d6efd;
        }
        
        .badge-4ps {
            background-color: #fd7e14;
        }
        
        /* Tables */
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }
        
        .table-hover tbody tr:hover {
            background-color: var(--light-green);
        }
        
        /* Mobile Responsive */
        @media (max-width: 991px) {
            .sidebar {
                left: -260px;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        /* User Dropdown with Avatar */
        .user-dropdown {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            padding: 6px 16px 6px 6px;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-dropdown:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: white;
            color: var(--primary-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Search Bar Glow */
        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.25rem rgba(58, 183, 149, 0.25), 0 0 20px rgba(58, 183, 149, 0.3);
            transform: scale(1.02);
            transition: all 0.3s ease;
        }
        
        /* Modal Slide-in Animation */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: translateY(-50px);
        }
        
        .modal.show .modal-dialog {
            transform: translateY(0);
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-top navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <button class="btn btn-link d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                <i class="bi bi-list fs-4"></i>
            </button>
            
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('logo.png') }}" alt="Logo" onerror="this.style.display='none'">
                <span>Barangay Matina Pangi Information System</span>
            </a>
            
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark">{{ now()->format('M d, Y') }}</span>
                
                <div class="dropdown">
                    <button class="btn user-dropdown dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span>{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text"><strong>{{ ucfirst(auth()->user()->role) }}</strong></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar offcanvas-lg offcanvas-start" id="sidebar" tabindex="-1">
        <div class="offcanvas-header d-lg-none">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
        </div>
        
        <div class="offcanvas-body p-0">
            <nav class="nav flex-column py-3">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('residents.index') }}" class="nav-link {{ request()->routeIs('residents.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Residents</span>
                </a>
                
                <a href="{{ route('households.index') }}" class="nav-link {{ request()->routeIs('households.*') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    <span>Households</span>
                </a>
                
                <a href="#" class="nav-link">
                    <i class="bi bi-bar-chart"></i>
                    <span>Census</span>
                </a>
                
                <a href="#" class="nav-link">
                    <i class="bi bi-heart"></i>
                    <span>Programs</span>
                </a>
                
                <a href="#" class="nav-link">
                    <i class="bi bi-archive"></i>
                    <span>Archive</span>
                </a>
                
                <a href="#" class="nav-link">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Reports</span>
                </a>
                
                @if(auth()->user()->isSecretary())
                <a href="#" class="nav-link">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
                @endif
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Alerts -->
        

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
