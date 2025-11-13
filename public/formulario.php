<?php
require_once __DIR__ . '/../includes/conexion.php';
$slug = $_GET['slug'] ?? '';
$form = $conexion->query("SELECT * FROM formularios WHERE slug = '$slug'")->fetch_assoc();
if (!$form) {
    die("<h2 style='text-align:center;color:red;margin-top:50px;'>Formulario no encontrado.</h2>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($form['titulo']); ?> - Program Visit Form</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { 
  background-size: cover;
  background-position: center;
  font-family: 'Poppins', sans-serif;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
.card {
  background: rgb(241 151 151 / 35%);
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  border: none;
  border-radius: 1rem;
  padding: 2rem 2.5rem;
  max-width: 750px;
  width: 100%;
}
.btn-primary {
  background-color: #b30000;
  border-color: #b30000;
  border-radius: 0.5rem;
  font-weight: 600;
}
.btn-primary:hover {
  background-color: #900000;
}
</style>
</head>
<body>

<div class="card">
  <h3 class="text-center mb-4 text-uppercase fw-bold text-danger">
    <?php echo htmlspecialchars($form['titulo']); ?>
  </h3>

  <form id="formVisita" enctype="multipart/form-data">
    <input type="hidden" name="form_id" value="<?php echo $form['id']; ?>">

    <div class="mb-3">
      <label class="form-label">Visit title</label>
      <input type="text" name="titulo" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Day of the week</label>
      <select class="form-select" name="dia_semana" required>
        <option value="">Select...</option>
        <option>Monday</option><option>Tuesday</option><option>Wednesday</option>
        <option>Thursday</option><option>Friday</option><option>Saturday</option><option>Sunday</option>
      </select>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Day (ordinal)</label>
        <select class="form-select" name="fecha" required>
          <option value="">Select...</option>
          <?php
          for ($i=1; $i<=31; $i++) {
            $suffix = ($i%10==1&&$i!=11)?'st':(($i%10==2&&$i!=12)?'nd':(($i%10==3&&$i!=13)?'rd':'th'));
            echo "<option value='{$i}{$suffix}'>{$i}{$suffix}</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Month</label>
        <select class="form-select" name="mes" required>
          <option value="">Select...</option>
          <option>January</option><option>February</option><option>March</option><option>April</option>
          <option>May</option><option>June</option><option>July</option><option>August</option>
          <option>September</option><option>October</option><option>November</option><option>December</option>
        </select>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="descripcion" class="form-control" rows="3" required></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Location</label>
      <input type="text" name="lugar" class="form-control" required>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Start time</label>
        <input type="time" name="hora_inicio" class="form-control" required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">End time</label>
        <input type="time" name="hora_fin" class="form-control" required>
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label">Upload image (optional)</label>
      <input type="file" name="imagen" class="form-control" accept="image/*">
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-primary py-2">📩 Submit</button>
    </div>
  </form>
</div>

<!-- Modal de gracias -->
<div class="modal fade" id="modalGracias" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">GRACIAS</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <p class="fs-5">El formulario se ha enviado</p>
        <p class="text-muted">Puede seguir introduciendo visitas al programa</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('formVisita').addEventListener('submit', async function(e) {
  e.preventDefault();

  const form = e.target;
  const data = new FormData(form);

  try {
    const response = await fetch('guardar_respuesta.php', {
      method: 'POST',
      body: data
    });

    const text = await response.text();
    if (response.ok) {
      // Mostrar modal de gracias
      const modal = new bootstrap.Modal(document.getElementById('modalGracias'));
      modal.show();
      form.reset();
    } else {
      alert('Error saving data:\n' + text);
    }
  } catch (error) {
    console.error(error);
    alert('Unexpected error: ' + error.message);
  }
});
</script>
</body>
</html>