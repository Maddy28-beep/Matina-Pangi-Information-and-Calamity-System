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
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f9f7 0%, #e8f5f1 50%, #f0f9f6 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .landing-container {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        
        .logo-container img {
            width: 120px;
            height: 120px;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
            margin-bottom: 2rem;
        }
        
        .title {
            color: #3AB795;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .tagline {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 3rem;
        }
        
        .login-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }
        
        .btn-login {
            background: linear-gradient(90deg, #3AB795, #7AE582);
            border: none;
            color: white;
            padding: 14px 32px;
            font-size: 1.05rem;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.3s;
            width: 100%;
            margin-bottom: 12px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(58, 183, 149, 0.3);
            color: white;
        }
        
        .footer-text {
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="landing-container">
            <!-- Logo -->
            <div class="logo-container">
                <img src="{{ asset('logo.png') }}" alt="Barangay Logo" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%22120%22%3E%3Ccircle cx=%2260%22 cy=%2260%22 r=%2250%22 fill=%22%233AB795%22/%3E%3Ctext x=%2260%22 y=%2270%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2240%22 font-weight=%22bold%22%3EMP%3C/text%3E%3C/svg%3E'">
            </div>
            
            <!-- Title -->
            <h1 class="title">Barangay Matina Pangi</h1>
            <p class="tagline">Information System</p>
            
            <!-- Login Card -->
            <div class="login-card">
                <h4 class="mb-4" style="color: #495057;">Welcome! Please login to continue</h4>
                
                <a href="{{ route('login') }}" class="btn btn-login">
                    <i class="bi bi-person-check-fill me-2"></i>
                    Login as Secretary
                </a>
                
                <a href="{{ route('login') }}" class="btn btn-login">
                    <i class="bi bi-people-fill me-2"></i>
                    Login as Staff
                </a>
                
                <div class="footer-text">
                    <p class="mb-0">Building a connected community</p>
                    <small>© {{ date('Y') }} Barangay Matina Pangi. All rights reserved.</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
