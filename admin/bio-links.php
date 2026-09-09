<?php
$adminTitle = "Gestión de Bio Links (Hub)";
require_once __DIR__ . '/header.php';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Token CSRF inválido.');
        header("Location: " . BASE_URL . "/admin/bio-links");
        exit;
    }

    $action = $_POST['action'] ?? '';

    // 1. Add new link
    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fas fa-link');
        $badge = trim($_POST['badge'] ?? '');
        $style = trim($_POST['style_type'] ?? 'primary');
        $order = intval($_POST['display_order'] ?? 0);

        if (empty($title) || empty($url)) {
            set_flash('danger', 'El título y la URL son campos requeridos.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO bio_links (title, subtitle, url, icon, badge, style_type, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$title, $subtitle, $url, $icon, $badge, $style, $order]);
            set_flash('success', "Enlace '$title' agregado exitosamente.");
        }
        header("Location: " . BASE_URL . "/admin/bio-links");
        exit;
    }

    // 2. Edit existing link
    if ($action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fas fa-link');
        $badge = trim($_POST['badge'] ?? '');
        $style = trim($_POST['style_type'] ?? 'primary');
        $order = intval($_POST['display_order'] ?? 0);

        if (empty($title) || empty($url) || $id <= 0) {
            set_flash('danger', 'Datos incompletos para actualizar el enlace.');
        } else {
            $stmt = $pdo->prepare("UPDATE bio_links SET title = ?, subtitle = ?, url = ?, icon = ?, badge = ?, style_type = ?, display_order = ? WHERE id = ?");
            $stmt->execute([$title, $subtitle, $url, $icon, $badge, $style, $order, $id]);
            set_flash('success', "Enlace '$title' actualizado correctamente.");
        }
        header("Location: " . BASE_URL . "/admin/bio-links");
        exit;
    }

    // 3. Toggle status
    if ($action === 'toggle') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE bio_links SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', "Estado del enlace actualizado.");
        header("Location: " . BASE_URL . "/admin/bio-links");
        exit;
    }

    // 4. Delete link
    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM bio_links WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', "Enlace eliminado correctamente.");
        header("Location: " . BASE_URL . "/admin/bio-links");
        exit;
    }
}

// Fetch all links
$stmt = $pdo->query("SELECT * FROM bio_links ORDER BY display_order ASC, id ASC");
$links = $stmt->fetchAll();

// Check if editing
$editId = intval($_GET['edit'] ?? 0);
$editLink = null;
if ($editId > 0) {
    $stmtEdit = $pdo->prepare("SELECT * FROM bio_links WHERE id = ? LIMIT 1");
    $stmtEdit->execute([$editId]);
    $editLink = $stmtEdit->fetch();
}
?>

