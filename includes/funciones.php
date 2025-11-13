<?php
// ===============================================
// includes/funciones.php
// Funciones comunes para el sistema de programas de visitas
// ===============================================

/**
 * Sube un archivo de tipo imagen o documento, valida extensión,
 * genera nombre único y devuelve ruta relativa (para guardar en BD).
 * 
 * @param string $inputName  Nombre del campo <input type="file">
 * @param string $destFolder Carpeta destino (absoluta o relativa)
 * @param array  $extPermitidas Lista de extensiones válidas (sin punto)
 * @return string|null Ruta relativa (desde /public) o null si falla
 */
function subirArchivo($inputName, $destFolder, $extPermitidas = ['jpg','jpeg','png','gif']) {
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $nombreOriginal = $_FILES[$inputName]['name'];
    $tmpName = $_FILES[$inputName]['tmp_name'];
    $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    if (!in_array($ext, $extPermitidas)) {
        return null;
    }

    // Crear carpeta destino si no existe
    if (!is_dir($destFolder)) {
        mkdir($destFolder, 0755, true);
    }

    // Generar nombre único
    $nombreLimpio = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($nombreOriginal, PATHINFO_FILENAME));
    $nuevoNombre = uniqid() . '_' . $nombreLimpio . '.' . $ext;
    $rutaDestino = rtrim($destFolder, '/\\') . DIRECTORY_SEPARATOR . $nuevoNombre;

    if (move_uploaded_file($tmpName, $rutaDestino)) {
        // Convertir ruta absoluta en relativa (desde /public)
        $rutaAbsolutaPublic = realpath(__DIR__ . '/../public');
        $rutaReal = realpath($rutaDestino);

        if ($rutaAbsolutaPublic && $rutaReal && strpos($rutaReal, $rutaAbsolutaPublic) === 0) {
            $rutaRelativa = str_replace('\\', '/', substr($rutaReal, strlen($rutaAbsolutaPublic) + 1));
        } else {
            $rutaRelativa = str_replace('\\', '/', $rutaDestino);
        }

        return $rutaRelativa;
    }

    return null;
}

/**
 * Genera un "slug" seguro para URLs públicas.
 * Ejemplo: "Formulario Rías Baixas" → "formulario_rias_baixas_abc123"
 */
function generarSlug($texto) {
    $slug = strtolower(trim($texto));
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
    $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
    $slug = trim($slug, '_');
    return $slug . '_' . substr(uniqid(), -6);
}

/**
 * Devuelve una fecha formateada al estilo inglés con ordinal:
 * Ejemplo: "2025-11-14" → "Friday, 14th November"
 */
function formatoFechaLiteral($fecha) {
    if (empty($fecha) || $fecha === '0000-00-00') return '';

    setlocale(LC_TIME, 'en_US.UTF-8');
    $timestamp = strtotime($fecha);

    $dayOfWeek = strftime('%A', $timestamp);
    $day = date('j', $timestamp);
    $suffix = ($day % 10 == 1 && $day != 11) ? 'st' : (($day % 10 == 2 && $day != 12) ? 'nd' : (($day % 10 == 3 && $day != 13) ? 'rd' : 'th'));
    $month = strftime('%B', $timestamp);

    return "{$dayOfWeek}, {$day}{$suffix} {$month}";
}

/**
 * Limpia texto para evitar inyección o XSS
 */
function limpiarTexto($txt) {
    return htmlspecialchars(trim($txt), ENT_QUOTES, 'UTF-8');
}

/**
 * Devuelve un nombre de archivo limpio (sin caracteres especiales)
 */
function limpiarNombreArchivo($nombre) {
    return preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $nombre);
}