<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Centro San Alfonso</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-bg:       #0f1117;
            --color-surface:  #1a1d27;
            --color-border:   #2e3248;
            --color-primary:  #4f6ef7;
            --color-primary-h:#3a56d4;
            --color-accent:   #7c5ef7;
            --color-text:     #e2e6f3;
            --color-muted:    #7c84a3;
            --color-danger:   #ef4444;
            --radius:         14px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }
        /* Decorative blobs */
        body::before {
            content: '';
            position: fixed;
            top: -20%; left: -10%;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(79,110,247,.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -15%; right: -5%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(124,94,247,.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 920px;
            width: 100%;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            box-shadow: 0 24px 80px rgba(0,0,0,.6);
            overflow: hidden;
            position: relative; z-index: 1;
        }
        /* Left panel */
        .login-brand {
            background: linear-gradient(145deg, #1a1d27 0%, #151828 100%);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .login-brand::before {
            content: '';
            position: absolute;
            top: -60px; left: -60px;
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(34,197,94,.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .login-brand::after {
            content: '';
            position: absolute;
            bottom: -40px; right: -40px;
            width: 220px; height: 220px;
            background: radial-gradient(circle, rgba(56,189,248,.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .brand-logo-wrap {
            width: 120px; height: 120px;
            margin-bottom: 1.75rem;
            position: relative;
            z-index: 1;
        }
        .brand-logo-wrap::before {
            content: '';
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34,197,94,.25), rgba(56,189,248,.15));
            filter: blur(12px);
            animation: logoPulse 3s ease-in-out infinite;
        }
        @keyframes logoPulse {
            0%, 100% { opacity: .7; transform: scale(1); }
            50%       { opacity: 1;  transform: scale(1.08); }
        }
        .brand-logo-wrap img {
            width: 120px; height: 120px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 8px 20px rgba(34,197,94,.4)) drop-shadow(0 2px 8px rgba(0,0,0,.5));
            transition: transform .3s;
        }
        .brand-logo-wrap img:hover { transform: scale(1.06) rotate(3deg); }
        .brand-name {
            font-size: 1.5rem; font-weight: 700;
            line-height: 1.3;
            margin-bottom: .5rem;
        }
        .brand-sub {
            font-size: .85rem;
            color: var(--color-muted);
            line-height: 1.6;
        }
        .brand-features { margin-top: 2rem; display: flex; flex-direction: column; gap: .75rem; }
        .feature {
            display: flex; align-items: center; gap: .75rem;
            font-size: .82rem; color: var(--color-muted);
        }
        .feature-dot {
            width: 8px; height: 8px;
            background: var(--color-primary);
            border-radius: 50%; flex-shrink: 0;
        }
        /* Right panel */
        .login-form-panel {
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-title { font-size: 1.3rem; font-weight: 700; margin-bottom: .4rem; }
        .form-subtitle { font-size: .85rem; color: var(--color-muted); margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.1rem; }
        label { display: block; font-size: .8rem; font-weight: 500; color: var(--color-muted); margin-bottom: .4rem; }
        input {
            width: 100%;
            background: rgba(255,255,255,.04);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            padding: .7rem .9rem;
            color: var(--color-text);
            font-size: .9rem;
            font-family: inherit;
            transition: border-color .2s, box-shadow .2s;
        }
        input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(79,110,247,.2);
        }
        input.is-invalid { border-color: var(--color-danger); }
        .error-text { color: var(--color-danger); font-size: .75rem; margin-top: .3rem; }
        .btn-login {
            width: 100%;
            padding: .8rem;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: .9rem; font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
            transition: opacity .2s, transform .1s;
            font-family: inherit;
        }
        .btn-login:hover { opacity: .9; }
        .btn-login:active { transform: scale(.99); }
        .remember-row {
            display: flex; align-items: center; gap: .5rem;
            font-size: .82rem; color: var(--color-muted);
            margin-top: .25rem;
        }
        input[type="checkbox"] { width: auto; }
        .hint-box {
            margin-top: 1.5rem;
            background: rgba(79,110,247,.08);
            border: 1px solid rgba(79,110,247,.2);
            border-radius: 8px;
            padding: .75rem 1rem;
            font-size: .78rem;
            color: var(--color-muted);
            line-height: 1.6;
        }
        .hint-box strong { color: var(--color-text); }
        @media (max-width: 680px) {
            .login-wrapper { grid-template-columns: 1fr; }
            .login-brand { display: none; }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    {{-- Panel izquierdo (marca) --}}
    <div class="login-brand">
        <div class="brand-logo-wrap">
            <img src="{{ asset('sanalfonzo.png') }}" alt="Centro San Alfonso Logo" draggable="false">
        </div>
        <div class="brand-name">Centro Integral<br>San Alfonso, C.A.</div>
        <div class="brand-sub">Sistema de Gestión de Citas e Historias Clínicas</div>
        <div class="brand-features">
            <div class="feature">
                <div class="feature-dot"></div>
                <span>Gestión digital de historias clínicas</span>
            </div>
            <div class="feature">
                <div class="feature-dot"></div>
                <span>Agendamiento y control de citas</span>
            </div>
            <div class="feature">
                <div class="feature-dot"></div>
                <span>Control de ausentismo y reportes</span>
            </div>
            <div class="feature">
                <div class="feature-dot"></div>
                <span>Secreto médico garantizado por roles</span>
            </div>
        </div>
    </div>

    {{-- Panel derecho (formulario) --}}
    <div class="login-form-panel">
        <div class="form-title">Bienvenido</div>
        <div class="form-subtitle">Ingresa tus credenciales para continuar</div>

        <form id="login-form" action="{{ route('login') }}" method="POST" novalidate>
            @csrf

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="correo@ejemplo.com"
                    autocomplete="email"
                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                    required
                >
                @error('email')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                >
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember" style="margin-bottom:0; cursor:pointer;">Recordar sesión</label>
            </div>

            <button type="submit" class="btn-login" id="btn-submit">
                Iniciar Sesión
            </button>
        </form>

        <div class="hint-box">
            <strong>Acceso de demostración:</strong><br>
            Admin: <strong>admin@santalfonso.com</strong><br>
            Especialista: <strong>terapeuta@santalfonso.com</strong><br>
            Contraseña: <strong>password</strong>
        </div>
    </div>
</div>
</body>
</html>