<main class="adm-main">
  <!-- Top Bar -->
  <div class="adm-topbar">
    <div class="adm-breadcrumbs">
      <a href="<?= BASE_URL ?>/admin/index">Dashboard</a>
      <i class="fas fa-chevron-right"></i>
      <span>Bio Links (Hub)</span>
    </div>
    <div class="adm-user-info">
      <a href="<?= BASE_URL ?>/links" target="_blank" class="btn btn-primary btn-sm" style="background: var(--adm-primary); color: #09090c; font-weight: 700;">
        <i class="fas fa-external-link-alt"></i> Ver /links en vivo
      </a>
      <div class="adm-avatar">
        <i class="fas fa-user-shield"></i>
      </div>
      <span><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
    </div>
  </div>

  <div class="adm-content">

    <?php if ($flash): ?>
      <div class="adm-alert adm-alert-<?= htmlspecialchars($flash['type']) ?>">
        <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
        <span><?= htmlspecialchars($flash['message']) ?></span>
      </div>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
      <div>
        <h1 style="font-size: 1.6rem; color: #fff; margin-bottom: 4px;">
          <i class="fas fa-link" style="color: var(--adm-primary);"></i> Enlaces de la Bio (Hub / Linktree)
        </h1>
        <p style="color: var(--adm-text-muted); font-size: 0.92rem;">
          Gestiona los botones y accesos directos que verán tus seguidores en <strong><?= BASE_URL ?>/links</strong>
        </p>
      </div>
      <?php if ($editLink): ?>
        <a href="<?= BASE_URL ?>/admin/bio-links" class="btn btn-outline btn-sm">
          <i class="fas fa-times"></i> Cancelar Edición
        </a>
      <?php endif; ?>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1.8fr; gap: 28px; align-items: flex-start;">

      <!-- Left Column: Add / Edit Form -->
      <div class="adm-card">
        <h3 style="font-size: 1.15rem; color: #fff; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--adm-border);">
          <i class="fas <?= $editLink ? 'fa-pen' : 'fa-plus-circle' ?>" style="color: var(--adm-primary);"></i>
          <?= $editLink ? 'Editar Enlace #' . $editLink['id'] : 'Crear Nuevo Enlace' ?>
        </h3>

        <form method="POST" action="<?= BASE_URL ?>/admin/bio-links">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="action" value="<?= $editLink ? 'edit' : 'add' ?>">
          <?php if ($editLink): ?>
            <input type="hidden" name="id" value="<?= $editLink['id'] ?>">
          <?php endif; ?>

          <div class="adm-form-group">
            <label class="adm-label">Título Principal *</label>
            <input type="text" name="title" class="adm-input" required 
                   placeholder="Ej: Portafolio Audiovisual" 
                   value="<?= htmlspecialchars($editLink['title'] ?? '') ?>">
          </div>

          <div class="adm-form-group">
            <label class="adm-label">Subtítulo Descriptivo (Opcional)</label>
            <input type="text" name="subtitle" class="adm-input" 
                   placeholder="Ej: Conoce nuestras coberturas más recientes" 
                   value="<?= htmlspecialchars($editLink['subtitle'] ?? '') ?>">
          </div>

          <div class="adm-form-group">
            <label class="adm-label">URL Destino *</label>
            <input type="text" name="url" class="adm-input" required 
                   placeholder="Ej: https://... o /proyectos" 
                   value="<?= htmlspecialchars($editLink['url'] ?? '') ?>">
          </div>

          <div class="adm-form-group">
            <label class="adm-label">Icono (Clase FontAwesome)</label>
            <div style="display: flex; gap: 10px; align-items: center;">
              <input type="text" name="icon" id="iconInput" class="adm-input" 
                     placeholder="Ej: fab fa-whatsapp" 
                     value="<?= htmlspecialchars($editLink['icon'] ?? 'fas fa-link') ?>">
              <div id="iconPreview" style="width: 44px; height: 44px; background: rgba(255,255,255,0.06); border: 1px solid var(--adm-border); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: var(--adm-primary); flex-shrink: 0;">
                <i class="<?= htmlspecialchars($editLink['icon'] ?? 'fas fa-link') ?>"></i>
              </div>
            </div>
            <!-- Quick Icon Suggestions -->
            <div style="margin-top: 8px; display: flex; gap: 6px; flex-wrap: wrap;">
              <span style="font-size: 0.75rem; color: var(--adm-text-muted); align-self: center;">Sugeridos:</span>
              <button type="button" class="btn btn-outline btn-sm icon-pick-btn" data-icon="fab fa-whatsapp" style="padding: 2px 8px;"><i class="fab fa-whatsapp"></i></button>
              <button type="button" class="btn btn-outline btn-sm icon-pick-btn" data-icon="fab fa-instagram" style="padding: 2px 8px;"><i class="fab fa-instagram"></i></button>
              <button type="button" class="btn btn-outline btn-sm icon-pick-btn" data-icon="fab fa-youtube" style="padding: 2px 8px;"><i class="fab fa-youtube"></i></button>
              <button type="button" class="btn btn-outline btn-sm icon-pick-btn" data-icon="fab fa-behance" style="padding: 2px 8px;"><i class="fab fa-behance"></i></button>
              <button type="button" class="btn btn-outline btn-sm icon-pick-btn" data-icon="fas fa-camera-retro" style="padding: 2px 8px;"><i class="fas fa-camera-retro"></i></button>
              <button type="button" class="btn btn-outline btn-sm icon-pick-btn" data-icon="fas fa-globe" style="padding: 2px 8px;"><i class="fas fa-globe"></i></button>
              <button type="button" class="btn btn-outline btn-sm icon-pick-btn" data-icon="fas fa-film" style="padding: 2px 8px;"><i class="fas fa-film"></i></button>
              <button type="button" class="btn btn-outline btn-sm icon-pick-btn" data-icon="fas fa-phone-alt" style="padding: 2px 8px;"><i class="fas fa-phone-alt"></i></button>
              <button type="button" class="btn btn-outline btn-sm icon-pick-btn" data-icon="fas fa-envelope" style="padding: 2px 8px;"><i class="fas fa-envelope"></i></button>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            <div class="adm-form-group">
              <label class="adm-label">Etiqueta Badge</label>
              <input type="text" name="badge" class="adm-input" 
                     placeholder="Ej: Nuevo, Cotizar..." 
                     value="<?= htmlspecialchars($editLink['badge'] ?? '') ?>">
            </div>
            <div class="adm-form-group">
              <label class="adm-label">Estilo Visual</label>
              <select name="style_type" class="adm-input">
                <option value="primary" <?= ($editLink['style_type'] ?? '') === 'primary' ? 'selected' : '' ?>>Dorado Primario</option>
                <option value="glow" <?= ($editLink['style_type'] ?? '') === 'glow' ? 'selected' : '' ?>>Resplandor Glow (Destacado)</option>
                <option value="gradient" <?= ($editLink['style_type'] ?? '') === 'gradient' ? 'selected' : '' ?>>Gradiente Sunset</option>
                <option value="outline" <?= ($editLink['style_type'] ?? '') === 'outline' ? 'selected' : '' ?>>Glassmorphism / Outline</option>
              </select>
            </div>
          </div>

          <div class="adm-form-group">
            <label class="adm-label">Orden de Despliegue</label>
            <input type="number" name="display_order" class="adm-input" 
                   value="<?= intval($editLink['display_order'] ?? count($links) + 1) ?>">
          </div>

          <div style="margin-top: 22px;">
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; background: var(--adm-primary); color: #09090c; font-weight: 700;">
              <i class="fas fa-save"></i> <?= $editLink ? 'Guardar Cambios' : 'Crear Enlace' ?>
            </button>
          </div>
        </form>
      </div>

      <!-- Right Column: List of Current Links -->
      <div class="adm-card">
        <h3 style="font-size: 1.15rem; color: #fff; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--adm-border); display: flex; justify-content: space-between; align-items: center;">
          <span>Enlaces Activos en la Bio</span>
          <span style="font-size: 0.82rem; color: var(--adm-text-muted); font-weight: 500;">
            Total: <?= count($links) ?> enlaces
          </span>
        </h3>

        <?php if (empty($links)): ?>
          <p style="color: var(--adm-text-muted); text-align: center; padding: 40px 0;">
            No hay enlaces registrados actualmente.
          </p>
        <?php else: ?>
          <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($links as $l): ?>
              <div style="display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: rgba(255,255,255,0.02); border: 1px solid var(--adm-border); border-radius: 12px; transition: all 0.2s ease;">
                
                <!-- Order badge -->
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--adm-text-muted); width: 24px; text-align: center;">
                  #<?= $l['display_order'] ?>
                </div>

                <!-- Icon -->
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--adm-primary); flex-shrink: 0;">
                  <i class="<?= htmlspecialchars($l['icon'] ?: 'fas fa-link') ?>"></i>
                </div>

                <!-- Content -->
                <div style="flex-grow: 1; min-width: 0;">
                  <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 2px;">
                    <strong style="color: #fff; font-size: 0.98rem;"><?= htmlspecialchars($l['title']) ?></strong>
                    <?php if (!empty($l['badge'])): ?>
                      <span style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase; background: rgba(255, 197, 1, 0.15); color: var(--adm-primary); padding: 2px 7px; border-radius: 100px; border: 1px solid rgba(255, 197, 1, 0.3);">
                        <?= htmlspecialchars($l['badge']) ?>
                      </span>
                    <?php endif; ?>
                    <?php if ($l['style_type'] === 'glow'): ?>
                      <span style="font-size: 0.65rem; background: rgba(121, 56, 226, 0.2); color: #c4b5fd; padding: 2px 6px; border-radius: 4px;">Glow</span>
                    <?php endif; ?>
                  </div>
                  <div style="font-size: 0.8rem; color: var(--adm-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($l['subtitle'] ?: $l['url']) ?>
                  </div>
                </div>

                <!-- Status Pill -->
                <div>
                  <form method="POST" action="<?= BASE_URL ?>/admin/bio-links" style="margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= $l['id'] ?>">
                    <button type="submit" class="adm-badge" style="cursor: pointer; border: none; background: <?= $l['is_active'] ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)' ?>; color: <?= $l['is_active'] ? '#34d399' : '#f87171' ?>;">
                      <?= $l['is_active'] ? 'Activo' : 'Oculto' ?>
                    </button>
                  </form>
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 6px;">
                  <a href="<?= BASE_URL ?>/admin/bio-links?edit=<?= $l['id'] ?>" class="btn btn-outline btn-sm" title="Editar enlace" style="padding: 6px 10px;">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form method="POST" action="<?= BASE_URL ?>/admin/bio-links" style="margin: 0;" onsubmit="return confirm('¿Eliminar permanentemente este enlace?');">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $l['id'] ?>">
                    <button type="submit" class="btn btn-outline btn-sm" title="Eliminar enlace" style="padding: 6px 10px; color: #f87171;">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </div>

              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>

  </div>
</main>

<script>
  // Icon Picker Helper
  const iconInput = document.getElementById('iconInput');
  const iconPreview = document.getElementById('iconPreview');

  if (iconInput && iconPreview) {
    iconInput.addEventListener('input', () => {
      iconPreview.innerHTML = `<i class="${iconInput.value.trim() || 'fas fa-link'}"></i>`;
    });

    document.querySelectorAll('.icon-pick-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const chosen = btn.getAttribute('data-icon');
        iconInput.value = chosen;
        iconPreview.innerHTML = `<i class="${chosen}"></i>`;
      });
    });
  }
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
