<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sahabat Senja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #8B7355;
            --secondary-color: #A67B5B;
            --accent-color: #D7CCC8;
            --dark-brown: #5D4037;
            --light-bg: #FAF3E0;
            --text-dark: #4E342E;
            --text-light: #8D6E63;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, #F5E8D0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .login-container {
            min-height: 100vh;
            padding: 15px;
        }
        
        .login-left {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-brown) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            min-height: 40vh;
            border-radius: 20px 20px 0 0;
        }
        
        .login-content {
            text-align: center;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .login-icon {
            font-size: clamp(4rem, 10vw, 8rem);
            margin-bottom: clamp(1rem, 3vw, 2rem);
            opacity: 0.9;
        }
        
        .login-title {
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .login-subtitle {
            font-size: clamp(0.9rem, 2.5vw, 1.1rem);
            opacity: 0.9;
            line-height: 1.4;
            padding: 0 10px;
        }
        
        .login-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(1rem, 3vw, 2rem);
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(139, 115, 85, 0.2);
            padding: clamp(1.5rem, 4vw, 3rem);
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
        }
        
        .login-logo {
            font-size: clamp(2rem, 5vw, 2.5rem);
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .login-header h1 {
            color: var(--dark-brown);
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: clamp(1.5rem, 4vw, 1.8rem);
        }
        
        .login-header p {
            color: var(--text-light);
            margin: 0;
            font-size: clamp(0.9rem, 2vw, 1rem);
        }
        
        .form-control {
            border: 2px solid var(--accent-color);
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 1rem;
            font-size: clamp(0.9rem, 2vw, 1rem);
            width: 100%;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(139, 115, 85, 0.25);
        }
        
        .form-label {
            font-size: clamp(0.9rem, 2vw, 1rem);
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 1rem;
            transition: all 0.3s;
            font-size: clamp(0.9rem, 2vw, 1rem);
            cursor: pointer;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 115, 85, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-google {
            background: white;
            border: 2px solid var(--accent-color);
            color: var(--text-dark);
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 1rem;
            transition: all 0.3s;
            font-size: clamp(0.9rem, 2vw, 1rem);
            cursor: pointer;
        }
        
        .btn-google:hover {
            background: var(--accent-color);
            border-color: var(--primary-color);
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }
        
        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: clamp(0.85rem, 2vw, 0.95rem);
        }
        
        .forgot-password a:hover {
            color: var(--dark-brown);
            text-decoration: underline;
        }
        
        .alert-danger {
            background-color: rgba(211, 47, 47, 0.1);
            border-color: #d32f2f;
            color: #c62828;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: clamp(0.85rem, 2vw, 0.95rem);
        }
        
        /* Media Queries untuk berbagai ukuran layar */
        @media (min-width: 769px) {
            .login-left {
                min-height: 100vh;
                border-radius: 0;
            }
            
            .login-container {
                padding: 0;
            }
            
            .login-content {
                padding: 40px;
            }
        }
        
        @media (max-width: 768px) {
            .login-left {
                border-radius: 20px 20px 0 0;
                margin-bottom: 20px;
            }
            
            .login-content {
                padding: 10px;
            }
            
            .login-card {
                box-shadow: 0 5px 20px rgba(139, 115, 85, 0.15);
            }
        }
        
        @media (max-width: 576px) {
            .login-container {
                padding: 10px;
            }
            
            .login-left {
                padding: 1.5rem;
                min-height: 35vh;
            }
            
            .login-right {
                padding: 1rem 0.5rem;
            }
            
            .login-card {
                padding: 1.5rem;
            }
            
            .btn-login, .btn-google {
                padding: 0.65rem;
            }
        }
        
        @media (max-width: 375px) {
            .login-left {
                padding: 1rem;
                min-height: 30vh;
            }
            
            .login-card {
                padding: 1.2rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
            
            .login-subtitle {
                font-size: 0.85rem;
            }
        }
        
        /* Landscape orientation */
        @media (max-height: 600px) and (orientation: landscape) {
            .login-container {
                min-height: auto;
                padding: 20px;
            }
            
            .login-left {
                min-height: auto;
                padding: 1rem;
            }
            
            .login-icon {
                font-size: 3rem;
                margin-bottom: 0.5rem;
            }
            
            .login-title {
                font-size: 1.3rem;
                margin-bottom: 0.5rem;
            }
            
            .login-subtitle {
                font-size: 0.8rem;
            }
            
            .login-card {
                padding: 1.5rem;
            }
        }
        
        /* High DPI screens */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .btn-login, .btn-google {
                border-width: 1.5px;
            }
        }
        
        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .btn-login:hover, .btn-google:hover {
                transform: none;
            }
            
            .btn-login:active, .btn-google:active {
                transform: scale(0.98);
            }
            
            .form-control {
                font-size: 16px; /* Mencegah zoom di iOS */
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid login-container">
        <div class="row g-0 flex-column flex-lg-row">
            <!-- Bagian Kiri dengan Gambar/Ikon -->
            <div class="col-lg-6 login-left">
                <div class="login-content">
                    <i class="fas fa-heartbeat login-icon"></i>
                    <h1 class="login-title">Sahabat Senja</h1>
                    <p class="login-subtitle">Sistem Informasi Layanan Panti Jompo Berbasis Website & Mobile</p>
                </div>
            </div>
            
            <!-- Bagian Kanan dengan Form Login -->
            <div class="col-lg-6 login-right">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-heartbeat login-logo"></i>
                        <h1>Selamat Datang</h1>
                        <p>Silakan masuk ke akun Anda</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Login gagal!</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ old('email') }}" required autofocus
                                placeholder="Masukkan email Anda">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <input type="password" name="password" id="password" class="form-control"
                                required placeholder="Masukkan kata sandi">
                        </div>

                        <button type="submit" class="btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk
                        </button>
                    </form>

                    <button type="button" class="btn-google">
                        <i class="fab fa-google me-2"></i>Masuk dengan Google
                    </button>

                    <div class="forgot-password">
                        <a href="#">Lupa kata sandi?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk mencegah zoom pada input di iOS
        document.addEventListener('DOMContentLoaded', function() {
            let viewport = document.querySelector("meta[name=viewport]");
            if (viewport) {
                function preventZoom() {
                    viewport.content = "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no";
                }
                
                function allowZoom() {
                    viewport.content = "width=device-width, initial-scale=1.0";
                }
                
                const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
                inputs.forEach(input => {
                    input.addEventListener('focus', preventZoom);
                    input.addEventListener('blur', allowZoom);
                });
            }
            
            // Optimasi untuk touch devices
            if ('ontouchstart' in window) {
                document.body.classList.add('touch-device');
                
                // Menambahkan feedback visual untuk tombol
                const buttons = document.querySelectorAll('.btn-login, .btn-google');
                buttons.forEach(button => {
                    button.addEventListener('touchstart', function() {
                        this.style.opacity = '0.8';
                    });
                    
                    button.addEventListener('touchend', function() {
                        this.style.opacity = '1';
                    });
                });
            }
        });
    </script>
</body>
</html>