<?php
// public/guardar_respuesta.php (versión robusta y con manejo de errores)
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

// Asegurarnos de que UPLOADS_IMAGENES esté definido
if (!defined('UPLOADS_IMAGENES')) {
    define('UPLOADS_IMAGENES', __DIR__ . '/../uploads');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// --- Recolectar y sanear entradas ---
$form_id     = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
$titulo      = trim($_POST['titulo'] ?? '');
$nombre      = trim($_POST['nombre_remitente'] ?? '');
$dia_semana  = trim($_POST['dia_semana'] ?? '');
$dia_ordinal = trim($_POST['fecha'] ?? ''); // ordinal day value (1st, 2nd...)
$mes         = trim($_POST['mes'] ?? '');
$hora_inicio = trim($_POST['hora_inicio'] ?? '');
$hora_fin    = trim($_POST['hora_fin'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$lugar       = trim($_POST['lugar'] ?? '');

// Construir fecha literal (por ejemplo: "Monday, 21st of September")
$fecha_literal = '';
if ($dia_semana !== '' && $dia_ordinal !== '' && $mes !== '') {
    $fecha_literal = "$dia_semana, $dia_ordinal of $mes";
}

// --- Subir imagen (si hay) ---
$imagen_path = null;
if (!empty($_FILES['imagen']['name'])) {
    $subido = subirArchivo('imagen', UPLOADS_IMAGENES, ['jpg','jpeg','png','gif']);
    if ($subido) {
        // guardar la ruta relativa desde la carpeta public/uploads/ (según tu lógica)
        // subirArchivo devuelve la ruta absoluta o relativa según implementación; normalizamos:
        $imagen_path = str_replace('\\', '/', $subido);
        // Si la ruta es absoluta, convertimos a relativa respecto a la carpeta del proyecto (opcional)
        // Ejemplo si subirArchivo guarda "/var/www/.../uploads/imagen.jpg" -> lo dejamos tal cual
    } else {
        // (opcional) registrar o manejar error de subida
        error_log("guardar_respuesta.php: fallo al subir imagen para form_id={$form_id}");
    }
}

// --- SQL e INSERT con verificación ---
$sql = "INSERT INTO respuestas_formularios
    (form_id, titulo, nombre_remitente, fecha, dia_semana, hora_inicio, hora_fin, descripcion, lugar, imagen)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

if ($stmt === false) {
    // Preparación falló: registrar el error completo para depuración y mostrar mensaje genérico al usuario
    $err = $conexion->error;
    error_log("guardar_respuesta.php: prepare() falló. SQL: {$sql} - Error: {$err}");
    // Para depuración inmediata (temporal) puedes enviar un error más visible:
    die("Error interno: no se puede procesar el formulario. Consulte logs del servidor.");
}

// Tipos: 1 int + 9 strings => 10 variables
$types = 'isssssssss';

// Asegurarnos de pasar exactamente 10 parámetros
$params = [
    $form_id,
    $titulo,
    $nombre,
    $fecha_literal,
    $dia_semana,
    $hora_inicio,
    $hora_fin,
    $descripcion,
    $lugar,
    $imagen_path
];

// bind_param requiere parámetros por referencia
$bind_names[] = $types;
for ($i = 0; $i < count($params); $i++) {
    $bind_name = 'bind' . $i;
    $$bind_name = $params[$i];
    $bind_names[] = &$$bind_name;
}

// Llamada dinámica a bind_param
call_user_func_array([$stmt, 'bind_param'], $bind_names);

// Ejecutar y comprobar
if ($stmt->execute() === false) {
    $err2 = $stmt->error;
    error_log("guardar_respuesta.php: execute() falló. Error: {$err2}");
    die("Error al guardar los datos (ejecución).");
}

/if ($stmt->execute()) {
    echo "OK"; // respuesta simple para el fetch
} else {
    http_response_code(500);
    echo "Error al guardar los datos: " . $stmt->error;
}
exit;