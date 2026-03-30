<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMAGUESS // PARTIDA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=VT323&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #00ff41;
            --green-dim: #00b32c;
            --green-dark: #003b0f;
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
            overflow: hidden;
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
            background: radial-gradient(ellipse at center, transparent 50%, rgba(0,0,0,0.5) 100%);
            pointer-events: none; z-index: 99;
        }
        .scanline-move {
            position: fixed; width:100%; height:3px;
            background: linear-gradient(transparent, rgba(0,255,65,0.06), transparent);
            animation: scanline 7s linear infinite;
            pointer-events: none; z-index: 101;
        }
        @keyframes scanline { 0%{top:-4px;} 100%{top:100vh;} }

        /* HUD Top bar */
        .hud-bar {
            position: fixed; top:0; left:0; right:0; z-index:50;
            background: rgba(0,0,0,0.9);
            border-bottom: 1px solid rgba(0,255,65,0.3);
            padding: 10px 24px;
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px;
        }
        .hud-logo {
            font-family: 'VT323', monospace;
            font-size: 1.6rem;
            color: var(--green);
            text-shadow: 0 0 8px var(--green);
        }
        .hud-score {
            font-family: 'VT323', monospace;
            font-size: 1.8rem;
            color: var(--green);
            text-shadow: 0 0 10px rgba(0,255,65,0.5);
        }
        .hud-timer-wrap {
            display: flex; flex-direction: column; align-items: center;
        }
        .hud-timer {
            font-family: 'VT323', monospace;
            font-size: 2.8rem;
            line-height: 1;
            transition: color 0.3s;
        }
        .hud-timer.danger { color: var(--red); text-shadow: 0 0 12px rgba(255,0,64,0.6); animation: timerDanger 0.5s ease infinite; }
        @keyframes timerDanger { 0%,100%{opacity:1;} 50%{opacity:0.6;} }
        .hud-timer-label { font-size: 0.6rem; color: var(--green-dim); letter-spacing: 2px; }

        .timer-bar-wrap {
            position: fixed; top: 56px; left:0; right:0; z-index:49;
            height: 3px;
            background: rgba(0,255,65,0.1);
        }
        .timer-bar {
            height: 100%;
            background: var(--green);
            box-shadow: 0 0 8px var(--green);
            width: 100%;
            transition: width 1s linear, background 0.3s;
        }
        .timer-bar.danger { background: var(--red); box-shadow: 0 0 8px var(--red); }

        /* Image area */
        .img-container {
            position: relative;
            border: 1px solid var(--green-dim);
            box-shadow: 0 0 30px var(--green-glow);
            overflow: hidden;
            background: #000;
        }
        .img-container::after {
            content: '';
            position: absolute; inset: 0;
            background: repeating-linear-gradient(0deg, transparent, transparent 3px, rgba(0,0,0,0.07) 3px, rgba(0,0,0,0.07) 4px);
            pointer-events: none;
        }
        .img-container img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
            filter: saturate(0.8) contrast(1.1);
            transition: opacity 0.4s;
        }
        /* Corner decorations */
        .corner { position:absolute; width:16px; height:16px; border-color:var(--green); border-style:solid; z-index:5; }
        .corner-tl { top:0; left:0; border-width:2px 0 0 2px; }
        .corner-tr { top:0; right:0; border-width:2px 2px 0 0; }
        .corner-bl { bottom:0; left:0; border-width:0 0 2px 2px; }
        .corner-br { bottom:0; right:0; border-width:0 2px 2px 0; }

        /* Crosshair overlay */
        .crosshair {
            position: absolute; inset:0; pointer-events:none; z-index:4;
            display:flex; align-items:center; justify-content:center;
        }
        .crosshair::before, .crosshair::after { content:''; position:absolute; background:rgba(0,255,65,0.15); }
        .crosshair::before { width:1px; height:100%; left:50%; }
        .crosshair::after { height:1px; width:100%; top:50%; }

        /* Answer input */
        .answer-input {
            background: rgba(0,10,2,0.9);
            border: 1px solid var(--green-dim);
            color: var(--green);
            font-family: 'Share Tech Mono', monospace;
            font-size: 1.1rem;
            padding: 12px 16px;
            outline: none;
            width: 100%;
            letter-spacing: 2px;
            transition: all 0.2s;
            caret-color: var(--green);
        }
        .answer-input:focus { border-color: var(--green); box-shadow: 0 0 16px var(--green-glow); }
        .answer-input::placeholder { color: #1a5c29; }

        .submit-btn {
            background: transparent;
            border: 1px solid var(--green);
            color: var(--green);
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.9rem;
            padding: 12px 24px;
            cursor: pointer;
            letter-spacing: 2px;
            white-space: nowrap;
            transition: all 0.2s;
            overflow: hidden; position: relative;
        }
        .submit-btn::before { content:''; position:absolute; inset:0; background:var(--green); transform:translateX(-100%); transition:transform 0.2s; z-index:-1; }
        .submit-btn:hover { color:#000; }
        .submit-btn:hover::before { transform:translateX(0); }

        /* Hint button */
        .hint-btn {
            background: transparent;
            border: 1px solid rgba(0,255,65,0.3);
            color: var(--green-dim);
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.75rem;
            padding: 8px 16px;
            cursor: pointer;
            letter-spacing: 1px;
            transition: all 0.2s;
        }
        .hint-btn:hover { border-color: var(--green-dim); color: var(--green); }
        .hint-btn:disabled { opacity: 0.3; cursor: not-allowed; }

        /* Feedback flash */
        .feedback-overlay {
            position: fixed; inset:0; z-index:200;
            display:flex; align-items:center; justify-content:center;
            pointer-events:none;
            opacity:0;
            transition: opacity 0.2s;
        }
        .feedback-overlay.show { opacity:1; }
        .feedback-text {
            font-family: 'VT323', monospace;
            font-size: 5rem;
            letter-spacing: 8px;
            text-shadow: 0 0 40px currentColor;
            animation: feedbackPop 0.4s ease-out;
        }
        @keyframes feedbackPop { 0%{transform:scale(0.5);opacity:0;} 50%{transform:scale(1.1);} 100%{transform:scale(1);opacity:1;} }
        .feedback-correct { color: var(--green); }
        .feedback-wrong { color: var(--red); }

        /* Hint reveal */
        .hint-display {
            font-size: 0.8rem; color: var(--green-dim);
            letter-spacing: 3px; min-height: 20px;
            padding: 6px 0;
        }
        .hint-char { display:inline-block; margin:0 2px; color:var(--green); }

        /* Image counter */
        .img-counter { font-size:0.7rem; color:var(--green-dim); letter-spacing:2px; }

        .blink { animation:blink 1s step-end infinite; }
        @keyframes blink { 50%{opacity:0;} }

        /* Transition for new image */
        .img-loading { filter: brightness(0) !important; }

        /* Game over overlay */
        .gameover-overlay {
            position: fixed; inset:0; z-index:300;
            background: rgba(0,0,0,0.95);
            display:flex; align-items:center; justify-content:center;
            flex-direction: column;
            display: none;
        }
        .gameover-overlay.show { display:flex; }
    </style>
</head>
<body>
    <div class="scanline-move"></div>

    <!-- HUD -->
    <div class="hud-bar">
        <div class="hud-logo">IMAGUESS</div>
        <div style="display:flex;flex-direction:column;align-items:center;">
            <div style="font-size:0.6rem;color:var(--green-dim);letter-spacing:2px;">PUNTUACIÓN</div>
            <div class="hud-score" id="score-display">0</div>
        </div>
        <div class="hud-timer-wrap">
            <div class="hud-timer" id="timer-display">60</div>
            <div class="hud-timer-label">SEGUNDOS</div>
        </div>
        <div style="font-size:0.7rem;color:var(--green-dim);text-align:right;">
            <div class="img-counter">IMAGEN <span id="img-count">1</span></div>
            <div style="font-size:0.6rem;color:#1a5c29;margin-top:2px;">{{ auth()->user()->name ?? 'INVITADO' }}</div>
        </div>
    </div>
    <!-- Timer bar -->
    <div class="timer-bar-wrap">
        <div class="timer-bar" id="timer-bar"></div>
    </div>

    <!-- Feedback overlay -->
    <div class="feedback-overlay" id="feedback-overlay">
        <div class="feedback-text" id="feedback-text"></div>
    </div>

    <!-- Game Over overlay -->
    <div class="gameover-overlay" id="gameover-overlay">
        <div style="font-family:'VT323',monospace;font-size:1rem;color:var(--green-dim);letter-spacing:4px;margin-bottom:8px;">TIEMPO AGOTADO</div>
        <div style="font-family:'VT323',monospace;font-size:6rem;color:var(--green);text-shadow:0 0 30px var(--green);line-height:1;" id="final-score">0</div>
        <div style="font-family:'VT323',monospace;font-size:1.5rem;color:var(--green-dim);letter-spacing:4px;margin-bottom:40px;">PUNTOS</div>
        <div style="display:flex;gap:16px;">
            <a href="{{ route('game') }}" style="text-decoration:none;">
                <button style="background:transparent;border:1px solid var(--green);color:var(--green);font-family:'Share Tech Mono',monospace;font-size:0.85rem;padding:12px 28px;cursor:pointer;letter-spacing:2px;transition:all 0.2s;"
                        onmouseover="this.style.background='var(--green)';this.style.color='#000'"
                        onmouseout="this.style.background='transparent';this.style.color='var(--green)'">
                    ▶ NUEVA PARTIDA
                </button>
            </a>
            <a href="{{ route('ranking') }}" style="text-decoration:none;">
                <button style="background:transparent;border:1px solid rgba(0,255,65,0.4);color:var(--green-dim);font-family:'Share Tech Mono',monospace;font-size:0.85rem;padding:12px 28px;cursor:pointer;letter-spacing:2px;transition:all 0.2s;"
                        onmouseover="this.style.borderColor='var(--green-dim)';this.style.color='var(--green)'"
                        onmouseout="this.style.borderColor='rgba(0,255,65,0.4)';this.style.color='var(--green-dim)'">
                    LEADERBOARD
                </button>
            </a>
        </div>
    </div>

    <!-- Main game area -->
    <div style="position:relative;z-index:10;padding-top:68px;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding-left:16px;padding-right:16px;gap:16px;">

        <!-- Image container -->
        <div class="img-container" style="width:100%;max-width:580px;height:320px;position:relative;" id="img-container">
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>
            <div class="crosshair"></div>
            <img id="game-image"
                 src="https://images.pexels.com/photos/1108099/pexels-photo-1108099.jpeg?auto=compress&cs=tinysrgb&w=600"
                 alt="¿Qué ves?">
            <!-- Image ID watermark -->
            <div style="position:absolute;bottom:8px;right:10px;font-size:0.6rem;color:rgba(0,255,65,0.3);z-index:6;letter-spacing:1px;" id="img-id">
                IMG:001
            </div>
        </div>

        <!-- Hint display -->
        <div class="hint-display" id="hint-display" style="width:100%;max-width:580px;">
            &gt; ESPERANDO ENTRADA<span class="blink">_</span>
        </div>

        <!-- Answer row -->
        <div style="width:100%;max-width:580px;">
            <div style="font-size:0.65rem;color:var(--green-dim);letter-spacing:3px;margin-bottom:6px;">&gt; INTRODUCE_RESPUESTA:</div>
            <div style="display:flex;gap:8px;">
                <input type="text" class="answer-input" id="answer-input"
                       placeholder="escribe lo que la IA ve..."
                       autocomplete="off" autocorrect="off" spellcheck="false">
                <button class="submit-btn" onclick="submitAnswer()">ENVIAR</button>
            </div>
        </div>

        <!-- Hint buttons -->
        <div style="width:100%;max-width:580px;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;gap:8px;">
                <button class="hint-btn" id="hint1-btn" onclick="useHint(1)">
                    💡 PISTA 1 (letras)
                </button>
                <button class="hint-btn" id="hint2-btn" onclick="useHint(2)">
                    💡 PISTA 2 (categoría)
                </button>
            </div>
            <div style="font-size:0.65rem;color:#1a5c29;letter-spacing:1px;">
                PISTAS USADAS: <span id="hints-used" style="color:var(--green-dim);">0</span>/2
            </div>
        </div>
    </div>

    <script>
        let score = 0;
        let timeLeft = 60;
        let imgCount = 1;
        let hintsUsed = 0;
        let currentAnswers = [];
        let gameActive = true;
        let imagesGuessed = 0;

        const timerDisplay = document.getElementById('timer-display');
        const timerBar     = document.getElementById('timer-bar');
        const scoreDisplay = document.getElementById('score-display');

        // Cargar primera imagen al arrancar
        loadNextImage();

        // Timer
        const timer = setInterval(() => {
            if (!gameActive) return;
            timeLeft--;
            timerDisplay.textContent = timeLeft;
            timerBar.style.width = (timeLeft / 60 * 100) + '%';
            if (timeLeft <= 10) {
                timerDisplay.classList.add('danger');
                timerBar.classList.add('danger');
            }
            if (timeLeft <= 0) {
                clearInterval(timer);
                endGame();
            }
        }, 1000);

        function loadNextImage() {
            const img = document.getElementById('game-image');
            img.classList.add('img-loading');
            document.getElementById('hint1-btn').disabled = false;
            document.getElementById('hint2-btn').disabled = false;
            document.getElementById('hints-used').textContent = '0';
            document.getElementById('answer-input').value = '';
            document.getElementById('hint-display').innerHTML =
                '&gt; ANALIZANDO IMAGEN<span style="animation:blink 1s step-end infinite;display:inline-block;">_</span>';

            fetch('/game/next-image')
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        document.getElementById('hint-display').textContent = '> ERROR AL CARGAR IMAGEN';
                        return;
                    }
                    currentAnswers = data.answers.map(a => a.toLowerCase());
                    img.src = data.image_url;
                    img.classList.remove('img-loading');
                    document.getElementById('img-id').textContent = 'IMG:' + String(imgCount).padStart(3, '0');
                    document.getElementById('hint-display').innerHTML =
                        '&gt; ESPERANDO ENTRADA<span style="animation:blink 1s step-end infinite;display:inline-block;">_</span>';
                    img.dataset.hint1 = data.hint1;
                    img.dataset.hint2 = data.hint2;
                    hintsUsed = 0;
                })
                .catch(() => {
                    document.getElementById('hint-display').textContent = '> ERROR DE CONEXIÓN';
                });
        }

        function submitAnswer() {
            const input  = document.getElementById('answer-input');
            const answer = input.value.trim().toLowerCase();
            if (!answer || !gameActive || currentAnswers.length === 0) return;

            const correct = currentAnswers.some(a => a.includes(answer) || answer.includes(a));

            if (correct) {
                const pts = hintsUsed === 0 ? 100 : hintsUsed === 1 ? 60 : 30;
                score += pts;
                imagesGuessed++;
                scoreDisplay.textContent = score;
                showFeedback(true, '+' + pts);
                imgCount++;
                document.getElementById('img-count').textContent = imgCount;
                setTimeout(loadNextImage, 900);
            } else {
                showFeedback(false, 'ERROR');
                input.value = '';
                input.style.borderColor = 'var(--red)';
                setTimeout(() => { input.style.borderColor = 'var(--green-dim)'; }, 600);
            }
        }

        function showFeedback(correct, text) {
            const overlay      = document.getElementById('feedback-overlay');
            const feedbackText = document.getElementById('feedback-text');
            feedbackText.textContent = text;
            feedbackText.className   = 'feedback-text ' + (correct ? 'feedback-correct' : 'feedback-wrong');
            overlay.classList.add('show');
            setTimeout(() => overlay.classList.remove('show'), 700);
        }

        function useHint(hintNum) {
            if (hintsUsed >= 2 || currentAnswers.length === 0) return;
            hintsUsed++;
            document.getElementById('hints-used').textContent = hintsUsed;
            const img         = document.getElementById('game-image');
            const hintDisplay = document.getElementById('hint-display');

            if (hintNum === 1) {
                hintDisplay.innerHTML = '&gt; PISTA 1: <span style="color:var(--green);letter-spacing:6px;">'
                    + (img.dataset.hint1 || '_ _ _').toUpperCase() + '</span>';
                document.getElementById('hint1-btn').disabled = true;
            } else {
                hintDisplay.innerHTML = '&gt; PISTA 2: <span style="color:var(--green);">'
                    + (img.dataset.hint2 || 'SIN PISTA') + '</span>';
                document.getElementById('hint2-btn').disabled = true;
            }
        }

        function endGame() {
            gameActive = false;
            document.getElementById('final-score').textContent = score;
            document.getElementById('gameover-overlay').classList.add('show');

            @auth
            fetch('/game/save-score', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ points: score, images_guessed: imagesGuessed })
            });
            @endauth
        }

        document.getElementById('answer-input').addEventListener('keydown', e => {
            if (e.key === 'Enter') submitAnswer();
        });

        // Matrix rain
        const canvas = document.createElement('canvas');
        canvas.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;opacity:0.04;z-index:0;pointer-events:none;';
        document.body.prepend(canvas);
        const ctx    = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        const cols  = Math.floor(canvas.width / 16);
        const drops = Array(cols).fill(1);
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%アイウエオ';
        setInterval(() => {
            ctx.fillStyle = 'rgba(0,0,0,0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#00ff41';
            ctx.font = '14px monospace';
            drops.forEach((y, i) => {
                ctx.fillText(chars[Math.floor(Math.random() * chars.length)], i * 16, y * 16);
                if (y * 16 > canvas.height && Math.random() > 0.975) drops[i] = 0;
                drops[i]++;
            });
        }, 50);
    </script>
</body>
</html>
