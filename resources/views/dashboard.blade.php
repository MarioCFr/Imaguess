<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMAGUESS // DASHBOARD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/matrix.css') }}">
    <style>
        /* --- Estilos específicos del Dashboard --- */

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

        .log-line {
            font-size: 0.72rem;
            color: var(--green-dim);
            padding: 4px 0;
            border-bottom: 1px solid rgba(0,255,65,0.06);
            display: flex; gap: 12px;
        }
        .log-line .time { color: var(--green-faint); min-width: 80px; }
        .log-line .pts  { color: var(--green); margin-left: auto; }

        /* --- Responsive --- */
        @media (max-width: 640px) {
            .play-btn {
                font-size: 1.8rem;
                padding: 16px 32px;
                letter-spacing: 3px;
            }
            .stat-box {
                padding: 12px 8px;
            }
            .stat-num {
                font-size: 1.8rem;
            }
            .stat-label {
                font-size: 0.55rem;
                letter-spacing: 1px;
            }
        }
    </style>
</head>
<body>
    <div class="scanline-move"></div>
    <canvas id="matrix-canvas"></canvas>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="nav-logo flicker">IMAGUESS</a>
        <div class="nav-status">
            <div class="status-dot"></div>
            <span>SISTEMA ACTIVO</span>
            <span style="color:var(--green-faint);">|</span>
            <span>SESIÓN: <span style="color:var(--green);">{{ auth()->user()->name ?? 'INVITADO' }}</span></span>
        </div>
        <div class="dropdown-wrap" style="position:relative;">
            <button class="profile-btn" onclick="toggleDropdown()" title="Perfil">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#00b32c" stroke-width="1.5">
                    <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                </svg>
            </button>
            <div class="profile-dropdown" id="dropdown">
                <div style="padding:10px 16px; font-size:0.65rem; color:var(--green-faint); letter-spacing:2px; border-bottom:1px solid rgba(0,255,65,0.15);">
                    ROOT@IMAGUESS<span class="blink">_</span>
                </div>
                <a href="{{ route('ranking') }}" class="dropdown-item">&gt; LEADERBOARD</a>
                <a href="{{ route('profile') }}" class="dropdown-item">&gt; MI PERFIL</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">&gt; CERRAR SESIÓN</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main -->
    <div style="position:relative;z-index:10;padding-top:80px;" class="min-h-screen flex flex-col items-center justify-center px-4">

        <div style="font-size:0.75rem; color:var(--green-dim); letter-spacing:3px; margin-bottom:48px; text-align:center;">
            &gt; BIENVENIDO, <span style="color:var(--green);">{{ strtoupper(auth()->user()->name ?? 'OPERADOR') }}</span> — LISTO PARA COMENZAR<span class="blink">_</span>
        </div>

        <!-- Botón JUGAR -->
        <div style="text-align:center; margin-bottom:64px;">
            <a href="{{ route('game') }}" style="text-decoration:none; display:inline-block;">
                <button class="play-btn">
                    <span class="play-icon">▶</span> INICIAR PARTIDA
                </button>
            </a>
            <div style="margin-top:12px; font-size:0.7rem; color:var(--green-faint); letter-spacing:2px;">
                60 SEG · IMÁGENES ALEATORIAS · IA JUDGE
            </div>
        </div>

        <!-- Stats -->
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

        <!-- Historial reciente -->
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
                <span style="color:var(--green-faint);">SIN REGISTROS — JUEGA TU PRIMERA PARTIDA</span>
            </div>
            @endforelse
            <a href="{{ route('ranking') }}" style="display:block; margin-top:12px; font-size:0.7rem; color:var(--green-dim); text-decoration:none; letter-spacing:1px; transition:color 0.2s;"
               onmouseover="this.style.color='var(--green)'" onmouseout="this.style.color='var(--green-dim)'">
                &gt; VER LEADERBOARD COMPLETO →
            </a>
        </div>

        <!-- Footer -->
        <div style="margin-top:40px; font-size:0.6rem; color:var(--green-faint); letter-spacing:3px;">
            IMAGUESS v1.0 &nbsp;|&nbsp; PEXELS API &nbsp;|&nbsp; AZURE VISION &nbsp;|&nbsp; LARAVEL {{ app()->version() ?? '12' }}
        </div>
    </div>

    <script src="{{ asset('js/matrix.js') }}"></script>
</body>
</html>
