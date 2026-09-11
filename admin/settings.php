<?php
$adminTitle = "Configuración y Cuenta";
require_once __DIR__ . '/header.php';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Token CSRF inválido.');
        header("Location: " . BASE_URL . "/admin/settings");
        exit;
    }

    $formType = $_POST['form_type'] ?? '';

    // 1. Update Password
    if ($formType === 'password') {
        $currentPass = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        $adminId = $_SESSION['admin_id'];
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt->execute([$adminId]);
        $adm = $stmt->fetch();

        if (!$adm || !password_verify($currentPass, $adm['password'])) {
            set_flash('danger', 'La contraseña actual no es correcta.');
        } elseif (strlen($newPass) < 6) {
            set_flash('danger', 'La nueva contraseña debe tener al menos 6 caracteres.');
        } elseif ($newPass !== $confirmPass) {
            set_flash('danger', 'La nueva contraseña y su confirmación no coinciden.');
        } else {
            $hashed = password_hash($newPass, PASSWORD_BCRYPT);
            $stmtUp = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $stmtUp->execute([$hashed, $adminId]);
            set_flash('success', 'Contraseña actualizada exitosamente.');
        }
        header("Location: " . BASE_URL . "/admin/settings");
        exit;
    }

    // 2. Update Site Details
    if ($formType === 'site_settings') {
        $keys = ['site_name', 'site_tagline', 'phone', 'whatsapp', 'email', 'instagram', 'coverage'];
        foreach ($keys as $k) {
            if (isset($_POST[$k])) {
                $v = trim($_POST[$k]);
                $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$k, $v, $v]);
            }
        }
        set_flash('success', 'Datos del sitio actualizados correctamente.');
        header("Location: " . BASE_URL . "/admin/settings");
        exit;
    }
}
?>

<div class="adm-header">
  <div>
    <h1 class="adm-title">Ajustes Generales</h1>
    <p class="adm-subtitle">Administra los datos de contacto y la seguridad de tu cuenta</p>
  </div>
</div>

<div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px;">
  <!-- Site Info Card -->
  <div class="adm-card">
    <h3 class="adm-card-title" style="margin-bottom: 24px;">Información de la Marca y Contacto</h3>

    <form action="<?= BASE_URL ?>/admin/settings" method="POST">
      <input type="hidden" name="form_type" value="site_settings">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="adm-form-group">
        <label class="adm-form-label" for="site_name">Nombre de la Marca</label>
        <input type="text" class="adm-input" id="site_name" name="site_name" value="<?= htmlspecialchars(get_setting('site_name', 'Funktographer')) ?>" required>
      </div>

      <div class="adm-form-group">
        <label class="adm-form-label" for="site_tagline">Eslogan / Subtítulo</label>
        <input type="text" class="adm-input" id="site_tagline" name="site_tagline" value="<?= htmlspecialchars(get_setting('site_tagline', 'Fotografía y Video Profesional en Santiago de Chile')) ?>" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="adm-form-group">
          <label class="adm-form-label" for="phone">Teléfono de Contacto</label>
          <input type="text" class="adm-input" id="phone" name="phone" value="<?= htmlspecialchars(get_setting('phone', '+56 9 4757 3794')) ?>" required>
        </div>

        <div class="adm-form-group">
          <label class="adm-form-label" for="whatsapp">WhatsApp (solo números sin '+')</label>
          <input type="text" class="adm-input" id="whatsapp" name="whatsapp" value="<?= htmlspecialchars(get_setting('whatsapp', '56947573794')) ?>" required>
        </div>
      </div>

      <div class="adm-form-group">
        <label class="adm-form-label" for="email">Correo Electrónico Oficial</label>
        <input type="email" class="adm-input" id="email" name="email" value="<?= htmlspecialchars(get_setting('email', 'contacto@funktographer.cl')) ?>" required>
      </div>

      <div class="adm-form-group">
        <label class="adm-form-label" for="instagram">URL de Perfil Instagram</label>
        <input type="url" class="adm-input" id="instagram" name="instagram" value="<?= htmlspecialchars(get_setting('instagram', 'https://www.instagram.com/funktographer/')) ?>" required>
      </div>

      <div class="adm-form-group">
        <label class="adm-form-label" for="coverage">Zona de Cobertura</label>
        <input type="text" class="adm-input" id="coverage" name="coverage" value="<?= htmlspecialchars(get_setting('coverage', 'Santiago de Chile / Cobertura Nacional e Internacional')) ?>" required>
      </div>

      <button type="submit" class="adm-btn adm-btn-primary" style="padding: 12px 28px;">
        <i class="fas fa-save"></i> Guardar Información
      </button>
    </form>
  </div>

  <!-- Change Password Card -->
  <div class="adm-card">
    <h3 class="adm-card-title" style="margin-bottom: 24px;">Seguridad y Contraseña</h3>

    <form action="<?= BASE_URL ?>/admin/settings" method="POST">
      <input type="hidden" name="form_type" value="password">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="adm-form-group">
        <label class="adm-form-label" for="current_password">Contraseña Actual *</label>
        <input type="password" class="adm-input" id="current_password" name="current_password" required>
      </div>

      <div class="adm-form-group">
        <label class="adm-form-label" for="new_password">Nueva Contraseña (mínimo 6 caracteres) *</label>
        <input type="password" class="adm-input" id="new_password" name="new_password" minlength="6" required>
      </div>

      <div class="adm-form-group">
        <label class="adm-form-label" for="confirm_password">Confirmar Nueva Contraseña *</label>
        <input type="password" class="adm-input" id="confirm_password" name="confirm_password" minlength="6" required>
      </div>

      <button type="submit" class="adm-btn adm-btn-outline" style="padding: 12px 28px;">
        <i class="fas fa-key"></i> Actualizar Contraseña
      </button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
