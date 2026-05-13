<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Leave Report System — Province of Bukidnon</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #e8edf2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* Remove default browser eye icons */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }

        /* ── Outer card ──────────────────────────────────────── */
        .auth-card {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 580px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,.18);
            position: relative;
        }

        /* ── HR Logo bar (top-left absolute) ────────────────── */
        .logo-bar {
            position: absolute;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 20px;
            z-index: 20;
        }
        .logo-bar img {
            width: 46px;
            height: 46px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,.25));
        }
        .logo-bar-text {
            line-height: 1.25;
        }
        .logo-bar-text .province {
            font-size: 0.7rem;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: .05em;
            text-shadow: 0 1px 3px rgba(0,0,0,.3);
        }
        .logo-bar-text .system {
            font-size: 0.65rem;
            font-weight: 500;
            color: rgba(255,255,255,.85);
            text-shadow: 0 1px 3px rgba(0,0,0,.3);
        }

        /* ── LEFT PANEL (Sign In) ────────────────────────────── */
        .panel-left {
            flex: 1;
            background: linear-gradient(145deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 40px 40px;
            overflow: hidden;
            min-width: 0;
        }

        /* Decorative diamond shapes */
        .panel-left::before,
        .panel-left::after {
            content: '';
            position: absolute;
            border: 2px solid rgba(255,255,255,.15);
            transform: rotate(45deg);
            border-radius: 4px;
        }
        .panel-left::before {
            width: 100px; height: 100px;
            top: 18%; right: -20px;
        }
        .panel-left::after {
            width: 60px; height: 60px;
            bottom: 22%; left: 20px;
        }
        .diamond-extra {
            position: absolute;
            border: 2px solid rgba(255,255,255,.12);
            transform: rotate(45deg);
            border-radius: 3px;
        }
        .diamond-extra.d1 { width: 45px; height: 45px; top: 55%; right: 25px; }
        .diamond-extra.d2 { width: 70px; height: 70px; bottom: 10%; right: 10px; }
        .diamond-extra.d3 { width: 35px; height: 35px; top: 25%; left: 15px; }

        .panel-left-content {
            text-align: center;
            position: relative;
            z-index: 5;
        }
        .panel-left-content h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 12px;
            text-shadow: 0 2px 8px rgba(0,0,0,.15);
        }
        .panel-left-content p {
            font-size: 0.875rem;
            color: rgba(255,255,255,.85);
            line-height: 1.6;
            margin-bottom: 32px;
            max-width: 220px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Sign In form inside left panel */
        .signin-form {
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
        }
        .signin-form .form-group {
            margin-bottom: 14px;
        }
        .signin-form label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255,255,255,.9);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .signin-form .input-wrap {
            position: relative;
        }
        .signin-form .input-wrap > svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: rgba(255,255,255,.6);
            pointer-events: none;
        }
        .signin-form input {
            width: 100%;
            background: rgba(255,255,255,.15);
            border: 1.5px solid rgba(255,255,255,.25);
            border-radius: 10px;
            padding: 10px 12px 10px 36px;
            font-size: 0.85rem;
            color: #fff;
            outline: none;
            transition: border-color .2s, background .2s;
            font-family: 'Inter', sans-serif;
        }
        .signin-form input::placeholder { color: rgba(255,255,255,.5); }
        .signin-form input:focus {
            border-color: rgba(255,255,255,.6);
            background: rgba(255,255,255,.2);
        }

        .signin-remember {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 18px;
        }
        .signin-remember input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: #fff;
            padding: 0;
            border-radius: 4px;
        }
        .signin-remember span {
            font-size: 0.78rem;
            color: rgba(255,255,255,.8);
        }

        .btn-signin {
            width: 100%;
            background: rgba(255,255,255,.0);
            border: 2px solid #fff;
            border-radius: 30px;
            padding: 11px 20px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: .1em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .25s, color .25s, transform .15s;
            font-family: 'Inter', sans-serif;
        }
        .btn-signin:hover {
            background: rgba(255,255,255,.15);
            transform: scale(1.02);
        }
        .btn-signin:active { transform: scale(0.98); }

        /* Error / status alerts on left panel */
        .alert-error-left {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            color: #fff;
            font-size: 0.78rem;
            border-radius: 10px;
            padding: 9px 12px;
            margin-bottom: 12px;
            text-align: left;
        }
        .alert-success-left {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            color: #fff;
            font-size: 0.78rem;
            border-radius: 10px;
            padding: 9px 12px;
            margin-bottom: 12px;
        }

        /* ── RIGHT PANEL (Sign Up) ───────────────────────────── */
        .panel-right {
            flex: 1;
            background: #f0f4f8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 40px 40px;
            min-width: 0;
            position: relative;
        }

        .panel-right h2 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #2563eb;
            margin-bottom: 22px;
            text-align: center;
        }

        /* Register form */
        .register-form {
            width: 100%;
            max-width: 310px;
        }
        .register-form .form-group {
            margin-bottom: 13px;
        }
        .register-form label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #5a6a7a;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .register-form .input-wrap {
            position: relative;
        }
        .register-form .input-wrap > svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: #9aabb8;
            pointer-events: none;
        }
        .register-form input {
            width: 100%;
            background: #dce6ee;
            border: 1.5px solid #c5d4df;
            border-radius: 10px;
            padding: 10px 12px 10px 36px;
            font-size: 0.85rem;
            color: #2d3e50;
            outline: none;
            transition: border-color .2s, background .2s;
            font-family: 'Inter', sans-serif;
        }
        .register-form input::placeholder { color: #9aabb8; }
        .register-form input:focus {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,.6);
            z-index: 10;
            outline: none;
        }
        .password-toggle:hover {
            color: rgba(255,255,255,.9);
        }
        .password-toggle:focus {
            color: rgba(255,255,255,.9);
        }
        .password-toggle svg {
            width: 18px;
            height: 18px;
            pointer-events: none;
        }
        .password-toggle.dark {
            color: #9aabb8;
        }
        .password-toggle.dark:hover,
        .password-toggle.dark:focus {
            color: #2563eb;
        }
        .input-has-toggle {
            padding-right: 40px !important;
        }

        .btn-signup {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            border-radius: 30px;
            padding: 12px 20px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: .1em;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 6px;
            transition: opacity .25s, transform .15s;
            box-shadow: 0 6px 20px rgba(37, 99, 235, .35);
            font-family: 'Inter', sans-serif;
        }
        .btn-signup:hover { opacity: .9; transform: scale(1.02); }
        .btn-signup:active { transform: scale(0.98); }

        /* Error / status alerts on right panel */
        .alert-error-right {
            background: #fde8e8;
            border: 1px solid #f5a5a5;
            color: #c0392b;
            font-size: 0.78rem;
            border-radius: 10px;
            padding: 9px 12px;
            margin-bottom: 12px;
            text-align: left;
            width: 100%;
            max-width: 310px;
        }


        /* ── Developer Credit ── */
        .dev-credit {
            position: absolute;
            bottom: 4px;
            font-size: 0.59rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            z-index: 25;
            white-space: nowrap;
            
            /* Shiny Animation */
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: devShine 4s linear infinite;
        }
        .dev-credit-left {
            right: 0;
            padding-right: 4px;
            background-image: linear-gradient(120deg, #ffffff 30%, #93c5fd 50%, #ffffff 70%);
        }
        .dev-credit-right {
            left: 0;
            padding-left: 4px;
            background-image: linear-gradient(120deg, #2563eb 30%, #ffffff 50%, #2563eb 70%);
        }

        @keyframes devShine {
            0% { background-position: 120% center; }
            100% { background-position: -120% center; }
        }

        /* ── Responsive ──────────────────────────────────────── */

        @media (max-width: 680px) {
            .auth-card {
                flex-direction: column;
                max-width: 420px;
                border-radius: 16px;
            }
            .panel-left { padding: 70px 28px 32px; }
            .panel-right { padding: 32px 28px 36px; }
            .logo-bar-text .province,
            .logo-bar-text .system { color: #fff; }

            .dev-credit {
                position: relative;
                bottom: 0;
                right: auto;
                left: auto;
                text-align: center;
                display: block;
                margin-top: 15px;
            }
            .dev-credit-left { padding-right: 0; }
            .dev-credit-right { padding-left: 0; }
        }
    </style>
</head>
<body>

<div class="auth-card">

    {{-- ── HR Logo (top-left, overlays both panels) ─────────────────────── --}}
    <div class="logo-bar">
        <img src="{{ asset('images/hrlogo.png') }}" alt="HR Logo" />
        <div class="logo-bar-text">
            <div class="province">Province of Bukidnon</div>
            <div class="system">Leave Report System</div>
        </div>
    </div>

    {{-- ══════════════ LEFT PANEL – SIGN IN ══════════════ --}}
    <div class="panel-left">
        {{-- Decorative diamonds --}}
        <div class="diamond-extra d1"></div>
        <div class="diamond-extra d2"></div>
        <div class="diamond-extra d3"></div>

        <div class="panel-left-content">
            <h2>Welcome Back!</h2>
            <p>To keep connected with us please login with your personal info.</p>

            {{-- Error / Status from login --}}
            @if (session('login_status'))
                <div class="alert-success-left">{{ session('login_status') }}</div>
            @endif

            @if (session('register_success'))
                <div class="alert-success-left">{{ session('register_success') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="signin-form">
                @csrf

                {{-- Login errors --}}
                @if ($errors->hasBag('login') && $errors->getBag('login')->any())
                    <div class="alert-error-left">
                        {{ $errors->getBag('login')->first() }}
                    </div>
                @endif

                {{-- Email --}}
                <div class="form-group">
                    <label for="login_email">Email Address</label>
                    <div class="input-wrap">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                        <input id="login_email" name="email" type="email" autocomplete="email"
                               value="{{ old('email') }}" required placeholder="you@example.com" />
                    </div>
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label for="login_password">Password</label>
                    <div class="input-wrap">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input id="login_password" name="password" type="password" autocomplete="current-password"
                               required placeholder="••••••••" class="input-has-toggle" />
                        <button type="button" class="password-toggle" onclick="togglePassword('login_password', this)" tabindex="-1">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="eye-icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="signin-remember">
                    <input type="checkbox" name="remember" id="remember" />
                    <span>Remember me</span>
                </div>

                <button type="submit" class="btn-signin">Sign In</button>
            </form>
        </div>
        <div class="dev-credit dev-credit-left">SYSTEM DEVELOPED BY:</div>
    </div>

    {{-- ══════════════ RIGHT PANEL – SIGN UP ══════════════ --}}
    <div class="panel-right">
        <h2>Create Account</h2>

        {{-- Register errors --}}
        @if ($errors->hasBag('register') && $errors->getBag('register')->any())
            <div class="alert-error-right">
                @foreach ($errors->getBag('register')->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="register-form">
            @csrf

            {{-- Full Name --}}
            <div class="form-group">
                <label for="reg_name">Full Name</label>
                <div class="input-wrap">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <input id="reg_name" name="name" type="text" autocomplete="name"
                           value="{{ old('name') }}" required placeholder="Juan Dela Cruz" />
                </div>
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="reg_email">Email Address</label>
                <div class="input-wrap">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                    <input id="reg_email" name="email" type="email" autocomplete="email"
                           value="{{ old('email') }}" required placeholder="you@example.com" />
                </div>
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="reg_password">Password</label>
                <div class="input-wrap">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input id="reg_password" name="password" type="password" autocomplete="new-password"
                           required placeholder="At least 8 characters" class="input-has-toggle" />
                    <button type="button" class="password-toggle dark" onclick="togglePassword('reg_password', this)" tabindex="-1">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="eye-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div class="form-group">
                <label for="reg_password_confirmation">Confirm Password</label>
                <div class="input-wrap">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <input id="reg_password_confirmation" name="password_confirmation" type="password"
                           autocomplete="new-password" required placeholder="Repeat your password" class="input-has-toggle" />
                    <button type="button" class="password-toggle dark" onclick="togglePassword('reg_password_confirmation', this)" tabindex="-1">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="eye-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-signup">Sign Up</button>
        </form>
        <div class="dev-credit dev-credit-right">ANGEL MAE LAGARE</div>
    </div>

</div>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('.eye-icon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    }
</script>
</body>
</html>
