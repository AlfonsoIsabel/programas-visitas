<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';

$error = "";

// Si ya está logueado, enviarlo al panel
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "⚠️ Please fill in both fields.";
    } else {
        // Buscar el usuario admin
        $stmt = $conexion->prepare("SELECT id, nombre, password_hash FROM admins WHERE email = ?");
        if (!$stmt) {
            die("Database error: " . $conexion->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $nombre, $hash);
            $stmt->fetch();

            // Verificar contraseña
            if (password_verify($password, $hash)) {
                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_nombre'] = $nombre;
                header("Location: index.php");
                exit;
            } else {
                $error = "❌ Incorrect password.";
            }
        } else {
            $error = "❌ Email not found.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  font-family: 'Poppins', sans-serif;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
.card {
  width: 100%;
  max-width: 400px;
  border: none;
  border-radius: 1rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  padding: 2rem;
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

<div class="card">
  <h4 class="text-center mb-4 text-danger fw-bold">Admin Login</h4>
  
  <?php if ($error): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Email address</label>
      <input type="email" name="email" class="form-control" required placeholder="admin@admin.com">
    </div>

    <div class="mb-4">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required placeholder="••••••••">
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-danger py-2">🔐 Login</button>
    </div>
  </form>
</div>

</body>
</html>