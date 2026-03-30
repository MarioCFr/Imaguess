<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMAGUESS // DASHBOARD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=VT323&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #00ff41;
            --green-dim: #00b32c;
            --green-dark: #003b0f;
            --green-glow: rgba(0,255,65,0.15);
            --bg: #0a0a0a;
        }
        * { box-sizing: border-box; }
        body {
            background: var(--bg);
            font-family: 'Share Tech Mono', monospace;
            color: var(--green);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
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
            background: radial-gradient(ellipse at center, transparent 55%, rgba(0,0,0,0.6) 100%);
            pointer-events: none; z-index: 99;
        }
        #matrix-canvas {
            position: fixed; top:0; left:0; width:100%; height:100%;
            opacity: 0.05; z-index: 0;
        }
        .scanline-move {
            position: fixed; width: 100%; height: 3px;
            background: linear-gradient(transparent, rgba(0,255,65,0.06), transparent);
            animation: scanline 8s linear infinite;
            pointer-events: none; z-index: 101;
        }
        @keyframes scanline { 0% { top:-4px; } 100% { top:100vh; } }

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
            position: absolute; top: calc(100% + 8px); right: 0;
            background: rgba(0,10,2,0.97);
            border: 1px solid var(--green-dim);
            box-shadow: 0 0 20px var(--green-glow);
            min-width: 180px;
            display: none;
            z-index: 200;
        }
        .profile-dropdown.open { display: block; }
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

        /* Main content */
        .terminal-box {
            border: 1px solid var(--green-dim);
            box-shadow: 0 0 20px var(--green-glow), inset 0 0 20px rgba(0,255,65,0.02);
            background: rgba(0,10,2,0.9);
            position: relative;
        }
        .corner { position:absolute; width:12px; height:12px; border-color:var(--green); border-style:solid; }
        .corner-tl { top:-1px; left:-1px; border-width:1px 0 0 1px; }
        .corner-tr { top:-1px; right:-1px; border-width:1px 1px 0 0; }
        .corner-bl { bottom:-1px; left:-1px; border-width:0 0 1px 1px; }
        .corner-br { bottom:-1px; right:-1px; border-width:0 1px 1px 0; }

        .play-btn {
            position: relative;
            background: transparent;
            border: 2px solid var(--green);
            color: var(--green);
            font-family: 'VT323', monospace;
            font-size: 2.5rem;
            letter-spacing: 6px;
            padding: 20px 60px;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.3s;
            text-transform: uppercase;
        }
        .play-btn::before {
            content: '';
            position: absolute; inset: 0;
            background: var(--green);
            transform: translateY(100%);
            transition: transform 0.3s;
            z-index: -1;
        }
        .play-btn:hover { color: #000; box-shadow: 0 0 40px rgba(0,255,65,0.4), 0 0 80px rgba(0,255,65,0.15); }
        .play-btn:hover::before { transform: translateY(0); }
        .play-btn .play-icon { margin-right: 12px; }

        .stat-box {
            border: 1px solid rgba(0,255,65,0.25);
            padding: 16px 20px;
            background: rgba(0,255,65,0.03);
            text-align: center;
            position: relative;
        }
        .stat-num {
            font-family: 'VT323', monospace;
            font-size: 2.5rem;
            color: var(--green);
            text-shadow: 0 0 10px rgba(0,255,65,0.4);
            line-height: 1;
        }
        .stat-label { font-size: 0.65rem; color: var(--green-dim); letter-spacing: 2px; margin-top: 4px; }

        .blink { animation: blink 1s step-end infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        .flicker { animation: flicker 10s infinite; }
        @keyframes flicker { 0%,96%,100%{opacity:1;} 97%{opacity:0.5;} 98%{opacity:1;} 99%{opacity:0.7;} }

        /* Recent activity */
        .log-line {
            font-size: 0.72rem;
            color: var(--green-dim);
            padding: 4px 0;
            border-bottom: 1px solid rgba(0,255,65,0.06);
            display: flex; gap: 12px;
        }
        .log-line .time { color: #1a5c29; min-width: 80px; }
        .log-line .pts { color: var(--green); margin-left: auto; }
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
        <div style="position:relative;">
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
                <a href="{{ route('profile') }}" class="dropdown-item">&gt; MI PERFIL</a>
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

    <!-- Main -->
    <div style="position:relative;z-index:10;padding-top:80px;" class="min-h-screen flex flex-col items-center justify-center px-4">

        <!-- Welcome line -->
        <div style="font-size:0.75rem; color:var(--green-dim); letter-spacing:3px; margin-bottom:48px; text-align:center;">
            &gt; BIENVENIDO, <span style="color:var(--green);">{{ strtoupper(auth()->user()->name ?? 'OPERADOR') }}</span> — LISTO PARA COMENZAR<span class="blink">_</span>
        </div>

        <!-- PLAY BUTTON — centro de la pantalla -->
        <div style="text-align:center; margin-bottom:64px;">
            <a href="{{ route('game') }}" style="text-decoration:none; display:inline-block;">
                <button class="play-btn">
                    <span class="play-icon">▶</span> INICIAR PARTIDA
                </button>
            </a>
            <div style="margin-top:12px; font-size:0.7rem; color:#1a5c29; letter-spacing:2px;">
                60 SEG · IMÁGENES ALEATORIAS · IA JUDGE
            </div>
        </div>

        <!-- Stats row -->
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; width:100%; max-width:480px; margin-bottom:48px;">
            <div class="stat-box">
                <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div><div class="corner corner-br"></div>
                <div class="stat-num">{{ auth()->user()->scores()->max('points') ?? 0 }}</div>
                <div class="stat-label">RÉCORD</div>
            </div>
            <div class="stat-box">
                <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div><div class="corner corner-br"></div>
                <div class="stat-num">{{ auth()->user()->scores()->count() ?? 0 }}</div>
                <div class="stat-label">PARTIDAS</div>
            </div>
            <div class="stat-box">
                <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div><div class="corner corner-br"></div>
                <div class="stat-num">#{{ auth()->user()->globalRank() ?? '—' }}</div>
                <div class="stat-label">RANKING</div>
            </div>
        </div>

        <!-- Recent log -->
        <div class="terminal-box" style="width:100%; max-width:480px; padding:20px;">
            <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div><div class="corner corner-br"></div>
            <div style="font-size:0.65rem; color:var(--green-dim); letter-spacing:3px; margin-bottom:12px;">
                &gt; HISTORIAL_RECIENTE.LOG
            </div>
            @forelse(auth()->user()->scores()->latest()->take(4)->get() as $score)
            <div class="log-line">
                <span class="time">{{ $score->created_at->format('d/m H:i') }}</span>
                <span>PARTIDA COMPLETADA</span>
                <span class="pts">+{{ $score->points }} PTS</span>
            </div>
            @empty
            <div class="log-line">
                <span class="time">--/-- --:--</span>
                <span style="color:#1a5c29;">SIN REGISTROS — JUEGA TU PRIMERA PARTIDA</span>
            </div>
            @endforelse
            <a href="{{ route('ranking') }}" style="display:block; margin-top:12px; font-size:0.7rem; color:var(--green-dim); text-decoration:none; letter-spacing:1px; transition:color 0.2s;"
               onmouseover="this.style.color='var(--green)'" onmouseout="this.style.color='var(--green-dim)'">
                &gt; VER LEADERBOARD COMPLETO →
            </a>
        </div>

        <!-- Footer -->
        <div style="margin-top:40px; font-size:0.6rem; color:#1a5c29; letter-spacing:3px;">
            IMAGUESS v1.0 &nbsp;|&nbsp; PEXELS API &nbsp;|&nbsp; AZURE VISION &nbsp;|&nbsp; LARAVEL {{ app()->version() ?? '12' }}
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

        // Matrix rain
        const canvas = document.getElementById('matrix-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth; canvas.height = window.innerHeight;
        const cols = Math.floor(canvas.width / 16);
        const drops = Array(cols).fill(1);
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%アイウエオ';
        function drawMatrix() {
            ctx.fillStyle = 'rgba(0,0,0,0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#00ff41';
            ctx.font = '14px Share Tech Mono';
            drops.forEach((y, i) => {
                ctx.fillText(chars[Math.floor(Math.random() * chars.length)], i * 16, y * 16);
                if (y * 16 > canvas.height && Math.random() > 0.975) drops[i] = 0;
                drops[i]++;
            });
        }
        setInterval(drawMatrix, 50);
</script>
</body>
</html>
