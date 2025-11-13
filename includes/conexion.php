<?php
// includes/conexion.php
require_once __DIR__ . '/config.php';

$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conexion->connect_errno) {
    die("Error conexión DB: " . $conexion->connect_error);
}
$conexion->set_charset('utf8mb4');