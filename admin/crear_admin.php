<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';

// --- Solo permitir a usuarios autenticados crear otros admin ---
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$msg = "";

// --- Procesar formulario ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Validaciones
    if ($nombre === '' || $email === '' || $password === '' || $password2 === '') {
        $msg = '<div class="alert alert-warning">⚠️ All fields are required.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<div class="alert alert-warning">⚠️ Invalid email address.</div>';
    } elseif ($password !== $password2) {
        $msg = '<div class="alert alert-danger">❌ Passwords do not match.</div>';
    } else {
        // Comprobar si el correo ya existe
        $check = $conexion->prepare("SELECT id FROM admins WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $msg = '<div class="alert alert-danger">❌ This email is already registered.</div>';
        } else {
            // Insertar nuevo admin
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO admins (nombre, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $nombre, $email, $hash);

            if ($stmt->execute()) {
                $msg = '<div class="alert alert-success">✅ New admin created successfully.</div>';
            } else {
                $msg = '<div class="alert alert-danger">❌ Database error: '.$stmt->error.'</div>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Admin User</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background: linear-gradient(120deg, #ececec, #f7f7f7);
  font-family: 'Poppins', sans-serif;
}
.container {
  max-width: 500px;
  margin-top: 5%;
}
.card {
  border-radius: 1rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  border: none;
}
.btn-danger {
  background-color: #b30000;
  border-color: #b30000;
}
.btn-danger:hover {
  background-color: #900000;
}
</style>
</head>
<body>

<div class="container">
  <div class="card p-4">
    <h3 class="text-center text-danger mb-4">Create New Admin</h3>
    <?php echo $msg; ?>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Full name</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-4">
        <label class="form-label">Repeat password</label>
        <input type="password" name="password2" class="form-control" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-danger">➕ Create Admin</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
