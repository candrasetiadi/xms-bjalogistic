<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BJA Invoice</title>
    <link rel="icon" type="image/png" href="{{ asset('logo-bja.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f5f7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-screen {
            width: 100%;
            max-width: 400px;
            padding: 24px;
        }
        .login-box {
            background: #fff;
            border-radius: 16px;
            padding: 40px 36px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .login-logo img { height: 52px; object-fit: contain; }
        .login-logo h2 {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 10px;
        }
        .login-logo p { font-size: 13px; color: #6b7280; margin-top: 2px; }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            color: #1a1a1a;
            outline: none;
            transition: border-color 0.15s;
        }
        .form-group input:focus { border-color: #CC0000; }
        .form-group .error {
            font-size: 12px;
            color: #CC0000;
            margin-top: 4px;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #CC0000;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            margin-top: 4px;
        }
        .btn-login:hover { background: #aa0000; }
    </style>
</head>
<body>
    <div class="login-screen">
        @yield('content')
    </div>
</body>
</html>
