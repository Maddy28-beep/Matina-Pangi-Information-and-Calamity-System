<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Matina Pangi Information and Calamity System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c5f2d;
            --secondary-color: #97bc62;
            --accent-color: #ffd700;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .landing-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 1100px;
            width: 95%;
        }

        .landing-header {
            position: relative;
            color: white;
            padding: 60px 40px;
            text-align: left;
            background-size: cover;
            background-position: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .landing-header .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
        }

        .brand img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .nav-links a {
            color: #e9f6ea;
            text-decoration: none;
            margin: 0 12px;
            font-weight: 600;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
        }

        .hero-subtitle {
            font-size: 1.25rem;
        }

        .landing-body {
            padding: 40px;
        }

        .feature-card {
            text-align: left;
            padding: 20px;
            border-radius: 14px;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #eef1ef;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06);
            background-color: #f8f9fa;
        }

        .feature-icon {
            font-size: 32px;
            color: var(--primary-color);
        }

        .btn-login {
            background: var(--primary-color);
            color: white;
            padding: 14px 36px;
            font-size: 16px;
            border-radius: 12px;
            border: none;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    @php
        $pangiCandidate = collect(['pangi', 'pangi.jpg', 'pangi.jpeg', 'pangi.png', 'pangi.svg'])
            ->first(function ($f) {
                return file_exists(public_path($f));
            });
        $pangiUrl = $pangiCandidate ? asset($pangiCandidate) : asset('logo.png');
    @endphp
    <div class="landing-container">
        <div class="landing-header"
            style="background-image: linear-gradient(rgba(44,95,45,0.85), rgba(151,188,98,0.6)), url('{{ $pangiUrl }}');">
            <div class="topbar">
                <div class="brand">
                    <img src="{{ $pangiUrl }}" alt="Barangay Matina Pangi">
                    <span>Barangay Matina Pangi</span>
                </div>
                <div class="nav-links d-none d-md-block">
                    <a href="#home">Home</a>
                    <a href="#features">Features</a>
                    <a href="#about">About</a>
                    <a href="#contact">Contact</a>
                </div>
                <a href="{{ route('login') }}" class="btn btn-light btn-sm"><i
                        class="bi bi-box-arrow-in-right me-1"></i> Login</a>
            </div>
            <h1 class="hero-title mb-2">Barangay Matina Pangi</h1>
            <p class="hero-subtitle mb-1">Information and Calamity System</p>
            <p class="mb-0">Resident Management Subsystem</p>
        </div>

        <div class="landing-body">
            <div id="features" class="row mb-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h5 class="fw-bold">Resident Management</h5>
                        <p class="text-muted small">Complete resident registration and profile management</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-house-fill"></i>
                        </div>
                        <h5 class="fw-bold">Household Records</h5>
                        <p class="text-muted small">Comprehensive household data and member tracking</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-bar-chart-fill"></i>
                        </div>
                        <h5 class="fw-bold">Census Reports</h5>
                        <p class="text-muted small">Real-time population statistics and analytics</p>
                    </div>
                </div>
            </div>

            <div id="home" class="text-center">
                <a href="{{ route('login') }}" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Login to System
                </a>
                <p class="text-muted mt-4 small">
                    <i class="bi bi-shield-check"></i> Authorized Personnel Only
                </p>
            </div>

            <div id="about" class="mt-5 pt-4 border-top text-center text-muted small">
                <p class="mb-0">© 2025 Barangay Matina Pangi. All rights reserved.</p>
                <p class="mb-0">Powered by Laravel Framework</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>