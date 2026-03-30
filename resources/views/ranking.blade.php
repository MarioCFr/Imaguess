<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMAGUESS // LEADERBOARD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=VT323&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #00ff41;
            --green-dim: #00b32c;
            --green-dark: #003b0f;
            --green-glow: rgba(0,255,65,0.15);
            --gold: #ffd700;
            --silver: #c0c0c0;
            --bronze: #cd7f32;
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
            opacity: 0.05; z-index: 0;
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
        .terminal-box {
            border: 1px solid var(--green-dim);
            box-shadow: 0 0 20px var(--green-glow), inset 0 0 30px rgba(0,255,65,0.02);
            background: rgba(0,10,2,0.92);
            position: relative;
        }
        .corner { position:absolute; width:14px; height:14px; border-color:var(--green); border-style:solid; }
        .corner-tl { top:-1px; left:-1px; border-width:1px 0 0 1px; }
        .corner-tr { top:-1px; right:-1px; border-width:1px 1px 0 0; }
        .corner-bl { bottom:-1px; left:-1px; border-width:0 0 1px 1px; }
        .corner-br { bottom:-1px; right:-1px; border-width:0 1px 1px 0; }

        /* Leaderboard rows */
        .lb-row {
            display: grid;
            grid-template-columns: 60px 1fr auto auto;
            align-items: center;
            padding: 14px 20px;
            border-bottom: 1px solid rgba(0,255,65,0.08);
            transition: background 0.15s;
            position: relative;
            overflow: hidden;
        }
        .lb-row:last-child { border-bottom: none; }
        .lb-row:hover { background: rgba(0,255,65,0.04); }
        .lb-row.is-current-user { background: rgba(0,255,65,0.06); border-left: 2px solid var(--green); }
        .lb-row::before {
            content: '';
            position: absolute; left:0; top:0; bottom:0; width:0;
            background: linear-gradient(90deg, rgba(0,255,65,0.06), transparent);
            transition: width 0.3s;
        }
        .lb-row:hover::before { width: 100%; }

        .rank-num {
            font-family: 'VT323', monospace;
            font-size: 1.8rem;
            color: var(--green-dim);
            line-height: 1;
        }
        .rank-num.gold   { color: var(--gold);   text-shadow: 0 0 12px rgba(255,215,0,0.4);   }
        .rank-num.silver { color: var(--silver); text-shadow: 0 0 12px rgba(192,192,192,0.3); }
        .rank-num.bronze { color: var(--bronze); text-shadow: 0 0 12px rgba(205,127,50,0.3);  }

        .player-name {
            font-size: 0.9rem;
            color: var(--green);
            letter-spacing: 1px;
        }
        .player-games {
            font-size: 0.65rem;
            color: var(--green-dim);
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .player-score {
            font-family: 'VT323', monospace;
            font-size: 2rem;
            color: var(--green);
            text-shadow: 0 0 8px rgba(0,255,65,0.3);
            text-align: right;
        }
        .score-label {
            font-size: 0.6rem;
            color: var(--green-dim);
            text-align: right;
            letter-spacing: 1px;
        }

        /* Medal icons */
        .medal { font-size: 1.1rem; margin-left: 4px; }

        /* Top 3 special display */
        .podium-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin-bottom: 32px;
        }
        .podium-card {
            border: 1px solid;
            padding: 20px 12px;
            text-align: center;
            position: relative;
            background: rgba(0,5,1,0.8);
        }
        .podium-card.p1 { border-color: var(--gold); box-shadow: 0 0 20px rgba(255,215,0,0.1); order: 2; }
        .podium-card.p2 { border-color: var(--silver); box-shadow: 0 0 12px rgba(192,192,192,0.08); order: 1; }
        .podium-card.p3 { border-color: var(--bronze); box-shadow: 0 0 12px rgba(205,127,50,0.08); order: 3; }
        .podium-rank { font-family:'VT323',monospace; font-size:3rem; line-height:1; }
        .podium-card.p1 .podium-rank { color:var(--gold); }
        .podium-card.p2 .podium-rank { color:var(--silver); }
        .podium-card.p3 .podium-rank { color:var(--bronze); }
        .podium-name { font-size:0.8rem; color:var(--green); margin:8px 0 4px; letter-spacing:1px; word-break:break-all; }
        .podium-pts { font-family:'VT323',monospace; font-size:2rem; }
        .podium-card.p1 .podium-pts { color:var(--gold); }
        .podium-card.p2 .podium-pts { color:var(--silver); }
        .podium-card.p3 .podium-pts { color:var(--bronze); }
        .podium-sublabel { font-size:0.6rem; color:var(--green-dim); letter-spacing:2px; }

        .blink { animation:blink 1s step-end infinite; }
        @keyframes blink { 50%{opacity:0;} }
        .flicker { animation:flicker 10s infinite; }
        @keyframes flicker { 0%,96%,100%{opacity:1;} 97%{opacity:0.5;} 98%{opacity:1;} }

        /* Animated entry */
        .lb-row { animation: rowIn 0.3s ease both; }
        @keyframes rowIn { from{opacity:0;transform:translateX(-10px);} to{opacity:1;transform:none;} }

        /* Tab selector */
        .tab-btn {
            background: transparent;
            border: 1px solid rgba(0,255,65,0.2);
            color: var(--green-dim);
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.7rem;
            padding: 6px 16px;
            cursor: pointer;
            letter-spacing: 2px;
            transition: all 0.2s;
        }
        .tab-btn.active, .tab-btn:hover { border-color:var(--green); color:var(--green); background:rgba(0,255,65,0.05); }
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
                <a href="{{ route('profile') }}" class="dropdown-item">&gt; MI PERFIL</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item" style="width:100%;text-align:left;">
                        &gt; CERRAR SESIÓN
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div style="position:relative;z-index:10;padding-top:72px;min-height:100vh;max-width:720px;margin:0 auto;padding-left:16px;padding-right:16px;padding-bottom:40px;">

        <!-- Header -->
        <div style="text-align:center;margin-bottom:40px;padding-top:24px;">
            <div style="font-family:'VT323',monospace;font-size:3.5rem;color:var(--green);text-shadow:0 0 20px var(--green),0 0 40px rgba(0,255,65,0.2);line-height:1;">
                LEADERBOARD
            </div>
            <div style="font-size:0.7rem;color:var(--green-dim);letter-spacing:4px;margin-top:4px;">
                &gt; TOP OPERADORES — PUNTUACIÓN MÁXIMA<span class="blink">_</span>
            </div>
        </div>

        <!-- Tab selector -->
        <div style="display:flex;gap:8px;margin-bottom:24px;">
            <button class="tab-btn active">GLOBAL</button>
            <button class="tab-btn">ESTA SEMANA</button>
            <button class="tab-btn">MIS MEJORES</button>
        </div>

        <!-- Podium top 3 -->
        <div class="podium-section" style="max-width:100%;">
            @php $top3 = $topScores->take(3); @endphp

            @if($top3->count() >= 2)
            <div class="podium-card p2">
                <div class="podium-rank">#2</div>
                <div class="podium-name">{{ strtoupper($top3[1]->user->name ?? 'ANON') }}</div>
                <div class="podium-pts">{{ $top3[1]->points }}</div>
                <div class="podium-sublabel">PTS</div>
            </div>
            @endif

            @if($top3->count() >= 1)
            <div class="podium-card p1">
                <div style="position:absolute;top:-12px;left:50%;transform:translateX(-50%);font-size:1.4rem;">👑</div>
                <div class="podium-rank">#1</div>
                <div class="podium-name">{{ strtoupper($top3[0]->user->name ?? 'ANON') }}</div>
                <div class="podium-pts">{{ $top3[0]->points }}</div>
                <div class="podium-sublabel">PTS</div>
            </div>
            @endif

            @if($top3->count() >= 3)
            <div class="podium-card p3">
                <div class="podium-rank">#3</div>
                <div class="podium-name">{{ strtoupper($top3[2]->user->name ?? 'ANON') }}</div>
                <div class="podium-pts">{{ $top3[2]->points }}</div>
                <div class="podium-sublabel">PTS</div>
            </div>
            @endif

            {{-- Placeholders if not enough data yet --}}
            @if($top3->count() === 0)
                <div class="podium-card p2"><div class="podium-rank" style="opacity:0.2;">#2</div><div class="podium-name" style="color:#1a5c29;">---</div><div class="podium-pts" style="opacity:0.2;">0</div></div>
                <div class="podium-card p1"><div class="podium-rank" style="opacity:0.2;">#1</div><div class="podium-name" style="color:#1a5c29;">SIN DATOS</div><div class="podium-pts" style="opacity:0.2;">0</div></div>
                <div class="podium-card p3"><div class="podium-rank" style="opacity:0.2;">#3</div><div class="podium-name" style="color:#1a5c29;">---</div><div class="podium-pts" style="opacity:0.2;">0</div></div>
            @endif
        </div>

        <!-- Full ranking table -->
        <div class="terminal-box">
            <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div><div class="corner corner-br"></div>

            <!-- Table header -->
            <div style="display:grid;grid-template-columns:60px 1fr auto auto;padding:10px 20px;border-bottom:1px solid rgba(0,255,65,0.2);font-size:0.6rem;color:var(--green-dim);letter-spacing:3px;">
                <span>RANK</span>
                <span>OPERADOR</span>
                <span style="text-align:right;padding-right:20px;">PARTIDAS</span>
                <span style="text-align:right;">PUNTOS</span>
            </div>

            @forelse($topScores as $index => $score)
            @php
                $rank = $index + 1;
                $rankClass = $rank === 1 ? 'gold' : ($rank === 2 ? 'silver' : ($rank === 3 ? 'bronze' : ''));
                $medal = $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : ($rank === 3 ? '🥉' : ''));
                $isMe = auth()->check() && auth()->id() === $score->user_id;
            @endphp
            <div class="lb-row {{ $isMe ? 'is-current-user' : '' }}" style="animation-delay:{{ $index * 0.05 }}s;">
                <div class="rank-num {{ $rankClass }}">
                    {{ $rank }}{{ $medal }}
                </div>
                <div>
                    <div class="player-name">
                        {{ strtoupper($score->user->name ?? 'ANON') }}
                        @if($isMe) <span style="font-size:0.6rem;color:var(--green-dim);margin-left:6px;">[TÚ]</span> @endif
                    </div>
                    <div class="player-games">{{ $score->user->scores()->count() ?? 0 }} PARTIDAS</div>
                </div>
                <div style="text-align:right;padding-right:20px;font-size:0.7rem;color:var(--green-dim);">
                    {{ $score->user->scores()->count() ?? 0 }}
                </div>
                <div>
                    <div class="player-score">{{ number_format($score->points) }}</div>
                    <div class="score-label">PTS</div>
                </div>
            </div>
            @empty
            <div style="padding:40px;text-align:center;color:#1a5c29;font-size:0.8rem;letter-spacing:2px;">
                &gt; SIN REGISTROS — SÉ EL PRIMERO EN JUGAR
            </div>
            @endforelse
        </div>

        @auth
        <!-- Your position (if not in top 10) -->
        <div style="margin-top:16px;text-align:center;">
            <div style="font-size:0.7rem;color:var(--green-dim);letter-spacing:2px;">
                TU POSICIÓN GLOBAL:
                <span style="color:var(--green);font-size:1rem;font-family:'VT323',monospace;">
                    #{{ auth()->user()->globalRank() ?? '—' }}
                </span>
                &nbsp;|&nbsp;
                MEJOR PUNTUACIÓN:
                <span style="color:var(--green);font-size:1rem;font-family:'VT323',monospace;">
                    {{ auth()->user()->scores()->max('points') ?? 0 }}
                </span>
            </div>
        </div>
        @endauth

        <!-- CTA -->
        <div style="text-align:center;margin-top:32px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('game') }}" style="text-decoration:none;">
                <button style="background:transparent;border:1px solid var(--green);color:var(--green);font-family:'Share Tech Mono',monospace;font-size:0.85rem;padding:12px 36px;cursor:pointer;letter-spacing:3px;transition:all 0.2s;"
                        onmouseover="this.style.background='var(--green)';this.style.color='#000'"
                        onmouseout="this.style.background='transparent';this.style.color='var(--green)'">
                    ▶ JUGAR AHORA
                </button>
            </a>
            <a href="{{ route('dashboard') }}" style="text-decoration:none;">
                <button style="background:transparent;border:1px solid var(--green);color:var(--green);font-family:'Share Tech Mono',monospace;font-size:0.85rem;padding:12px 36px;cursor:pointer;letter-spacing:3px;transition:all 0.2s;"
                        onmouseover="this.style.background='var(--green)';this.style.color='#000'"
                        onmouseout="this.style.background='transparent';this.style.color='var(--green)'">
                    ⌂ DASHBOARD
                </button>
            </a>
        </div>

        <!-- Footer -->
        <div style="margin-top:40px;text-align:center;font-size:0.6rem;color:#1a5c29;letter-spacing:3px;">
            RANKING ACTUALIZADO EN TIEMPO REAL &nbsp;|&nbsp; IMAGUESS v1.0
        </div>
    </div>

    <script>
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

        function toggleDropdown() {
            document.getElementById('dropdown').classList.toggle('open');
        }
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.profile-btn') && !e.target.closest('#dropdown')) {
                const d = document.getElementById('dropdown');
                if (d) d.classList.remove('open');
            }
        });

        // Tab switching (visual only for now)
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
