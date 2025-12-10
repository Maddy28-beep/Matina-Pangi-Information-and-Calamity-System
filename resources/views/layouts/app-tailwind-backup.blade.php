<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Barangay Matina Pangi Information System')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app-simple.css', 'resources/js/app.js'])
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @stack('styles')
</head>
<body class="bg-gradient-primary-soft dark:bg-gray-900 transition-colors duration-300">
    
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-72 h-screen transition-transform -translate-x-full lg:translate-x-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-r border-gray-200 dark:border-gray-700">
        <div class="h-full px-4 py-6 overflow-y-auto">
            <!-- Logo -->
            <div class="flex items-center gap-3 mb-8 px-4">
                <img src="{{ asset('logo.png') }}" alt="Barangay Logo" class="w-12 h-12 rounded-full shadow-md">
                <div>
                    <h2 class="text-lg font-poppins font-bold text-gradient">Barangay</h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Matina Pangi</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('residents.index') }}" class="{{ request()->routeIs('residents.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Residents</span>
                </a>

                <a href="{{ route('households.index') }}" class="{{ request()->routeIs('households.*') ? 'sidebar-link-active' : 'sidebar-link' }}">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    <span>Households</span>
                </a>

                <a href="#" class="sidebar-link">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    <span>Census</span>
                </a>

                <a href="#" class="sidebar-link">
                    <i data-lucide="heart-handshake" class="w-5 h-5"></i>
                    <span>Programs</span>
                </a>

                <a href="#" class="sidebar-link">
                    <i data-lucide="archive" class="w-5 h-5"></i>
                    <span>Archive</span>
                </a>

                <a href="#" class="sidebar-link">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    <span>Reports</span>
                </a>

                <a href="#" class="sidebar-link">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    <span>Settings</span>
                </a>
            </nav>

            <!-- User Info -->
            <div class="mt-auto pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary-50 dark:bg-gray-700">
                    <div class="w-10 h-10 rounded-full bg-gradient-primary flex items-center justify-center text-white font-semibold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ ucfirst(auth()->user()->role) }}
                        </p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-72">
        <!-- Top Navbar -->
        <nav class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
            <div class="px-4 py-4">
                <div class="flex items-center justify-between">
                    <!-- Mobile Menu Button -->
                    <button data-drawer-target="sidebar" data-drawer-toggle="sidebar" type="button" class="lg:hidden p-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>

                    <!-- Breadcrumb -->
                    <div class="hidden lg:block">
                        @yield('breadcrumb')
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center gap-4">
                        <!-- Dark Mode Toggle -->
                        <button id="theme-toggle" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition-all duration-300">
                            <i data-lucide="moon" class="w-5 h-5 dark:hidden"></i>
                            <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
                        </button>

                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition-all duration-300">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="p-6 min-h-screen">
            <!-- Alerts -->
            

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 animate-fade-in">
                    <div class="flex items-center gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 animate-fade-in">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 mt-0.5"></i>
                        <div>
                            <p class="font-semibold mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12">
            <div class="px-6 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        © {{ date('Y') }} Barangay Matina Pangi. All rights reserved.
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Built with ❤️ for the community
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>

    @stack('scripts')
</body>
</html>
