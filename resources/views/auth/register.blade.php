<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMAGUESS // REGISTRO</title>
    <meta name="description" content="Crea tu cuenta en IMAGUESS para guardar puntuaciones, competir en el ranking global y acceder a tu historial de partidas.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(["resources/css/app.css"])
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/matrix.css') }}">
    <style>
        body { overflow: hidden; }
        #matrix-canvas { opacity: 0.07; }
        .terminal-box::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,255,65,0.04) 0%, transparent 50%);
            pointer-events: none;
        }
        .glitch { animation: flicker 8s infinite; }
    </style>
</head>
<body>
    <div class="scanline-move"></div>
    <canvas id="matrix-canvas"></canvas>

    <div style="position:relative;z-index:10;" class="min-h-screen flex flex-col items-center justify-center px-4">

        <!-- Logo -->
        <div class="text-center mb-8 glitch">
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

            <div style="font-size:0.7rem; color:var(--green-dim); letter-spacing:3px; margin-bottom:24px;">
                &gt; REGISTRO_NUEVO_USUARIO<span class="blink">_</span>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Nombre -->
                <div>
                    <div class="label-text">&gt; NOMBRE_USUARIO</div>
                    <input class="terminal-input" type="text" name="name"
                           placeholder="operador_01"
                           value="{{ old('name') }}" required autofocus autocomplete="name">
                    @error('name')
                        <div style="color:#ff4040; font-size:0.7rem; margin-top:4px;">[ERR] {{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <div class="label-text">&gt; EMAIL_ADDRESS</div>
                    <input class="terminal-input" type="email" name="email"
                           placeholder="usuario@sistema.net"
                           value="{{ old('email') }}" required autocomplete="username">
                    @error('email')
                        <div style="color:#ff4040; font-size:0.7rem; margin-top:4px;">[ERR] {{ $message }}</div>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <div class="label-text">&gt; PASSWORD_HASH</div>
                    <input class="terminal-input" type="password" name="password"
                           placeholder="••••••••" required autocomplete="new-password">
                    @error('password')
                        <div style="color:#ff4040; font-size:0.7rem; margin-top:4px;">[ERR] {{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirmar contraseña -->
                <div>
                    <div class="label-text">&gt; CONFIRM_PASSWORD</div>
                    <input class="terminal-input" type="password" name="password_confirmation"
                           placeholder="••••••••" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn-primary mt-4">
                    &gt; CREAR CUENTA
                </button>
            </form>

            <div style="margin-top:24px; text-align:center; font-size:0.75rem; color:var(--green-dim);">
                <a href="{{ route('login') }}"
                   style="color:var(--green-dim); text-decoration:none; transition:color 0.2s;"
                   onmouseover="this.style.color='var(--green)'"
                   onmouseout="this.style.color='var(--green-dim)'">
                    [ YA TENGO CUENTA ]
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
        <div style="margin-top:32px; font-size:0.65rem; color:var(--green-faint); letter-spacing:2px;">
            SYS:ONLINE &nbsp;|&nbsp; API:CONNECTED &nbsp;|&nbsp; DB:OK
        </div>
    </div>

    <script src="{{ asset('js/matrix.js') }}"></script>
</body>
</html>
