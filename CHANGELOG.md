# Changelog — IMAGUESS

Todos los cambios relevantes del proyecto se documentan en este archivo.

## [1.0.0] — 2025

### Añadido
- Juego de adivinanza de imágenes con temporizador de 60 segundos
- Integración con **Pexels API** para imágenes aleatorias
- Integración con **Azure Computer Vision** para etiquetado con IA
- Sistema de puntuación con bonificación por pistas no usadas (100/60/30 pts)
- Sistema de pistas: revelación de letras y conteo de caracteres
- Autenticación de usuarios con Laravel Breeze (registro, login, perfil)
- Modo invitado (jugar sin registro, sin guardar puntuación)
- Dashboard con estadísticas personales (récord, partidas, ranking)
- Leaderboard global con pódium top 3
- Página de perfil con historial de partidas, cambio de contraseña y eliminación de cuenta
- Estética Matrix/terminal con lluvia de caracteres, scanlines CRT y efectos de brillo
- Diseño responsive completo (móvil, tablet, escritorio)
- CSS y JS compartidos (`matrix.css`, `matrix.js`) para consistencia visual
