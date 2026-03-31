/* ============================================================
   IMAGUESS — JavaScript compartido Matrix/Terminal
   Incluir en todas las vistas con:
   <script src="{{ asset('js/matrix.js') }}"></script>
   ============================================================ */

/* --- Lluvia Matrix --- */
(function () {
    const canvas = document.getElementById('matrix-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;

    const cols  = Math.floor(canvas.width / 16);
    const drops = Array(cols).fill(1);
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%^&*()アイウエオカキクケコ';

    function drawMatrix() {
        ctx.fillStyle = 'rgba(0,0,0,0.05)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#00ff41';
        ctx.font = '14px "Share Tech Mono", monospace';

        drops.forEach((y, i) => {
            const char = chars[Math.floor(Math.random() * chars.length)];
            ctx.fillText(char, i * 16, y * 16);
            if (y * 16 > canvas.height && Math.random() > 0.975) drops[i] = 0;
            drops[i]++;
        });
    }

    setInterval(drawMatrix, 50);

    /* Redibujar si cambia el tamaño de ventana */
    window.addEventListener('resize', () => {
        canvas.width  = window.innerWidth;
        canvas.height = window.innerHeight;
    });
})();

/* --- Dropdown de perfil --- */
function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    if (dropdown) dropdown.classList.toggle('open');
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('.profile-btn') && !e.target.closest('#dropdown')) {
        const dropdown = document.getElementById('dropdown');
        if (dropdown) dropdown.classList.remove('open');
    }
});
