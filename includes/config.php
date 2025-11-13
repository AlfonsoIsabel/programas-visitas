<?php
// includes/config.php
// Ajusta según tu entorno local
define('DB_HOST', 'localhost');
define('DB_NAME', 'programas_visitas');
define('DB_USER', 'root');
define('DB_PASS', ''); // pon tu contraseña de MySQL si aplica

// Rutas
define('BASE_PATH', __DIR__ . '/../');
define('PUBLIC_PATH', BASE_PATH . 'public/');
define('UPLOADS_LOGOS', PUBLIC_PATH . 'uploads/logos/');
define('UPLOADS_IMAGENES', PUBLIC_PATH . 'uploads/imagenes/');