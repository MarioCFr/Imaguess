# IMAGUESS 🟩

Juego web de adivinanza de imágenes potenciado por inteligencia artificial.  
El jugador ve una imagen aleatoria obtenida de **Pexels** y tiene que adivinar la etiqueta que la IA de **Azure Computer Vision** le ha asignado. Tienes 60 segundos y dos pistas disponibles.

> Proyecto de Fin de Grado — Desarrollo de Aplicaciones Web  
> Autor: Mario Cordero Freire | Tutor: Antonio Jesús Carmona Lara

---

## Tecnologías

- **Laravel 12** + PHP 8.2
- **MySQL 8** (via XAMPP)
- **Tailwind CSS v3** + Blade
- **Pexels API** — imágenes aleatorias
- **Azure Computer Vision** — etiquetado con IA

---

## Requisitos previos

Antes de instalar el proyecto necesitas tener en tu sistema:

- [PHP 8.2+](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Node.js y npm](https://nodejs.org/)
- [XAMPP](https://www.apachefriends.org/) (o cualquier servidor MySQL)
- Git

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/imaguess.git
cd imaguess
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Instalar dependencias JavaScript

```bash
npm install
```

### 4. Configurar el archivo de entorno

```bash
cp .env.example .env
php artisan key:generate
```

Abre el archivo `.env` y edita estos valores con tus datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=imaguess
DB_USERNAME=root
DB_PASSWORD=

PEXELS_API_KEY=tu_clave_aqui
AZURE_VISION_KEY=tu_clave_aqui
AZURE_VISION_ENDPOINT=tu_endpoint_aqui
```

### 5. Crear la base de datos

Abre XAMPP, arranca MySQL y crea una base de datos llamada `imaguess` desde phpMyAdmin (`http://localhost/phpmyadmin`).

### 6. Ejecutar las migraciones

```bash
php artisan migrate
```

### 7. Arrancar el proyecto

Necesitas dos terminales abiertas a la vez:

**Terminal 1 — servidor Laravel:**

```bash
php artisan serve
```

**Terminal 2 — compilador de assets:**

```bash
npm run dev
```

Abre el navegador en **http://localhost:8000**

---

## Obtener las claves de API

### Pexels

1. Crea una cuenta en [pexels.com](https://www.pexels.com/api/)
2. Ve a tu perfil → API
3. Copia tu clave y pégala en `PEXELS_API_KEY` del `.env`

### Azure Computer Vision

1. Necesitas una cuenta en [Azure](https://azure.microsoft.com/)
2. Crea un recurso de tipo **Computer Vision** en el portal
3. Copia la clave y el endpoint y pégalos en el `.env`

---

## Licencia

Proyecto académico — TFG DAW 2025/2026. No destinado a uso comercial.
