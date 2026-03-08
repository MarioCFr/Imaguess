<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMAGUESS // LOGIN</title>
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
            overflow: hidden;
            position: relative;
        }
        /* Scanlines */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0,0,0,0.15) 2px,
                rgba(0,0,0,0.15) 4px
            );
            pointer-events: none;
            z-index: 100;
        }
        /* Vignette */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at center, transparent 60%, rgba(0,0,0,0.7) 100%);
            pointer-events: none;
            z-index: 99;
        }
        /* Matrix rain canvas */
        #matrix-canvas {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            opacity: 0.07;
            z-index: 0;
        }
        .terminal-box {
            border: 1px solid var(--green-dim);
            box-shadow: 0 0 20px var(--green-glow), inset 0 0 20px rgba(0,255,65,0.03);
            background: rgba(0,10,2,0.92);
            position: relative;
        }
        .terminal-box::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,255,65,0.04) 0%, transparent 50%);
            pointer-events: none;
        }
        .terminal-input {
            background: transparent;
            border: none;
            border-bottom: 1px solid var(--green-dim);
            color: var(--green);
            font-family: 'Share Tech Mono', monospace;
            font-size: 1rem;
            outline: none;
            width: 100%;
            padding: 8px 4px;
            transition: border-color 0.2s;
            caret-color: var(--green);
        }
        .terminal-input:focus {
            border-bottom-color: var(--green);
            box-shadow: 0 2px 8px rgba(0,255,65,0.2);
        }
        .terminal-input::placeholder { color: #1a5c29; }
        .btn-primary {
            background: transparent;
            border: 1px solid var(--green);
            color: var(--green);
            font-family: 'Share Tech Mono', monospace;
            font-size: 1rem;
            padding: 10px 0;
            width: 100%;
            cursor: pointer;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--green);
            transform: translateX(-100%);
            transition: transform 0.2s;
            z-index: -1;
        }
        .btn-primary:hover {
            color: #000;
            box-shadow: 0 0 20px var(--green-glow);
        }
        .btn-primary:hover::before { transform: translateX(0); }

        .glitch {
            position: relative;
            animation: flicker 8s infinite;
        }
        @keyframes flicker {
            0%, 97%, 100% { opacity: 1; }
            98% { opacity: 0.4; }
            99% { opacity: 1; }
        }
        .blink { animation: blink 1s step-end infinite; }
        @keyframes blink { 50% { opacity: 0; } }
        .scanline-move {
            position: fixed;
            width: 100%;
            height: 4px;
            background: linear-gradient(transparent, rgba(0,255,65,0.08), transparent);
            animation: scanline 6s linear infinite;
            pointer-events: none;
            z-index: 101;
        }
        @keyframes scanline {
            0% { top: -4px; }
            100% { top: 100vh; }
        }
        .label-text {
            font-size: 0.7rem;
            color: var(--green-dim);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .corner {
            position: absolute;
            width: 10px;
            height: 10px;
            border-color: var(--green);
            border-style: solid;
        }
        .corner-tl { top: -1px; left: -1px; border-width: 1px 0 0 1px; }
        .corner-tr { top: -1px; right: -1px; border-width: 1px 1px 0 0; }
        .corner-bl { bottom: -1px; left: -1px; border-width: 0 0 1px 1px; }
        .corner-br { bottom: -1px; right: -1px; border-width: 0 1px 1px 0; }
    </style>
</head>
<body>
    <div class="scanline-move"></div>
    <canvas id="matrix-canvas"></canvas>

    <div style="position:relative;z-index:10;" class="min-h-screen flex flex-col items-center justify-center px-4">

        <!-- Logo -->
        <div class="text-center mb-10 glitch">
            <div style="font-family:'VT323',monospace; font-size:4rem; color:var(--green); line-height:1; text-shadow: 0 0 20px var(--green), 0 0 40px rgba(0,255,65,0.3);">
                IMAGUESS
            </div>
            <div style="color:var(--green-dim); font-size:0.75rem; letter-spacing:6px;">
                IMAGE RECOGNITION CHALLENGE v1.0
            </div>
        </div>

        <!-- Terminal box -->
        <div class="terminal-box w-full max-w-sm p-8" style="position:relative;">
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>

            <!-- Header -->
            <div style="font-size:0.7rem; color:var(--green-dim); letter-spacing:3px; margin-bottom:24px;">
                &gt; AUTENTICACIÓN DE USUARIO<span class="blink">_</span>
            </div>

            {{-- Si hay errores de login --}}
            @if(session('error'))
            <div style="border:1px solid #ff0040; color:#ff0040; padding:8px; font-size:0.75rem; margin-bottom:16px; letter-spacing:1px;">
                [ERR] {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <div class="label-text">&gt; EMAIL_ADDRESS</div>
                    <input class="terminal-input" type="email" name="email"
                           placeholder="usuario@sistema.net"
                           value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div style="color:#ff4040; font-size:0.7rem; margin-top:4px;">[ERR] {{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <div class="label-text">&gt; PASSWORD_HASH</div>
                    <input class="terminal-input" type="password" name="password"
                           placeholder="••••••••" required>
                    @error('password')
                        <div style="color:#ff4040; font-size:0.7rem; margin-top:4px;">[ERR] {{ $message }}</div>
                    @enderror
                </div>

                <div style="display:flex; align-items:center; gap:8px; font-size:0.75rem; color:var(--green-dim);">
                    <input type="checkbox" name="remember" id="remember"
                           style="accent-color:var(--green);">
                    <label for="remember">MANTENER SESIÓN ACTIVA</label>
                </div>

                <button type="submit" class="btn-primary mt-4">
                    &gt; INICIAR SESIÓN
                </button>
            </form>

            <!-- Links -->
            <div style="margin-top:24px; text-align:center; font-size:0.75rem; color:var(--green-dim);">
                <a href="{{ route('register') }}"
                   style="color:var(--green-dim); text-decoration:none; transition:color 0.2s;"
                   onmouseover="this.style.color='var(--green)'"
                   onmouseout="this.style.color='var(--green-dim)'">
                    [ CREAR CUENTA NUEVA ]
                </a>
                <span style="margin:0 12px; opacity:0.3;">|</span>
                <a href="{{ route('game.guest') }}"
                   style="color:var(--green-dim); text-decoration:none; transition:color 0.2s;"
                   onmouseover="this.style.color='var(--green)'"
                   onmouseout="this.style.color='var(--green-dim)'">
                    [ ENTRAR COMO INVITADO ]
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top:32px; font-size:0.65rem; color:#1a5c29; letter-spacing:2px;">
            SYS:ONLINE &nbsp;|&nbsp; API:CONNECTED &nbsp;|&nbsp; DB:OK
        </div>
    </div>

    <script>
        // Matrix rain
        const canvas = document.getElementById('matrix-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        const cols = Math.floor(canvas.width / 16);
        const drops = Array(cols).fill(1);
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%^&*()アイウエオカキクケコ';
        function drawMatrix() {
            ctx.fillStyle = 'rgba(0,0,0,0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#00ff41';
            ctx.font = '14px Share Tech Mono';
            drops.forEach((y, i) => {
                const char = chars[Math.floor(Math.random() * chars.length)];
                ctx.fillText(char, i * 16, y * 16);
                if (y * 16 > canvas.height && Math.random() > 0.975) drops[i] = 0;
                drops[i]++;
            });
        }
        setInterval(drawMatrix, 50);
    </script>
</body>
</html>
