<?php
// admin/listado_respuestas.php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/../includes/conexion.php';

$respuestas = $conexion->query("SELECT r.*, f.titulo AS formulario_nombre FROM respuestas_formularios r LEFT JOIN formularios f ON r.form_id = f.id ORDER BY r.fecha_envio DESC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Respuestas</title>
<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Iconos de Bootstrap (opcional) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <h2>Respuestas recibidas</h2>
  <p><a href="index.php">Volver</a></p>
  <table border="1" cellpadding="6" cellspacing="0">
    <tr><th>ID</th><th>Formulario</th><th>Título</th><th>Fecha</th><th>Envío</th><th>Acciones</th></tr>
    <?php while($r = $respuestas->fetch_assoc()): ?>
      <tr>
        <td><?php echo $r['id']; ?></td>
        <td><?php echo htmlspecialchars($r['formulario_nombre']); ?></td>
        <td><?php echo htmlspecialchars($r['titulo']); ?></td>
        <td><?php echo $r['fecha']; ?></td>
        <td><?php echo $r['fecha_envio']; ?></td>
        <td>
          <a href="ver_respuesta.php?id=<?php echo $r['id']; ?>">Ver</a> |
          <a href="generar_pdf.php?id=<?php echo $r['id']; ?>" target="_blank">Generar PDF</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>