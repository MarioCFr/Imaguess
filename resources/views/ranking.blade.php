<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMAGUESS // LEADERBOARD</title>
    <meta name="description" content="Ranking global de IMAGUESS. Consulta las mejores puntuaciones y compite por el primer puesto en el leaderboard.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/matrix.css') }}">
    <style>
        /* --- Estilos específicos del Ranking --- */

        /* Filas del leaderboard */
        .lb-row {
            display: grid;
            grid-template-columns: 60px 1fr auto auto;
            align-items: center;
            padding: 14px 20px;
            border-bottom: 1px solid rgba(0,255,65,0.08);
            transition: background 0.15s;
            position: relative;
            overflow: hidden;
            animation: rowIn 0.3s ease both;
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
        @keyframes rowIn { from{opacity:0;transform:translateX(-10px);} to{opacity:1;transform:none;} }

        .rank-num {
            font-family: 'VT323', monospace;
            font-size: 1.8rem;
            color: var(--green-dim);
            line-height: 1;
        }
        .rank-num.gold   { color: var(--gold);   text-shadow: 0 0 12px rgba(255,215,0,0.4);   }
        .rank-num.silver { color: var(--silver); text-shadow: 0 0 12px rgba(192,192,192,0.3); }
        .rank-num.bronze { color: var(--bronze); text-shadow: 0 0 12px rgba(205,127,50,0.3);  }

        .player-name  { font-size: 0.9rem; color: var(--green); letter-spacing: 1px; }
        .player-games { font-size: 0.65rem; color: var(--green-dim); letter-spacing: 1px; margin-top: 2px; }
        .player-score { font-family: 'VT323', monospace; font-size: 2rem; color: var(--green); text-shadow: 0 0 8px rgba(0,255,65,0.3); text-align: right; }
        .score-label  { font-size: 0.6rem; color: var(--green-dim); text-align: right; letter-spacing: 1px; }

        /* Pódium top 3 */
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
        .podium-card.p1 { border-color: var(--gold);   box-shadow: 0 0 20px rgba(255,215,0,0.1);    order: 2; }
        .podium-card.p2 { border-color: var(--silver); box-shadow: 0 0 12px rgba(192,192,192,0.08); order: 1; }
        .podium-card.p3 { border-color: var(--bronze); box-shadow: 0 0 12px rgba(205,127,50,0.08);  order: 3; }

        .podium-rank { font-family:'VT323',monospace; font-size:3rem; line-height:1; }
        .podium-card.p1 .podium-rank { color:var(--gold);   }
        .podium-card.p2 .podium-rank { color:var(--silver); }
        .podium-card.p3 .podium-rank { color:var(--bronze); }

        .podium-name { font-size:0.8rem; color:var(--green); margin:8px 0 4px; letter-spacing:1px; word-break:break-all; }
        .podium-pts  { font-family:'VT323',monospace; font-size:2rem; }
        .podium-card.p1 .podium-pts { color:var(--gold);   }
        .podium-card.p2 .podium-pts { color:var(--silver); }
        .podium-card.p3 .podium-pts { color:var(--bronze); }
        .podium-sublabel { font-size:0.6rem; color:var(--green-dim); letter-spacing:2px; }

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
        .tab-btn.active, .tab-btn:hover {
            border-color: var(--green);
            color: var(--green);
            background: rgba(0,255,65,0.05);
        }

        /* --- Responsive --- */
        @media (max-width: 640px) {
            /* Pódium: columna única en lugar de 3 */
            .podium-section {
                grid-template-columns: 1fr;
            }
            /* Reordenar pódium: #1 arriba, #2, #3 */
            .podium-card.p1 { order: 1; }
            .podium-card.p2 { order: 2; }
            .podium-card.p3 { order: 3; }

            /* Tabla: ocultar columna PARTIDAS, queda RANK + OPERADOR + PUNTOS */
            .lb-row {
                grid-template-columns: 48px 1fr auto;
            }
            .lb-row > div:nth-child(3) {
                display: none;
            }

            /* Cabecera de tabla igual */
            .lb-header {
                grid-template-columns: 48px 1fr auto;
            }

            /* Título más pequeño */
            .ranking-title {
                font-size: 2.2rem;
            }

            /* Tabs en móvil más pequeños */
            .tab-btn {
                font-size: 0.6rem;
                padding: 6px 10px;
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
                <a href="{{ route('dashboard') }}" class="dropdown-item">&gt; DASHBOARD</a>
                <a href="{{ route('profile') }}"   class="dropdown-item">&gt; MI PERFIL</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">&gt; CERRAR SESIÓN</button>
                </form>
            </div>
        </div>
    </nav>

    <div style="position:relative;z-index:10;padding-top:72px;min-height:100vh;max-width:720px;margin:0 auto;padding-left:16px;padding-right:16px;padding-bottom:40px;">

        <!-- Header -->
        <div style="text-align:center;margin-bottom:40px;padding-top:24px;">
            <div class="ranking-title" style="font-family:'VT323',monospace;font-size:3.5rem;color:var(--green);text-shadow:0 0 20px var(--green),0 0 40px rgba(0,255,65,0.2);line-height:1;">
                LEADERBOARD
            </div>
            <div style="font-size:0.7rem;color:var(--green-dim);letter-spacing:4px;margin-top:4px;">
                &gt; TOP OPERADORES — PUNTUACIÓN MÁXIMA<span class="blink">_</span>
            </div>
        </div>

        <!-- Tabs -->
        <div style="display:flex;gap:8px;margin-bottom:24px;">
            <button class="tab-btn active">GLOBAL</button>
            <button class="tab-btn">ESTA SEMANA</button>
            <button class="tab-btn">MIS MEJORES</button>
        </div>

        <!-- Pódium top 3 -->
        <div class="podium-section">
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

            @if($top3->count() === 0)
                <div class="podium-card p2"><div class="podium-rank" style="opacity:0.2;">#2</div><div class="podium-name" style="color:var(--green-faint);">---</div><div class="podium-pts" style="opacity:0.2;">0</div></div>
                <div class="podium-card p1"><div class="podium-rank" style="opacity:0.2;">#1</div><div class="podium-name" style="color:var(--green-faint);">SIN DATOS</div><div class="podium-pts" style="opacity:0.2;">0</div></div>
                <div class="podium-card p3"><div class="podium-rank" style="opacity:0.2;">#3</div><div class="podium-name" style="color:var(--green-faint);">---</div><div class="podium-pts" style="opacity:0.2;">0</div></div>
            @endif
        </div>

        <!-- Tabla completa -->
        <div class="terminal-box">
            <div class="corner corner-tl"></div><div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div><div class="corner corner-br"></div>

            <div class="lb-header" style="display:grid;grid-template-columns:60px 1fr auto auto;padding:10px 20px;border-bottom:1px solid rgba(0,255,65,0.2);font-size:0.6rem;color:var(--green-dim);letter-spacing:3px;">
                <span>RANK</span>
                <span>OPERADOR</span>
                <span style="text-align:right;padding-right:20px;">PARTIDAS</span>
                <span style="text-align:right;">PUNTOS</span>
            </div>

            @forelse($topScores as $index => $score)
            @php
                $rank      = $index + 1;
                $rankClass = $rank === 1 ? 'gold' : ($rank === 2 ? 'silver' : ($rank === 3 ? 'bronze' : ''));
                $medal     = $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : ($rank === 3 ? '🥉' : ''));
                $isMe      = auth()->check() && auth()->id() === $score->user_id;
            @endphp
            <div class="lb-row {{ $isMe ? 'is-current-user' : '' }}" style="animation-delay:{{ $index * 0.05 }}s;">
                <div class="rank-num {{ $rankClass }}">{{ $rank }}{{ $medal }}</div>
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
            <div style="padding:40px;text-align:center;color:var(--green-faint);font-size:0.8rem;letter-spacing:2px;">
                &gt; SIN REGISTROS — SÉ EL PRIMERO EN JUGAR
            </div>
            @endforelse
        </div>

        @auth
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
                <button class="btn-primary" style="width:auto;padding:12px 36px;letter-spacing:3px;">▶ JUGAR AHORA</button>
            </a>
            <a href="{{ route('dashboard') }}" style="text-decoration:none;">
                <button class="btn-primary" style="width:auto;padding:12px 36px;letter-spacing:3px;">⌂ DASHBOARD</button>
            </a>
        </div>

        <div style="margin-top:40px;text-align:center;font-size:0.6rem;color:var(--green-faint);letter-spacing:3px;">
            RANKING ACTUALIZADO EN TIEMPO REAL &nbsp;|&nbsp; IMAGUESS v1.0
        </div>
    </div>

    <script src="{{ asset('js/matrix.js') }}"></script>
    <script>
        /* Tabs (visual, sin lógica backend por ahora) */
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
