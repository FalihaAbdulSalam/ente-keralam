<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - {{ config('app.name', 'Ente Keralam') }}</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('design/css/bootstrap.min.css') }}?v=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('design/css/fontawesome-all.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('design/css/it-source-2.css') }}?v=1.0.1">
    <link rel="stylesheet" href="{{ asset('design/css/style-34.css') }}?v=1.0">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .login-box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(90deg, #003399 0%, #0099ff 100%, #003399);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input[type="email"]:focus,
        .form-group input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .form-check label {
            margin: 0 0 0 8px;
            cursor: pointer;
            font-size: 14px;
            color: #666;
            font-weight: 400;
        }

        .error-alert {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .error-alert .alert-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .error-text {
            color: #721c24;
            font-size: 13px;
            margin-top: 5px;
        }

        .login-footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 13px;
            color: #666;
        }

        .it-nw-btn a, .it-nw-btn button {
            height: 45px;
            border-top-right-radius: 5px;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 10px;
            }

            .login-header {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 24px;
            }

            .login-body {
                padding: 30px 20px;
            }

            .login-footer {
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1><i class="fas fa-lock"></i> Admin Panel</h1>
                <p>Ente Keralam Administration</p>
            </div>

            <div class="login-body">
                @if ($errors->any())
                    <div class="error-alert">
                        <div class="alert-title">Login Failed</div>
                        @foreach ($errors->all() as $error)
                            <div class="error-text">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               placeholder="Email address" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" 
                               placeholder="Enter your password" required>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="remember" name="remember" value="on">
                        <label for="remember">Remember me</label>
                    </div>

                    <div class="it-nw-btn">
                        <button type="submit" class="btn-login">Sign In</button>
                    </div>
                </form>
            </div>

            <div class="login-footer">
                <p style="margin-bottom: 10px;">
                    <i class="fas fa-shield-alt"></i> Secure Admin Access
                </p>
                <a href="{{ url('/') }}" style="color: #667eea; text-decoration: none; font-weight: 500;">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
