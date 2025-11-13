<?php
require_once __DIR__ . '/../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);

    // Generar un slug único a partir del título
    $slug_base = strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $titulo)));
    $slug_base = trim($slug_base, '-');
    if (empty($slug_base)) {
        $slug_base = 'formulario';
    }
    $slug = $slug_base . '_' . uniqid();

    // Insertar en BD
    $stmt = $conexion->prepare("INSERT INTO formularios (titulo, slug, descripcion) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Error en prepare(): " . $conexion->error);
    }
    $stmt->bind_param("sss", $titulo, $slug, $descripcion);
    $stmt->execute();

    $form_id = $stmt->insert_id;

    // Crear enlace público
    $enlace_publico = "http://localhost/programas-visitas/public/formulario.php?slug=" . urlencode($slug);

    // Actualizar enlace
    $conexion->query("UPDATE formularios SET enlace_publico = '$enlace_publico' WHERE id = $form_id");

    header("Location: index.php?success=1");
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nuevo formulario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a href="index.php" class="navbar-brand">Panel de Administración</a>
  </div>
</nav>

<div class="container mt-5">
  <div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Crear nuevo formulario</h5>
    </div>
    <div class="card-body">
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Título del formulario</label>
          <input type="text" name="titulo" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" rows="4" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">
          <i class="bi bi-plus-circle"></i> Crear formulario
        </button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
      </form>
    </div>
  </div>
</div>

</body>
</html>