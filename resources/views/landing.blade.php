<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Matina Pangi Information System</title>
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
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .landing-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 900px;
            width: 90%;
        }
        
        .landing-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .landing-logo {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .landing-logo i {
            font-size: 60px;
            color: var(--primary-color);
        }
        
        .landing-body {
            padding: 40px;
        }
        
        .feature-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            background-color: #f8f9fa;
        }
        
        .feature-icon {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .btn-login {
            background: var(--primary-color);
            color: white;
            padding: 15px 50px;
            font-size: 18px;
            border-radius: 50px;
            border: none;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="landing-header">
            <div class="landing-logo">
                <i class="bi bi-building"></i>
            </div>
            <h1 class="display-4 fw-bold mb-3">Barangay Matina Pangi</h1>
            <h2 class="h4 mb-0">Information System</h2>
            <p class="mt-3 mb-0">Resident Management Subsystem</p>
        </div>
        
        <div class="landing-body">
            <div class="row mb-5">
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
            
            <div class="text-center">
                <a href="{{ route('login') }}" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Login to System
                </a>
                <p class="text-muted mt-4 small">
                    <i class="bi bi-shield-check"></i> Authorized Personnel Only
                </p>
            </div>
            
            <div class="mt-5 pt-4 border-top text-center text-muted small">
                <p class="mb-0">© 2025 Barangay Matina Pangi. All rights reserved.</p>
                <p class="mb-0">Powered by Laravel Framework</p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
