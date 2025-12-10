<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Matina Pangi Information and Calamity System</title>
    
    @vite(['resources/css/app-simple.css', 'resources/js/app.js'])
    
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gradient-primary-soft dark:bg-gray-900">
    
    <!-- Floating Background Shapes -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-primary-400/10 rounded-full blur-3xl float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-secondary-400/10 rounded-full blur-3xl float" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-primary-300/10 rounded-full blur-3xl float" style="animation-delay: 4s;"></div>
    </div>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center px-4 py-20">
        <div class="max-w-6xl mx-auto text-center">
            <!-- Logo -->
            <div class="mb-8 animate-fade-in">
                <img src="{{ asset('logo.png') }}" alt="Barangay Matina Pangi Logo" class="w-32 h-32 mx-auto rounded-full shadow-2xl glow">
            </div>

            <!-- Title -->
            <h1 class="text-5xl md:text-7xl font-poppins font-bold mb-6 animate-fade-in" style="animation-delay: 0.2s;">
                <span class="text-gradient">Barangay Matina Pangi</span>
            </h1>
            
            <p class="text-2xl md:text-3xl font-poppins text-gray-700 dark:text-gray-300 mb-4 animate-fade-in" style="animation-delay: 0.4s;">
                Information and Calamity System
            </p>

            <!-- Slogan -->
            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mb-12 max-w-2xl mx-auto animate-fade-in" style="animation-delay: 0.6s;">
                "Building a connected community — one record at a time."
            </p>

            <!-- Login Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center animate-fade-in" style="animation-delay: 0.8s;">
                <a href="{{ route('login') }}" class="btn-primary w-full sm:w-auto flex items-center justify-center gap-3">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                    <span>Login as Secretary</span>
                </a>
                <a href="{{ route('login') }}" class="btn-secondary w-full sm:w-auto flex items-center justify-center gap-3">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Login as Staff</span>
                </a>
            </div>

            <!-- Features -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-20 animate-fade-in" style="animation-delay: 1s;">
                <!-- Feature 1 -->
                <div class="card hover:scale-105">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-primary rounded-2xl flex items-center justify-center">
                        <i data-lucide="users" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-poppins font-semibold mb-2 text-gray-900 dark:text-white">
                        Resident Management
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Comprehensive resident profiling with PWD, 4Ps, Senior, and Teen tracking
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="card hover:scale-105">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-primary rounded-2xl flex items-center justify-center">
                        <i data-lucide="home" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-poppins font-semibold mb-2 text-gray-900 dark:text-white">
                        Household Records
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Track extended families, co-heads, and shared household attributes
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="card hover:scale-105">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-primary rounded-2xl flex items-center justify-center">
                        <i data-lucide="bar-chart-3" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-xl font-poppins font-semibold mb-2 text-gray-900 dark:text-white">
                        Census & Analytics
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Real-time population data, demographics, and program distribution
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="relative py-20 px-4 bg-white dark:bg-gray-800">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-16">
                <h2 class="text-4xl font-poppins font-bold mb-4 text-gray-900 dark:text-white">
                    About the System
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                    A comprehensive digital platform designed to streamline resident management, household profiling, and census operations for Barangay Matina Pangi.
                </p>
            </div>
            
            <!-- Key Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                <!-- Feature 1 -->
                <div class="flex items-start gap-4 p-6 bg-gradient-to-br from-primary-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-2xl border border-primary-100 dark:border-gray-600">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-primary rounded-xl flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Secure & Reliable</h3>
                        <p class="text-gray-600 dark:text-gray-400">Role-based access control ensures data privacy and security for all resident records</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="flex items-start gap-4 p-6 bg-gradient-to-br from-primary-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-2xl border border-primary-100 dark:border-gray-600">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-primary rounded-xl flex items-center justify-center">
                        <i data-lucide="zap" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Fast & Efficient</h3>
                        <p class="text-gray-600 dark:text-gray-400">Real-time updates and instant report generation save time and improve productivity</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="flex items-start gap-4 p-6 bg-gradient-to-br from-primary-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-2xl border border-primary-100 dark:border-gray-600">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-primary rounded-xl flex items-center justify-center">
                        <i data-lucide="calendar-check" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">24/7 Access</h3>
                        <p class="text-gray-600 dark:text-gray-400">Access records anytime, anywhere with cloud-based infrastructure</p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="flex items-start gap-4 p-6 bg-gradient-to-br from-primary-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-2xl border border-primary-100 dark:border-gray-600">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-primary rounded-xl flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Calamity Management</h3>
                        <p class="text-gray-600 dark:text-gray-400">Integrated disaster response system for emergency preparedness and relief operations</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative py-12 px-4 bg-gray-900 text-white">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-poppins font-semibold mb-4">Contact Us</h3>
                    <div class="space-y-2 text-gray-400">
                        <p class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                            <span>Matina Pangi, Davao City</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            <span>(082) 123-4567</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                            <span>info@matinapangi.gov.ph</span>
                        </p>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-poppins font-semibold mb-4">Quick Links</h3>
                    <div class="space-y-2 text-gray-400">
                        <a href="#" class="block hover:text-primary-400 transition-colors">About Barangay</a>
                        <a href="#" class="block hover:text-primary-400 transition-colors">Services</a>
                        <a href="#" class="block hover:text-primary-400 transition-colors">Programs</a>
                        <a href="#" class="block hover:text-primary-400 transition-colors">Announcements</a>
                    </div>
                </div>

                <!-- Office Hours -->
                <div>
                    <h3 class="text-lg font-poppins font-semibold mb-4">Office Hours</h3>
                    <div class="space-y-2 text-gray-400">
                        <p>Monday - Friday</p>
                        <p class="font-semibold text-white">8:00 AM - 5:00 PM</p>
                        <p class="mt-4">Saturday</p>
                        <p class="font-semibold text-white">8:00 AM - 12:00 PM</p>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>© {{ date('Y') }} Barangay Matina Pangi. All rights reserved.</p>
                <p class="mt-2">Built with ❤️ for the community</p>
            </div>
        </div>
    </footer>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
