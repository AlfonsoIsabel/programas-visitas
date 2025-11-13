<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$form_id = intval($_GET['form_id'] ?? 0);
if ($form_id <= 0) {
    die("ID de formulario no válido.");
}

// Obtener info del formulario
$form = $conexion->query("SELECT * FROM formularios WHERE id = $form_id")->fetch_assoc();

// Obtener respuestas asociadas
$respuestas = $conexion->query("SELECT * FROM respuestas_formularios WHERE form_id = $form_id ORDER BY fecha ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respuestas - <?php echo htmlspecialchars($form['titulo']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>📅 Respuestas de: <span class="text-primary"><?php echo htmlspecialchars($form['titulo']); ?></span></h3>
        <a href="index.php" class="btn btn-secondary">⬅ Volver</a>
    </div>
    <!-- BOTÓN GENERAR PDF -->
    <div class="mb-3 d-flex justify-content-end">
        <a href="generar_pdf.php?form_id=<?php echo $form['id']; ?>" class="btn btn-success">
            📄 Generar PDF
        </a>
    </div>
    <?php if (empty($respuestas)) : ?>
        <div class="alert alert-info">No hay respuestas registradas todavía.</div>
    <?php else : ?>
        <div class="card shadow rounded-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Título</th>
                                <th>Fecha visita</th>
                                <th>Hora inicio</th>
                                <th>Hora fin</th>
                                <th>Descripción</th>
                                <th>Imagen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($respuestas as $r) : ?>
                                <tr>
                                    <td><?php echo $r['id']; ?></td>
                                    <td><?php echo htmlspecialchars($r['titulo']); ?></td>
                                    <td><?php echo $r['fecha']; ?></td>
                                    <td><?php echo $r['hora_inicio']; ?></td>
                                    <td><?php echo $r['hora_fin']; ?></td>
                                    <td><?php echo htmlspecialchars($r['descripcion']); ?></td>
                                    <td>
                                        <?php if ($r['imagen']) : ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($r['imagen']); ?>" alt="" width="100" class="rounded shadow-sm">
                                        <?php else : ?>
                                            <span class="text-muted">Sin imagen</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>