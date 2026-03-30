<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMAGUESS // PERFIL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=VT323&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #00ff41;
            --green-dim: #00b32c;
            --green-glow: rgba(0,255,65,0.15);
            --red: #ff0040;
            --bg: #0a0a0a;
        }
        * { box-sizing: border-box; }
        body {
            background: var(--bg);
            font-family: 'Share Tech Mono', monospace;
            color: var(--green);
            min-height: 100vh;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.12) 2px, rgba(0,0,0,0.12) 4px);
            pointer-events: none; z-index: 100;
        }
        body::after {
            content: '';
            position: fixed; inset: 0;
            background: radial-gradient(ellipse at center, transparent 55%, rgba(0,0,0,0.65) 100%);
            pointer-events: none; z-index: 99;
        }
        #matrix-canvas {
            position: fixed; top:0; left:0; width:100%; height:100%;
            opacity: 0.04; z-index: 0;
        }
        .scanline-move {
            position: fixed; width:100%; height:3px;
            background: linear-gradient(transparent, rgba(0,255,65,0.06), transparent);
            animation: scanline 7s linear infinite;
            pointer-events: none; z-index: 101;
        }
        @keyframes scanline { 0%{top:-4px;} 100%{top:100vh;} }

                /* Navbar */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 150;
            border-bottom: 1px solid rgba(0,255,65,0.2);
            box-shadow: 0 0 20px rgba(0,255,65,0.06), inset 0 0 20px rgba(0,255,65,0.01);
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(4px);
            padding: 12px 32px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .nav-logo {
            font-family: 'VT323', monospace;
            font-size: 1.8rem;
            color: var(--green);
            text-shadow: 0 0 10px var(--green);
            letter-spacing: 2px;
        }
        .nav-status {
            display: flex; align-items: center; gap: 16px;
            font-size: 0.7rem; color: var(--green-dim);
        }
        .status-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 6px var(--green);
            animation: pulse 2s ease-in-out infinite;
        }
        .profile-btn {
            width: 38px; height: 38px;
            border: 1px solid var(--green-dim);
            border-radius: 2px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            background: transparent;
            position: relative;
        }
        .profile-btn:hover {
            border-color: var(--green);
            box-shadow: 0 0 12px var(--green-glow);
        }
        .profile-dropdown {
            position: absolute; top: 100%; right: 0;
            background: rgba(0,10,2,0.97);
            border: 1px solid var(--green-dim);
            box-shadow: 0 0 20px var(--green-glow);
            min-width: 180px;
            display: block;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s;
            padding-top: 8px;
            z-index: 200;
        }
        .profile-dropdown.open,
        .dropdown-wrap:hover .profile-dropdown {
            opacity: 1;
            pointer-events: all;
        }
        .dropdown-item {
            padding: 10px 16px;
            font-size: 0.75rem;
            color: var(--green-dim);
            cursor: pointer;
            transition: all 0.15s;
            border-bottom: 1px solid rgba(0,255,65,0.1);
            display: block; text-decoration: none;
            letter-spacing: 1px;
            background: transparent;
            border-left: none; border-right: none; border-top: none;
            width: 100%; text-align: left;
            font-family: 'Share Tech Mono', monospace;
        }
        .dropdown-item:last-child { border-bottom: none; }
        .dropdown-item:hover { color: var(--green); background: rgba(0,255,65,0.05); }

        /* Terminal box */
        .t-box {
            border: 1px solid var(--green-dim);
            box-shadow: 0 0 20px var(--green-glow), inset 0 0 20px rgba(0,255,65,0.02);
            background: rgba(0,10,2,0.92);
            position: relative;
        }
        .corner { position:absolute; width:12px; height:12px; border-color:var(--green); border-style:solid; }
        .corner-tl { top:-1px; left:-1px; border-width:1px 0 0 1px; }
        .corner-tr { top:-1px; right:-1px; border-width:1px 1px 0 0; }
        .corner-bl { bottom:-1px; left:-1px; border-width:0 0 1px 1px; }
        .corner-br { bottom:-1px; right:-1px; border-width:0 1px 1px 0; }

        /* Avatar */
        .avatar-ring {
            width: 96px; height: 96px;
            border: 2px solid var(--green);
            border-radius: 2px;
            display: flex; align-items:center; justify-content:center;
            background: rgba(0,255,65,0.04);
            box-shadow: 0 0 20px var(--green-glow);
            position: relative;
            flex-shrink: 0;
        }
        .avatar-ring::before {
            content: '';
            position:absolute; inset:-6px;
            border: 1px solid rgba(0,255,65,0.2);
            border-radius: 2px;
            animation: rotate 8s linear infinite;
        }
        @keyframes rotate { to { transform: rotate(360deg); } }
        .avatar-letter {
            font-family: 'VT323', monospace;
            font-size: 3.5rem;
            color: var(--green);
            text-shadow: 0 0 20px var(--green);
            line-height: 1;
        }

        /* Form fields */
        .field-label {
            font-size: 0.65rem; color: var(--green-dim);
            letter-spacing: 3px; text-transform: uppercase;
            margin-bottom: 6px;
        }
        .field-value {
            font-size: 0.9rem; color: var(--green);
            padding: 10px 14px;
            border: 1px solid rgba(0,255,65,0.2);
            background: rgba(0,255,65,0.03);
            letter-spacing: 1px;
        }
        .field-input {
            background: transparent;
            border: none;
            border-bottom: 1px solid var(--green-dim);
            color: var(--green);
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.9rem;
            outline: none;
            width: 100%;
            padding: 8px 4px;
            letter-spacing: 1px;
            transition: border-color 0.2s;
            caret-color: var(--green);
        }
        .field-input:focus { border-bottom-color: var(--green); }
        .field-input::placeholder { color: #1a5c29; }

        /* Save button */
        .save-btn {
            background: transparent;
            border: 1px solid var(--green);
            color: var(--green);
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.85rem;
            padding: 10px 28px;
            cursor: pointer;
            letter-spacing: 2px;
            transition: all 0.2s;
            position: relative; overflow: hidden;
        }
        .save-btn::before {
            content: ''; position:absolute; inset:0;
            background: var(--green); transform:translateX(-100%);
            transition:transform 0.2s; z-index:-1;
        }
        .save-btn:hover { color:#000; }
        .save-btn:hover::before { transform:translateX(0); }

        /* Danger button */
        .danger-btn {
            background: transparent;
            border: 1px solid rgba(255,0,64,0.4);
            color: rgba(255,0,64,0.6);
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.75rem;
            padding: 8px 20px;
            cursor: pointer;
            letter-spacing: 2px;
            transition: all 0.2s;
        }
        .danger-btn:hover { border-color:var(--red); color:var(--red); box-shadow:0 0 12px rgba(255,0,64,0.2); }

        /* Stat cards */
        .stat-card {
            border: 1px solid rgba(0,255,65,0.2);
            background: rgba(0,255,65,0.03);
            padding: 16px;
            text-align: center;
        }
        .stat-num { font-family:'VT323',monospace; font-size:2.2rem; color:var(--green); line-height:1; }
        .stat-lbl { font-size:0.6rem; color:var(--green-dim); letter-spacing:2px; margin-top:4px; }

        /* History rows */
        .hist-row {
            display:flex; align-items:center; justify-content:space-between;
            padding:10px 16px;
            border-bottom:1px solid rgba(0,255,65,0.06);
            font-size:0.75rem;
            transition:background 0.15s;
        }
        .hist-row:last-child { border-bottom:none; }
        .hist-row:hover { background:rgba(0,255,65,0.03); }

        /* Section title */
        .section-title {
            font-size:0.65rem; color:var(--green-dim);
            letter-spacing:3px; margin-bottom:16px;
        }

        /* Toggle switch */
        .toggle-wrap {
            display:flex; align-items:center; justify-content:space-between;
            padding:12px 0;
            border-bottom:1px solid rgba(0,255,65,0.08);
        }
        .toggle-wrap:last-child { border-bottom:none; }
        .toggle-label { font-size:0.8rem; color:var(--green-dim); }
        .toggle-desc { font-size:0.65rem; color:#1a5c29; margin-top:2px; }
        .toggle {
            position:relative; width:44px; height:22px;
            background:rgba(0,255,65,0.1);
            border:1px solid rgba(0,255,65,0.3);
            border-radius:2px;
            cursor:pointer;
            transition:all 0.2s;
            flex-shrink:0;
        }
        .toggle.on { background:rgba(0,255,65,0.2); border-color:var(--green); }
        .toggle::after {
            content:''; position:absolute; top:3px; left:3px;
            width:14px; height:14px;
            background:rgba(0,255,65,0.4);
            transition:all 0.2s;
        }
        .toggle.on::after { left:23px; background:var(--green); box-shadow:0 0 6px var(--green); }

        .blink { animation:blink 1s step-end infinite; }
        @keyframes blink { 50%{opacity:0;} }

        /* Alert */
        .alert-success {
            border:1px solid var(--green-dim);
            background:rgba(0,255,65,0.05);
            color:var(--green);
            padding:8px 14px;
            font-size:0.75rem;
            letter-spacing:1px;
            margin-bottom:16px;
            display:none;
        }
        .alert-success.show { display:block; }

        /* Badge */
        .badge {
            display:inline-block;
            border:1px solid rgba(0,255,65,0.3);
            color:var(--green-dim);
            font-size:0.6rem;
            padding:2px 8px;
            letter-spacing:1px;
            margin-right:4px;
            margin-bottom:4px;
        }
        .badge.earned { border-color:var(--green); color:var(--green); background:rgba(0,255,65,0.06); }

        /* Work in progress box */
        .wip-box {
            border-color: #ffbf00 !important;
            box-shadow: 0 0 20px rgba(255,191,0,0.08), inset 0 0 20px rgba(255,191,0,0.02) !important;
        }
        .wip-box .corner { border-color: #ffbf00 !important; }
        .wip-label {
            font-size: 0.55rem; color: #ffbf00;
            letter-spacing: 2px; opacity: 0.7;
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <div class="scanline-move"></div>
    <canvas id="matrix-canvas"></canvas>

        <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-logo flicker">IMAGUESS</div>
        <div class="nav-status">
            <div class="status-dot"></div>
            <span>SISTEMA ACTIVO</span>
            <span style="color:#1a5c29;">|</span>
            <span>SESIÓN: <span style="color:var(--green);">{{ auth()->user()->name ?? 'INVITADO' }}</span></span>
        </div>
        <div class="dropdown-wrap" style="position:relative;">
            <button class="profile-btn" onclick="toggleDropdown()" title="Perfil">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#00b32c" stroke-width="1.5">
                    <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                </svg>
            </button>
            <div class="profile-dropdown" id="dropdown">
                <div style="padding:10px 16px; font-size:0.65rem; color:#1a5c29; letter-spacing:2px; border-bottom:1px solid rgba(0,255,65,0.15);">
                    ROOT@IMAGUESS<span class="blink">_</span>
                </div>
                <a href="{{ route('dashboard') }}" class="dropdown-item">&gt; DASHBOARD</a>

                <a href="{{ route('ranking') }}" class="dropdown-item">&gt; LEADERBOARD</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item" style="width:100%;text-align:left;">
                        &gt; CERRAR SESIÓN
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div style="position:relative;z-index:10;padding-top:72px;max-width:800px;margin:0 auto;padding-left:16px;padding-right:16px;padding-bottom:60px;">

        <!-- Page header -->
        <div style="padding-top:28px;margin-bottom:32px;">
            <div style="font-size:0.65rem;color:var(--green-dim);letter-spacing:3px;margin-bottom:4px;">
                &gt; SISTEMA // PERFIL_USUARIO
            </div>
            <div style="font-family:'VT323',monospace;font-size:2.5rem;color:var(--green);text-shadow:0 0 12px rgba(0,255,65,0.3);">
                CONFIGURACIÓN
            </div>
        </div>

        @if(session('status'))
        <div class="alert-success show">
            [OK] {{ session('status') }}
        </div>
        @endif

        <!-- TOP: Avatar + info + stats -->
        <div style="display:grid;grid-template-columns:auto 1fr;gap:24px;align-items:start;margin-bottom:24px;">

            <!-- Avatar -->
            <div class="avatar-ring">
                <div class="avatar-letter">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
            </div>

            <!-- Info block -->
            <div class="t-box" style="padding:20px;">
                <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div><div class="corner corner-br"></div>

                <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                    <div>
                        <div style="font-family:'VT323',monospace;font-size:2rem;color:var(--green);text-shadow:0 0 10px rgba(0,255,65,0.3);line-height:1;">
                            {{ strtoupper(auth()->user()->name ?? 'USUARIO') }}
                        </div>
                        <div style="font-size:0.7rem;color:var(--green-dim);margin-top:4px;letter-spacing:1px;">
                            {{ auth()->user()->email ?? 'correo@ejemplo.com' }}
                        </div>
                        <div style="font-size:0.65rem;color:#1a5c29;margin-top:6px;letter-spacing:2px;">
                            MIEMBRO DESDE {{ auth()->user()->created_at?->format('M Y') ?? 'ENERO 2025' }}
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:0.6rem;color:#1a5c29;letter-spacing:2px;margin-bottom:4px;">ESTADO</div>
                        <div style="display:flex;align-items:center;gap:6px;justify-content:flex-end;">
                            <div style="width:6px;height:6px;border-radius:50%;background:var(--green);box-shadow:0 0 6px var(--green);animation:pulse 2s ease-in-out infinite;"></div>
                            <span style="font-size:0.75rem;color:var(--green);">ACTIVO</span>
                        </div>
                        <div style="font-size:0.65rem;color:#1a5c29;margin-top:6px;">
                            RANK GLOBAL: <span style="color:var(--green-dim);">#{{ auth()->user()->globalRank() ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Mini stats -->
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:16px;">
                    <div class="stat-card">
                        <div class="stat-num">{{ auth()->user()->scores()->max('points') ?? 0 }}</div>
                        <div class="stat-lbl">RÉCORD</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-num">{{ auth()->user()->scores()->count() ?? 0 }}</div>
                        <div class="stat-lbl">PARTIDAS</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-num">{{ auth()->user()->scores()->avg('points') ? round(auth()->user()->scores()->avg('points')) : 0 }}</div>
                        <div class="stat-lbl">MEDIA</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two column layout -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">

            <!-- Edit name -->
            <div class="t-box" style="padding:20px;">
                <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div><div class="corner corner-br"></div>
                <div class="section-title">&gt; DATOS_DE_CUENTA</div>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div style="margin-bottom:20px;">
                        <div class="field-label">&gt; NOMBRE DE USUARIO</div>
                        <input type="text" name="name" class="field-input"
                               value="{{ auth()->user()->name ?? '' }}"
                               placeholder="tu_nombre">
                        @error('name')
                            <div style="color:var(--red);font-size:0.65rem;margin-top:4px;">[ERR] {{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom:24px;">
                        <div class="field-label">&gt; EMAIL</div>
                        <input type="email" name="email" class="field-input"
                               value="{{ auth()->user()->email ?? '' }}"
                               placeholder="correo@ejemplo.com">
                        @error('email')
                            <div style="color:var(--red);font-size:0.65rem;margin-top:4px;">[ERR] {{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="save-btn">GUARDAR</button>
                </form>
            </div>

            <!-- Change password -->
            <div class="t-box" style="padding:20px;">
                <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div><div class="corner corner-br"></div>
                <div class="section-title">&gt; CAMBIAR_CONTRASEÑA</div>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div style="margin-bottom:16px;">
                        <div class="field-label">&gt; CONTRASEÑA ACTUAL</div>
                        <input type="password" name="current_password" class="field-input" placeholder="••••••••">
                        @error('current_password')
                            <div style="color:var(--red);font-size:0.65rem;margin-top:4px;">[ERR] {{ $message }}</div>
                        @enderror
                    </div>
                    <div style="margin-bottom:16px;">
                        <div class="field-label">&gt; NUEVA CONTRASEÑA</div>
                        <input type="password" name="password" class="field-input" placeholder="••••••••">
                        @error('password')
                            <div style="color:var(--red);font-size:0.65rem;margin-top:4px;">[ERR] {{ $message }}</div>
                        @enderror
                    </div>
                    <div style="margin-bottom:24px;">
                        <div class="field-label">&gt; CONFIRMAR</div>
                        <input type="password" name="password_confirmation" class="field-input" placeholder="••••••••">
                    </div>

                    <button type="submit" class="save-btn">ACTUALIZAR</button>
                </form>
            </div>
        </div>

        <!-- Preferences + History row -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">

            <!-- Preferences (expandable in future) -->
            <div class="t-box wip-box" style="padding:20px;">
                <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div><div class="corner corner-br"></div>
                <div class="section-title">&gt; PREFERENCIAS <span class="wip-label">[PRÓXIMAMENTE]</span></div>

                <div class="toggle-wrap">
                    <div>
                        <div class="toggle-label">SONIDO</div>
                        <div class="toggle-desc">Efectos de sonido en partida</div>
                    </div>
                    <div class="toggle on" onclick="this.classList.toggle('on')" title="Próximamente"></div>
                </div>
                <div class="toggle-wrap">
                    <div>
                        <div class="toggle-label">ANIMACIONES</div>
                        <div class="toggle-desc">Lluvia de fondo Matrix</div>
                    </div>
                    <div class="toggle on" onclick="this.classList.toggle('on')" title="Próximamente"></div>
                </div>
                <div class="toggle-wrap">
                    <div>
                        <div class="toggle-label">PISTAS AUTOMÁTICAS</div>
                        <div class="toggle-desc">Pista al fallar 3 veces</div>
                    </div>
                    <div class="toggle" onclick="this.classList.toggle('on')" title="Próximamente"></div>
                </div>
                <div class="toggle-wrap">
                    <div>
                        <div class="toggle-label">APARECER EN RANKING</div>
                        <div class="toggle-desc">Mostrar tu puntuación públicamente</div>
                    </div>
                    <div class="toggle on" onclick="this.classList.toggle('on')" title="Próximamente"></div>
                </div>

                <div style="margin-top:14px;font-size:0.6rem;color:#1a5c29;letter-spacing:1px;">
                    * Configuración guardable próximamente
                </div>
            </div>

            <!-- Score history -->
            <div class="t-box" style="padding:20px;">
                <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div><div class="corner corner-br"></div>
                <div class="section-title">&gt; ÚLTIMAS_PARTIDAS</div>

                @forelse(auth()->user()->scores()->latest()->take(6)->get() as $score)
                <div class="hist-row">
                    <span style="color:#1a5c29;font-size:0.65rem;">{{ $score->created_at->format('d/m/y H:i') }}</span>
                    <span style="font-family:'VT323',monospace;font-size:1.3rem;color:var(--green);">
                        {{ $score->points }} <span style="font-size:0.7rem;color:var(--green-dim);">PTS</span>
                    </span>
                </div>
                @empty
                <div style="padding:20px 0;text-align:center;color:#1a5c29;font-size:0.75rem;letter-spacing:2px;">
                    SIN PARTIDAS AÚN
                </div>
                @endforelse

                <a href="{{ route('ranking') }}" style="display:block;margin-top:10px;font-size:0.65rem;color:var(--green-dim);text-decoration:none;letter-spacing:1px;transition:color 0.2s;"
                   onmouseover="this.style.color='var(--green)'" onmouseout="this.style.color='var(--green-dim)'">
                    &gt; VER LEADERBOARD →
                </a>
            </div>
        </div>

        <!-- Badges section (placeholder for future) -->
        <div class="t-box wip-box" style="padding:20px;margin-bottom:16px;">
            <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div><div class="corner corner-br"></div>
            <div class="section-title">&gt; LOGROS <span class="wip-label">[PRÓXIMAMENTE]</span></div>
            <div>
                <span class="badge earned">PRIMERA PARTIDA</span>
                <span class="badge earned">10 IMÁGENES ACERTADAS</span>
                <span class="badge">50 IMÁGENES</span>
                <span class="badge">PUNTUACIÓN 500+</span>
                <span class="badge">SIN PISTAS</span>
                <span class="badge">PARTIDA PERFECTA</span>
            </div>
        </div>

        <!-- Danger zone -->
        <div class="t-box" style="padding:20px;border-color:rgba(255,0,64,0.25);">
            <div class="corner corner-tl" style="border-color:rgba(255,0,64,0.5);"></div>
            <div class="corner corner-tr" style="border-color:rgba(255,0,64,0.5);"></div>
            <div class="corner corner-bl" style="border-color:rgba(255,0,64,0.5);"></div>
            <div class="corner corner-br" style="border-color:rgba(255,0,64,0.5);"></div>
            <div style="font-size:0.65rem;color:rgba(255,0,64,0.5);letter-spacing:3px;margin-bottom:12px;">
                &gt; ZONA_PELIGROSA
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <div style="font-size:0.8rem;color:rgba(255,0,64,0.6);">ELIMINAR CUENTA</div>
                    <div style="font-size:0.65rem;color:#1a5c29;margin-top:2px;">Esta acción es irreversible. Se borrarán todos tus datos.</div>
                </div>
                <button class="danger-btn" onclick="confirmDelete()">ELIMINAR CUENTA</button>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top:40px;text-align:center;font-size:0.6rem;color:#1a5c29;letter-spacing:3px;">
            IMAGUESS v1.0 &nbsp;|&nbsp; TUS DATOS SON PRIVADOS
        </div>
    </div>

    <!-- Delete confirmation modal -->
    <div id="delete-modal" style="display:none;position:fixed;inset:0;z-index:300;background:rgba(0,0,0,0.92);align-items:center;justify-content:center;">
        <div class="t-box" style="padding:32px;max-width:360px;width:90%;text-align:center;">
            <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div><div class="corner corner-br"></div>
            <div style="font-size:0.65rem;color:var(--red);letter-spacing:3px;margin-bottom:16px;">[ADVERTENCIA]</div>
            <div style="font-family:'VT323',monospace;font-size:1.8rem;color:var(--red);margin-bottom:12px;">¿CONFIRMAR BORRADO?</div>
            <div style="font-size:0.75rem;color:var(--green-dim);margin-bottom:24px;line-height:1.6;">
                Se eliminarán permanentemente tu cuenta,<br>puntuaciones e historial de partidas.
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-btn" style="margin-right:12px;">SÍ, ELIMINAR</button>
            </form>
            <button class="save-btn" onclick="document.getElementById('delete-modal').style.display='none'">CANCELAR</button>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById('dropdown').classList.toggle('open');
        }
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.profile-btn') && !e.target.closest('#dropdown')) {
                document.getElementById('dropdown').classList.remove('open');
            }
        });

        function confirmDelete() {
            document.getElementById('delete-modal').style.display = 'flex';
        }

        // Matrix background
        const canvas = document.getElementById('matrix-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth; canvas.height = window.innerHeight;
        const cols = Math.floor(canvas.width / 16);
        const drops = Array(cols).fill(1);
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%^&*()アイウエオ';
        function drawMatrix() {
            ctx.fillStyle='rgba(0,0,0,0.05)'; ctx.fillRect(0,0,canvas.width,canvas.height);
            ctx.fillStyle='#00ff41'; ctx.font='14px Share Tech Mono';
            drops.forEach((y,i) => {
                ctx.fillText(chars[Math.floor(Math.random()*chars.length)],i*16,y*16);
                if(y*16>canvas.height && Math.random()>0.975) drops[i]=0;
                drops[i]++;
            });
        }
        setInterval(drawMatrix, 50);

    </script>
    <style>
        @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.3;} }
    </style>
</body>
</html>
