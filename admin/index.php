<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/conexion.php';

// Obtener datos del admin logueado (por si necesitas mostrarlos)
$id = $_SESSION['admin_id'];
$nombre = $_SESSION['admin_nombre'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel - Programas de Visitas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background: #f8f9fa;
  font-family: 'Poppins', sans-serif;
}
.navbar {
  background-color: #b30000;
}
.navbar-brand, .nav-link, .navbar-text {
  color: #fff !important;
}
.card {
  border-radius: 1rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg mb-4">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">Programas de Visitas</a>
    <div class="d-flex">
      <span class="navbar-text me-3">
        👤 <?= htmlspecialchars($nombre) ?>
      </span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="container">
  <div class="card p-4">
    <h4 class="text-danger fw-bold mb-3">Panel de administración</h4>
    <p class="mb-4">Bienvenido, <?= htmlspecialchars($nombre) ?>. Desde aquí puedes gestionar los formularios y programas de visitas.</p>

    <div class="d-grid gap-2 d-md-block">
      <a href="formularios.php" class="btn btn-outline-danger">📋 Formularios</a>
      <a href="crear_admin.php" class="btn btn-outline-danger">👥 Crear administrador</a>
      <a href="admins.php" class="btn btn-outline-danger">🧾 Ver administradores</a>
    </div>
  </div>
</div>

</body>
</html>