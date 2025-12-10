<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Barangay Information System of Matina Pangi: A Focus on Health and Assistance Services with Resident Profiling and Monitoring</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }
        
        body {
            background: radial-gradient(1200px 600px at 10% -10%, rgba(58,183,149,0.35) 0%, rgba(122,229,130,0.2) 25%, rgba(255,255,255,0.6) 60%),
                        radial-gradient(800px 400px at 90% 10%, rgba(58,183,149,0.25) 0%, rgba(122,229,130,0.15) 35%, rgba(255,255,255,0.6) 70%),
                        linear-gradient(135deg, #3AB795, #7AE582);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        .login-card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            overflow: hidden;
            max-width: 460px;
            width: 100%;
            margin: auto;
            border: 1px solid rgba(255,255,255,0.6);
        }
        
        .login-header {
            background: linear-gradient(135deg, #3AB795, #7AE582);
            color: white;
            padding: 28px;
            text-align: center;
        }
        
        .login-logo {
            width: 96px;
            height: 96px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            padding: 4px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        }
        
        .login-logo img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .login-body {
            padding: 24px;
            background: rgba(255,255,255,0.9);
        }
        
        .btn-login {
            background: linear-gradient(90deg, #3AB795, #7AE582);
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.05rem;
            box-shadow: 0 8px 20px rgba(58,183,149,0.3);
        }
        
        .btn-login:hover {
            background: linear-gradient(90deg, #2d9975, #6bc76b);
            color: white;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 45px 12px 16px;
            border: 2px solid #e9ecef;
            font-size: 1rem;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
        }
        
        .input-wrapper input[type="email"] {
            padding-right: 16px;
        }
        
        .form-control:focus {
            border-color: #3AB795;
            box-shadow: 0 0 0 0.2rem rgba(58, 183, 149, 0.25);
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.25rem;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .back-link {
            color: #6c757d;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .back-link:hover {
            color: #3AB795;
        }
        
        /* Simple password toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 5px;
        }
        
        .password-toggle:hover {
            color: #3AB795;
        }
        
        .input-group {
            margin-bottom: 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        
        .input-wrapper {
            position: relative;
            width: 100%;
        }
        
        .form-control {
            width: 100%;
        }
        
        .form-label {
            text-align: left;
            width: 100%;
        }
        
        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .password-toggle:hover {
            color: var(--primary-green);
            transform: translateY(-50%) scale(1.1);
        }
        
        /* Loading Animation */
        .btn-login.loading {
            pointer-events: none;
            position: relative;
            color: transparent;
        }
        
        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* System Status */
        .system-status {
            position: absolute;
            top: 24px;
            right: 24px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 14px;
            padding: 10px 14px;
            color: white;
            font-size: 0.85rem;
            z-index: 10;
        }
        
        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #28a745;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="system-status"><span class="status-indicator"></span> System Online</div>
        <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <img src="{{ asset('logo.png') }}?v={{ time() }}" alt="Barangay Logo">
            </div>
            <h4 class="mb-0 fw-bold">System Login</h4>
            <p class="small mb-0 mt-2 opacity-90">Barangay Matina Pangi Information System</p>
        </div>
        
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    {{ $errors->first() }}
                </div>
            @endif
            
            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf
                
                <div class="input-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-2"></i>Email Address
                    </label>
                    <div class="input-wrapper">
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email') }}" required autofocus placeholder="Enter your email address">
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-2"></i>Password
                    </label>
                    <div class="input-wrapper">
                        <input type="password" class="form-control" id="password" name="password" 
                               required placeholder="Enter your password">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        <i class="bi bi-check-circle me-1"></i> Remember me for 30 days
                    </label>
                </div>
                
                <button type="submit" class="btn btn-login" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Sign In to Dashboard
                </button>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Secure login protected by SSL encryption
                    </small>
                </div>
            </form>
            
            <div class="mt-4 text-center">
                <a href="{{ route('landing') }}" class="back-link small">
                    <i class="bi bi-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
    
        <!-- Footer -->
        <div class="text-center mt-4" style="position: relative; z-index: 2;">
            <p class="text-muted small mb-0">
                © {{ date('Y') }} Barangay Matina Pangi. All rights reserved.
            </p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Simple Password Toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        }
        // Optional loading feedback
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        loginForm.addEventListener('submit', function(){
            loginBtn.classList.add('loading');
        });
    </script>
</body>
</html>
