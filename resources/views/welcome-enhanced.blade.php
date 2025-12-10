<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Matina Pangi Information System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-green: #3AB795;
            --secondary-green: #7AE582;
            --light-green: #e8f5f1;
            --dark-green: #2d9575;
            --gradient-primary: linear-gradient(90deg, #3AB795, #7AE582);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Navbar */
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--primary-green) !important;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .navbar-brand img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        
        .nav-link {
            color: #495057 !important;
            font-weight: 500;
            margin: 0 8px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--primary-green) !important;
            transform: translateY(-2px);
        }
        
        .btn-login {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 8px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(58, 183, 149, 0.3);
            color: white;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #f5f9f7 0%, #e8f5f1 50%, #f0f9f6 100%);
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(58, 183, 149, 0.1), transparent);
            border-radius: 50%;
        }
        
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(122, 229, 130, 0.1), transparent);
            border-radius: 50%;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 1rem;
            animation: fadeInUp 0.8s ease;
        }
        
        .hero-subtitle {
            font-size: 2rem;
            color: #495057;
            margin-bottom: 1rem;
            animation: fadeInUp 0.8s ease 0.2s both;
        }
        
        .hero-tagline {
            font-size: 1.3rem;
            color: #6c757d;
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease 0.4s both;
        }
        
        .hero-description {
            font-size: 1.1rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto 2.5rem;
            line-height: 1.8;
            animation: fadeInUp 0.8s ease 0.6s both;
        }
        
        .hero-buttons {
            animation: fadeInUp 0.8s ease 0.8s both;
        }
        
        .btn-hero {
            padding: 14px 32px;
            font-size: 1.1rem;
            border-radius: 10px;
            font-weight: 600;
            margin: 0 8px;
            transition: all 0.3s ease;
        }
        
        .btn-hero-primary {
            background: var(--gradient-primary);
            color: white;
            border: none;
        }
        
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(58, 183, 149, 0.4);
            color: white;
        }
        
        .btn-hero-outline {
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            background: white;
        }
        
        .btn-hero-outline:hover {
            background: var(--primary-green);
            color: white;
            transform: translateY(-3px);
        }
        
        .hero-image {
            animation: fadeInRight 1s ease;
        }
        
        .hero-image img {
            max-width: 100%;
            filter: drop-shadow(0 10px 30px rgba(0,0,0,0.1));
        }
        
        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: white;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .section-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .feature-card {
            border: none;
            border-radius: 16px;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            background: white;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 28px rgba(58, 183, 149, 0.2);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            color: white;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: rotate(5deg) scale(1.1);
        }
        
        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .feature-text {
            color: #6c757d;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }
        
        .btn-feature {
            background: var(--light-green);
            color: var(--primary-green);
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-feature:hover {
            background: var(--primary-green);
            color: white;
            transform: translateX(5px);
        }
        
        /* Stats Section */
        .stats-section {
            background: var(--gradient-primary);
            padding: 60px 0;
            color: white;
        }
        
        .stat-item {
            text-align: center;
            padding: 2rem;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }
        
        /* News Section */
        .news-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .news-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .news-badge {
            background: var(--primary-green);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .news-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .news-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 1rem 0;
        }
        
        /* Footer */
        .footer {
            background: #2c3e50;
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .footer-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: block;
            margin-bottom: 0.8rem;
            transition: all 0.3s ease;
        }
        
        .footer-link:hover {
            color: var(--secondary-green);
            transform: translateX(5px);
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            background: var(--primary-green);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 3rem;
            padding-top: 2rem;
            text-align: center;
            color: rgba(255,255,255,0.7);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.5rem;
            }
            
            .btn-hero {
                display: block;
                width: 100%;
                margin: 8px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('logo.png') }}" alt="Logo" onerror="this.style.display='none'">
                <span>Barangay Matina Pangi</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a href="{{ route('login') }}" class="btn btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content text-center text-lg-start">
                    <h1 class="hero-title">Barangay Matina Pangi</h1>
                    <h2 class="hero-subtitle">Information System</h2>
                    <p class="hero-tagline"><i class="bi bi-star-fill text-warning"></i> Resident Management Subsystem</p>
                    <p class="hero-description">
                        A comprehensive digital platform designed to streamline resident management, household records, 
                        and census data collection for Barangay Matina Pangi. Empowering our community through efficient 
                        and transparent governance.
                    </p>
                    <div class="hero-buttons">
                        <a href="{{ route('login') }}" class="btn btn-hero btn-hero-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                        <a href="#features" class="btn btn-hero btn-hero-outline">
                            <i class="bi bi-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 hero-image text-center mt-5 mt-lg-0">
                    <img src="{{ asset('pangi.png') }}" alt="Barangay Matina Pangi" style="max-width: 500px; width: 100%;" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <h2 class="section-title">Our Features</h2>
            <p class="section-subtitle">Comprehensive tools to manage your barangay efficiently</p>
            
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="card feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="feature-title">Resident Management</h3>
                        <p class="feature-text">
                            Efficiently manage resident records with comprehensive profiles including personal information, 
                            household details, and special categories (PWD, Senior Citizens, 4Ps beneficiaries).
                        </p>
                        <a href="#" class="btn btn-feature">
                            Learn More <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="card feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <h3 class="feature-title">Household Records</h3>
                        <p class="feature-text">
                            Track household information including family composition, primary and co-heads, 
                            living arrangements, and household statistics for better community planning.
                        </p>
                        <a href="#" class="btn btn-feature">
                            Learn More <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="card feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3 class="feature-title">Census Reports</h3>
                        <p class="feature-text">
                            Generate comprehensive census reports with demographic analysis, population statistics, 
                            and visual charts for data-driven decision making and community development.
                        </p>
                        <a href="#" class="btn btn-feature">
                            Learn More <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-number">{{ \App\Models\Resident::where('approval_status', 'approved')->count() }}</div>
                        <div class="stat-label">Total Residents</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-home"></i></div>
                        <div class="stat-number">{{ \App\Models\Household::where('approval_status', 'approved')->count() }}</div>
                        <div class="stat-label">Households</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-heart"></i></div>
                        <div class="stat-number">{{ \App\Models\Resident::where('approval_status', 'approved')->where('is_senior_citizen', true)->count() }}</div>
                        <div class="stat-label">Senior Citizens</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                        <div class="stat-number">{{ \App\Models\Resident::where('approval_status', 'approved')->where('is_voter', true)->count() }}</div>
                        <div class="stat-label">Registered Voters</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News/Announcements Section -->
    <section class="news-section" id="about">
        <div class="container">
            <h2 class="section-title">Latest Updates</h2>
            <p class="section-subtitle">Stay informed with our latest news and announcements</p>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card news-card">
                        <div class="card-body">
                            <span class="news-badge">Announcement</span>
                            <p class="news-date mt-2"><i class="bi bi-calendar3 me-2"></i>{{ now()->format('F d, Y') }}</p>
                            <h3 class="news-title">System Launch</h3>
                            <p class="text-muted">
                                The Barangay Matina Pangi Information System is now live! 
                                Experience efficient resident management and streamlined services.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card news-card">
                        <div class="card-body">
                            <span class="news-badge">Update</span>
                            <p class="news-date mt-2"><i class="bi bi-calendar3 me-2"></i>{{ now()->format('F d, Y') }}</p>
                            <h3 class="news-title">Census Data Collection</h3>
                            <p class="text-muted">
                                We're conducting a comprehensive census to better serve our community. 
                                Please ensure your information is up to date.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card news-card">
                        <div class="card-body">
                            <span class="news-badge">Service</span>
                            <p class="news-date mt-2"><i class="bi bi-calendar3 me-2"></i>{{ now()->format('F d, Y') }}</p>
                            <h3 class="news-title">Online Services Available</h3>
                            <p class="text-muted">
                                Access barangay services online! Request certificates, view records, 
                                and more through our digital platform.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h3 class="footer-title">Barangay Matina Pangi</h3>
                    <p class="text-white-50">
                        Building a connected community through efficient governance and transparent services.
                    </p>
                    <div class="mt-3">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h3 class="footer-title">Quick Links</h3>
                    <a href="#home" class="footer-link">Home</a>
                    <a href="#features" class="footer-link">Features</a>
                    <a href="#about" class="footer-link">About Us</a>
                    <a href="{{ route('login') }}" class="footer-link">Login</a>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h3 class="footer-title">Contact Us</h3>
                    <p class="text-white-50">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        Matina Pangi, Davao City
                    </p>
                    <p class="text-white-50">
                        <i class="bi bi-telephone-fill me-2"></i>
                        (082) XXX-XXXX
                    </p>
                    <p class="text-white-50">
                        <i class="bi bi-envelope-fill me-2"></i>
                        info@matinapangi.gov.ph
                    </p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p class="mb-0">
                    © {{ date('Y') }} Barangay Matina Pangi. All rights reserved. | 
                    Building a connected community — one record at a time.
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Smooth Scroll -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
