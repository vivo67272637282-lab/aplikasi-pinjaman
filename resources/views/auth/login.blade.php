<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --soft-bg: #edf2fb;
            --panel-bg: rgba(248, 250, 255, 0.7);
            --primary: #5a8ee6;
            --primary-2: #7ea8f1;
            --primary-soft: #dfeafc;
            --ink: #243248;
            --muted: #7d8ca5;
            --line: rgba(96, 126, 186, 0.3);
            --shadow: rgba(91, 129, 201, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f1f3f7 0%, #e5edf9 40%, #dfeaf8 100%);
            color: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            border-radius: 50%;
            background: rgba(136, 172, 230, 0.18);
            z-index: 0;
        }

        body::before {
            width: 700px;
            height: 700px;
            right: -180px;
            bottom: -180px;
        }

        body::after {
            width: 620px;
            height: 620px;
            left: -160px;
            top: -120px;
            background: rgba(149, 180, 228, 0.12);
        }

        .login-shell {
            position: relative;
            z-index: 1;
            width: min(1180px, 92vw);
            min-height: 700px;
            background: rgba(255, 255, 255, 0.18);
            border: 3px solid rgba(68, 122, 197, 0.8);
            border-radius: 28px;
            box-shadow: 0 12px 26px rgba(73, 114, 182, 0.08);
            overflow: hidden;
        }

        .login-shell::before,
        .login-shell::after {
            content: "";
            position: absolute;
            pointer-events: none;
            border-radius: 50%;
            background: rgba(154, 182, 236, 0.2);
        }

        .login-shell::before {
            width: 490px;
            height: 420px;
            left: 50%;
            bottom: -150px;
            transform: translateX(-50%);
        }

        .login-shell::after {
            width: 260px;
            height: 220px;
            right: -60px;
            top: -20px;
            background: rgba(168, 191, 235, 0.14);
        }

        .login-wrap {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 0.95fr 1.5fr;
            min-height: 700px;
            padding: 36px 42px 16px;
            gap: 12px;
        }

        .login-panel {
            padding: 16px 10px 12px 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .label-brand {
            display: inline-block;
            font-size: 0.9rem;
            letter-spacing: 0.18em;
            font-weight: 700;
            color: rgba(36, 50, 72, 0.8);
            text-transform: uppercase;
            margin-bottom: 44px;
        }

        .login-title {
            margin: 0;
            font-size: clamp(2.2rem, 2vw + 1rem, 3.4rem);
            font-weight: 700;
            color: var(--ink);
            line-height: 1.15;
        }

        .login-subtitle {
            margin: 10px 0 28px;
            font-size: 0.78rem;
            color: rgba(36, 50, 72, 0.7);
        }

        .login-form {
            max-width: 360px;
        }

        .field-group {
            margin-bottom: 18px;
        }

        .field-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(36, 50, 72, 0.85);
        }

        .input-shell {
            position: relative;
        }

        .input-shell .form-control {
            height: 42px;
            border: 1px solid rgba(106, 142, 196, 0.3);
            background: rgba(255, 255, 255, 0.25);
            padding-left: 14px;
            padding-right: 42px;
            border-radius: 10px;
            color: var(--ink);
            box-shadow: none;
            font-size: 0.96rem;
        }

        .input-shell .form-control:focus {
            border-color: rgba(90, 142, 230, 0.7);
            box-shadow: 0 0 0 0.18rem rgba(90, 142, 230, 0.12);
            background: rgba(255, 255, 255, 0.4);
        }

        .input-shell .form-control.is-invalid {
            border-color: rgba(220, 53, 69, 0.7);
        }

        .input-shell .icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.7;
            color: rgba(36, 50, 72, 0.7);
            font-size: 0.9rem;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: rgba(36, 50, 72, 0.7);
            padding: 0;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
        }

        .toggle-password:focus {
            outline: none;
        }

        .invalid-feedback {
            display: block;
            margin-top: 6px;
            font-size: 0.77rem;
            color: #d94d5c;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 16px 0 22px;
            color: rgba(36, 50, 72, 0.72);
            font-size: 0.75rem;
        }

        .remember-row input {
            width: 14px;
            height: 14px;
            accent-color: var(--primary);
        }

        .register-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 16px;
            padding: 11px 16px;
            border-radius: 10px;
            border: 1px solid rgba(90, 142, 230, 0.35);
            background: rgba(255, 255, 255, 0.2);
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 0.03em;
            transition: all 0.2s ease;
        }

        .register-link:hover {
            background: rgba(90, 142, 230, 0.08);
            color: var(--primary);
            text-decoration: none;
        }

        .btn-login {
            width: 100%;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, #93b8f8 100%);
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.04em;
            padding: 12px 18px;
            box-shadow: 0 10px 18px rgba(91, 142, 230, 0.22);
        }

        .btn-login:hover {
            color: #fff;
            filter: brightness(1.02);
        }

        .alert-success {
            margin-bottom: 20px;
            border-radius: 10px;
            border-color: rgba(40, 167, 69, 0.2);
            background: rgba(40, 167, 69, 0.08);
            color: #1e6f3d;
            font-size: 0.8rem;
        }

        .illustration-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 640px;
        }

        .artboard {
            position: relative;
            width: min(760px, 100%);
            height: 520px;
            margin-left: 24px;
        }

        .blob {
            position: absolute;
            background: linear-gradient(135deg, rgba(164, 193, 244, 0.38), rgba(219, 228, 249, 0.2));
            border-radius: 50%;
            filter: blur(2px);
        }

        .blob.one {
            width: 380px;
            height: 260px;
            left: 40px;
            top: 70px;
        }

        .blob.two {
            width: 430px;
            height: 260px;
            right: 20px;
            bottom: 60px;
            background: linear-gradient(135deg, rgba(182, 196, 235, 0.46), rgba(220, 229, 252, 0.2));
        }

        .blob.three {
            width: 240px;
            height: 200px;
            left: 260px;
            top: 150px;
            background: linear-gradient(135deg, rgba(160, 182, 229, 0.28), rgba(219, 228, 249, 0.08));
        }

        .display {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 540px;
            height: 260px;
            background: rgba(255, 255, 255, 0.25);
            border: 2px solid rgba(117, 137, 203, 0.2);
            border-radius: 16px;
            box-shadow: 0 15px 30px rgba(94, 117, 175, 0.1);
        }

        .display::before {
            content: "";
            position: absolute;
            inset: 20px 18px 14px 18px;
            border-radius: 12px;
            background: linear-gradient(180deg, rgba(247, 249, 255, 0.9), rgba(230, 237, 251, 0.9));
        }

        .chart {
            position: absolute;
            left: 50%;
            top: 52%;
            transform: translate(-50%, -50%);
            width: 440px;
            height: 150px;
            border-radius: 12px;
            z-index: 2;
        }

        .chart .bars {
            position: absolute;
            left: 42px;
            bottom: 0;
            width: 170px;
            height: 120px;
            display: flex;
            align-items: flex-end;
            gap: 12px;
        }

        .chart .bars span {
            display: inline-block;
            width: 22px;
            border-radius: 10px 10px 0 0;
            background: linear-gradient(180deg, rgba(162, 190, 243, 0.9), rgba(111, 151, 222, 0.95));
        }

        .chart .bars span:nth-child(1) { height: 35%; }
        .chart .bars span:nth-child(2) { height: 72%; }
        .chart .bars span:nth-child(3) { height: 52%; }
        .chart .bars span:nth-child(4) { height: 84%; }
        .chart .bars span:nth-child(5) { height: 60%; }

        .chart .donut {
            position: absolute;
            right: 64px;
            bottom: 12px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(#9bbaf7 0 38%, #dfeafc 38% 66%, #b7cff7 66% 100%);
            box-shadow: inset 0 0 0 18px rgba(255,255,255,0.7);
        }

        .chart .donut::after {
            content: "";
            position: absolute;
            inset: 15px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
        }

        .chart .line {
            position: absolute;
            left: 32px;
            right: 40px;
            bottom: 18px;
            height: 56px;
            border: 2px solid rgba(144, 166, 218, 0.4);
            border-color: rgba(144, 166, 218, 0.4) transparent transparent transparent;
            border-radius: 50%;
            transform: rotate(-6deg);
        }

        .person {
            position: absolute;
            bottom: 18px;
            width: 120px;
            height: 200px;
            z-index: 3;
        }

        .person-left {
            left: 54px;
        }

        .person-right {
            right: 92px;
        }

        .person .head {
            position: absolute;
            left: 50%;
            top: 18px;
            transform: translateX(-50%);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #f5d6bb;
            z-index: 2;
        }

        .person .hair {
            position: absolute;
            left: 50%;
            top: 12px;
            transform: translateX(-50%);
            width: 40px;
            height: 18px;
            border-radius: 16px 16px 10px 10px;
            background: rgba(72, 98, 151, 0.88);
            z-index: 3;
        }

        .person .body {
            position: absolute;
            left: 50%;
            top: 52px;
            transform: translateX(-50%);
            width: 72px;
            height: 84px;
            border-radius: 22px 22px 14px 14px;
            background: linear-gradient(180deg, rgba(117, 151, 213, 0.95), rgba(91, 125, 192, 0.96));
        }

        .person .arm {
            position: absolute;
            top: 66px;
            width: 10px;
            height: 70px;
            border-radius: 8px;
            background: #b6c8ed;
        }

        .person-left .arm.left { left: 22px; transform: rotate(18deg); }
        .person-left .arm.right { right: 20px; transform: rotate(-18deg); }
        .person-right .arm.left { left: 24px; transform: rotate(26deg); }
        .person-right .arm.right { right: 18px; transform: rotate(-30deg); }

        .person .legs {
            position: absolute;
            left: 50%;
            top: 128px;
            transform: translateX(-50%);
            width: 80px;
            height: 68px;
        }

        .person .legs::before,
        .person .legs::after {
            content: "";
            position: absolute;
            bottom: 0;
            width: 18px;
            height: 60px;
            border-radius: 10px;
            background: rgba(94, 123, 188, 0.92);
        }

        .person .legs::before { left: 14px; }
        .person .legs::after { right: 14px; }

        .person-right .body {
            background: linear-gradient(180deg, rgba(137, 164, 231, 0.95), rgba(99, 128, 194, 0.96));
        }

        .person .shadow {
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 100px;
            height: 18px;
            background: rgba(62, 90, 152, 0.08);
            border-radius: 50%;
        }

        .mini-card {
            position: absolute;
            bottom: 50px;
            left: 200px;
            width: 120px;
            height: 44px;
            background: rgba(255,255,255,0.36);
            border-radius: 10px;
            border: 1px solid rgba(124, 144, 199, 0.2);
            box-shadow: 0 10px 18px rgba(115, 140, 203, 0.1);
            z-index: 4;
        }

        .mini-card::before,
        .mini-card::after {
            content: "";
            position: absolute;
            left: 16px;
            right: 16px;
            height: 8px;
            border-radius: 8px;
            background: rgba(126, 152, 220, 0.68);
        }

        .mini-card::before { top: 12px; }
        .mini-card::after { top: 24px; width: 54%; }

        .data-pill {
            position: absolute;
            z-index: 4;
            width: 90px;
            height: 18px;
            background: rgba(255,255,255,0.4);
            border-radius: 12px;
            border: 1px solid rgba(124, 144, 199, 0.2);
        }

        .data-pill.one { right: 130px; top: 130px; }
        .data-pill.two { left: 130px; bottom: 130px; }

        @media (max-width: 980px) {
            body {
                overflow-y: auto;
                padding: 30px 0;
            }

            .login-shell {
                min-height: auto;
            }

            .login-wrap {
                grid-template-columns: 1fr;
                gap: 10px;
                padding: 26px 20px 22px;
            }

            .login-panel {
                padding: 8px 8px 0;
            }

            .login-form {
                max-width: 100%;
            }

            .illustration-panel {
                min-height: 420px;
            }

            .artboard {
                transform: scale(0.9);
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <div class="login-wrap">
            <div class="login-panel">
                <div class="label-brand">FinTrack</div>

                <h1 class="login-title">Login</h1>
                <p class="login-subtitle">Welcome to login to your dashboard management system</p>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('login.submit') }}" method="POST" class="login-form">
                    @csrf

                    <div class="field-group">
                        <label for="username" class="field-label">Username</label>
                        <div class="input-shell">
                            <input id="username" type="text" name="username"
                                class="form-control @error('username') is-invalid @enderror"
                                placeholder="Masukkan username"
                                value="{{ old('username') }}" autofocus>
                            <span class="icon"><i class="bi bi-person"></i></span>
                        </div>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="password" class="field-label">Password</label>
                        <div class="input-shell">
                            <input id="password" type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Masukkan password">
                            <button type="button" class="toggle-password" id="togglePassword" aria-label="Lihat password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="remember-row">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Ingat saya</label>
                    </div>

                    <button type="submit" class="btn btn-login">LOGIN</button>

                    <a href="{{ route('register.show') }}" class="register-link">
                        <i class="bi bi-person-plus"></i>
                        BUKA AKUN BARU
                    </a>
                </form>
            </div>

            <div class="illustration-panel">
                <div class="artboard" aria-hidden="true">
                    <div class="blob one"></div>
                    <div class="blob two"></div>
                    <div class="blob three"></div>
                    <div class="display"></div>
                    <div class="chart">
                        <div class="bars">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <div class="donut"></div>
                        <div class="line"></div>
                    </div>

                    <div class="mini-card"></div>
                    <div class="data-pill one"></div>
                    <div class="data-pill two"></div>

                    <div class="person person-left">
                        <div class="hair"></div>
                        <div class="head"></div>
                        <div class="body"></div>
                        <div class="arm left"></div>
                        <div class="arm right"></div>
                        <div class="legs"></div>
                        <div class="shadow"></div>
                    </div>

                    <div class="person person-right">
                        <div class="hair"></div>
                        <div class="head"></div>
                        <div class="body"></div>
                        <div class="arm left"></div>
                        <div class="arm right"></div>
                        <div class="legs"></div>
                        <div class="shadow"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword')?.addEventListener('click', function () {
            const pwd = document.getElementById('password');
            const icon = this.querySelector('i');

            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                pwd.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    </script>
</body>
</html>

