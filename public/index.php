<?php
// public/index.php
require_once __DIR__ . '/../includes/conexion.php';
$formularios = $conexion->query("SELECT * FROM formularios ORDER BY id");
?>
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Formularios - Programas de visitas</title>
  <link rel="stylesheet" href="css/estilos.css">
  <!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Iconos de Bootstrap (opcional) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <h1>Formularios disponibles</h1>
  <ul>
    <?php while($f = $formularios->fetch_assoc()): ?>
      <li><a href="formulario.php?form_id=<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['titulo']); ?></a></li>
    <?php endwhile; ?>
  </ul>
</body>
</html>