<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// If already logged in, redirect to dashboard
if (is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/index");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = "Sesión inválida o expirada. Recarga la página.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $error = "Por favor ingresa usuario y contraseña.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Regenerate session id for security
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_email'] = $admin['email'];

                header("Location: " . BASE_URL . "/admin/index");
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acceso Administrador — Funktographer</title>
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: radial-gradient(circle at center, #1b162c 0%, #09090c 100%);
    }
    .login-card {
      width: 100%;
      max-width: 440px;
      background: #14141e;
      border: 1px solid rgba(121, 56, 226, 0.3);
      border-radius: 16px;
      padding: 44px 36px;
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.7), 0 0 30px rgba(121, 56, 226, 0.2);
    }
    .login-brand {
      text-align: center;
      margin-bottom: 30px;
    }
    .login-brand img {
      height: 48px;
      margin-bottom: 12px;
    }
    .login-brand h2 {
      font-family: 'Sora', sans-serif;
      font-size: 1.4rem;
      color: #fff;
    }
    .login-brand p {
      color: var(--adm-muted);
      font-size: 0.9rem;
    }
    .default-hint {
      margin-top: 24px;
      padding: 12px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px dashed var(--adm-border);
      border-radius: 8px;
      font-size: 0.8rem;
      color: var(--adm-muted);
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <div class="login-brand">
      <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Funktographer">
      <h2>Panel de Administración</h2>
      <p>Gestión de fotos del Home y Proyectos</p>
    </div>

    <?php if ($error): ?>
      <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-exclamation-circle"></i>
        <span><?= htmlspecialchars($error) ?></span>
      </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/admin/login" method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="adm-form-group">
        <label class="adm-form-label" for="username">Usuario o Email</label>
        <input type="text" class="adm-input" id="username" name="username" placeholder="admin" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>

      <div class="adm-form-group">
        <label class="adm-form-label" for="password">Contraseña</label>
        <input type="password" class="adm-input" id="password" name="password" placeholder="••••••••" required>
      </div>

      <button type="submit" class="adm-btn adm-btn-primary" style="width: 100%; justify-content: center; padding: 14px; font-size: 1rem; margin-top: 10px;">
        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
      </button>
    </form>

    <div class="default-hint">
      Acceso por defecto: <strong>admin</strong> / <strong>admin1234</strong>
    </div>

    <div style="text-align: center; margin-top: 20px;">
      <a href="<?= BASE_URL ?>/" style="color: var(--adm-muted); font-size: 0.85rem;">
        <i class="fas fa-arrow-left"></i> Volver al sitio web
      </a>
    </div>
  </div>

</body>
</html>
