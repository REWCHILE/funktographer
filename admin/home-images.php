<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle Actions BEFORE outputting HTML header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || ($_POST['ajax'] ?? '') === '1';

    // 1. Upload new image
    if ($action === 'upload') {
        if (!csrf_verify($_POST['csrf_token'] ?? '')) {
            set_flash('danger', 'Token CSRF inválido.');
        } else {
            $title = trim($_POST['title'] ?? 'Nueva Fotografía');
            $category = trim($_POST['category'] ?? 'General');
            $order = intval($_POST['display_order'] ?? 1);

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload = upload_image($_FILES['photo'], 'home');
                if ($upload['success']) {
                    $stmt = $pdo->prepare("INSERT INTO home_images (title, image_url, category, display_order, is_active) VALUES (?, ?, ?, ?, 1)");
                    $stmt->execute([$title, $upload['url'], $category, $order]);
                    set_flash('success', "Fotografía '$title' subida correctamente.");
                } else {
                    set_flash('danger', $upload['error']);
                }
            } else {
                set_flash('danger', 'Debes seleccionar un archivo de imagen para subir.');
            }
        }
        header("Location: " . BASE_URL . "/admin/home-images");
        exit;
    }

    // 2. Toggle active/inactive
    if ($action === 'toggle') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE home_images SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?");
        $stmt->execute([$id]);
        
        $newActive = $pdo->query("SELECT is_active FROM home_images WHERE id = $id")->fetchColumn();
        
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'id' => $id, 'is_active' => intval($newActive)]);
            exit;
        }

        set_flash('success', "Estado de la foto actualizado.");
        header("Location: " . BASE_URL . "/admin/home-images");
        exit;
    }

    // 3. Delete image
    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT image_url FROM home_images WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $filePath = ROOT_PATH . '/' . $row['image_url'];
            if (!empty($row['image_url']) && str_starts_with($row['image_url'], 'uploads/') && file_exists($filePath)) {
                @unlink($filePath);
            }
            $pdo->prepare("DELETE FROM home_images WHERE id = ?")->execute([$id]);
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'deleted_id' => $id]);
                exit;
            }

            set_flash('success', "Fotografía eliminada correctamente.");
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Fotografía no encontrada.']);
                exit;
            }
        }
        header("Location: " . BASE_URL . "/admin/home-images");
        exit;
    }
}

$adminTitle = "Gestión de Fotos del Home";
require_once __DIR__ . '/header.php';

// Fetch active categories
$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order ASC, id ASC")->fetchAll();

// Fetch all home images
$images = $pdo->query("SELECT * FROM home_images ORDER BY display_order ASC, id DESC")->fetchAll();
?>

<div class="adm-header">
  <div>
    <h1 class="adm-title">Fotos de la Galería Home</h1>
    <p class="adm-subtitle">Sube, categoriza y gestiona las fotografías que se muestran en la portada</p>
  </div>
  <a href="#uploadCard" class="adm-btn adm-btn-primary">
    <i class="fas fa-upload"></i> Subir Nueva Foto
  </a>
</div>

<!-- Upload Form Card -->
<div class="adm-card" id="uploadCard">
  <div class="adm-card-header">
    <h3 class="adm-card-title"><i class="fas fa-cloud-upload-alt" style="color: var(--adm-primary); margin-right: 8px;"></i> Subir Nueva Fotografía</h3>
  </div>

  <form action="<?= BASE_URL ?>/admin/home-images" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="upload">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
      <div class="adm-form-group">
        <label class="adm-form-label" for="title">Título o Descripción *</label>
        <input type="text" class="adm-input" id="title" name="title" placeholder="Ej: Evento Corporativo WEG 2024" required>
      </div>

      <div class="adm-form-group">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
          <label class="adm-form-label" for="category" style="margin-bottom: 0;">Categoría *</label>
          <button type="button" class="btn-open-cat-modal" id="btnOpenCatModal" title="Gestionar Categorías (Crear, Editar, Eliminar)">
            <i class="fas fa-plus"></i>
          </button>
        </div>
        <select class="adm-select" id="category" name="category" required>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat['slug']) ?>">
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="adm-form-group">
        <label class="adm-form-label" for="display_order">Orden de Visualización</label>
        <input type="number" class="adm-input" id="display_order" name="display_order" value="1" min="0">
      </div>
    </div>

    <div class="adm-form-group">
      <label class="adm-form-label">Archivo de Fotografía (JPG, PNG, WEBP, AVIF, HEIC — Máx. 30MB) *</label>
      <div class="upload-dropzone" style="position: relative; overflow: hidden; padding: 36px 20px; text-align: center; border: 2px dashed var(--adm-border); border-radius: 12px; background: rgba(255,255,255,0.02); transition: all 0.2s ease;">
        <input type="file" id="photoInput" name="photo" accept="image/jpeg,image/png,image/webp,image/avif,image/heic,image/*" style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10;" data-preview="uploadPreviewImg" data-info="photoFileInfo" required>
        <i class="fas fa-cloud-upload-alt" style="font-size: 2.6rem; color: var(--adm-primary); margin-bottom: 12px; display: block;"></i>
        <div style="font-weight: 700; color: #fff; font-size: 1.05rem;" id="dropzoneText">Haz clic para seleccionar o arrastra la foto aquí</div>
        <div style="font-size: 0.85rem; color: var(--adm-muted); margin-top: 6px;" id="photoFileInfo">JPG, PNG, WEBP, AVIF hasta 30MB</div>
        <img id="uploadPreviewImg" class="upload-preview" alt="Vista previa" style="display: none; max-height: 220px; max-width: 100%; margin: 16px auto 0 auto; border-radius: 8px; border: 1px solid var(--adm-border); object-fit: contain;">
      </div>
    </div>

    <button type="submit" class="adm-btn adm-btn-primary" style="padding: 12px 28px;">
      <i class="fas fa-check"></i> Publicar en Home
    </button>
  </form>
</div>

<!-- Images List Card -->
<div class="adm-card">
  <div class="adm-card-header">
    <h3 class="adm-card-title">Fotografías Registradas (<?= count($images) ?> fotos)</h3>
  </div>

  <?php if (empty($images)): ?>
    <p style="color: var(--adm-muted); text-align: center; padding: 40px 0;">No hay fotos registradas. Sube la primera arriba.</p>
  <?php else: ?>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th style="width: 80px;">Foto</th>
            <th>Título</th>
            <th>Categoría</th>
            <th>Orden</th>
            <th>Estado</th>
            <th style="text-align: right;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($images as $img): ?>
            <tr id="photoRow_<?= $img['id'] ?>">
              <td>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($img['image_url']) ?>" alt="" class="adm-thumb">
              </td>
              <td>
                <strong><?= htmlspecialchars($img['title']) ?></strong><br>
                <span style="font-size: 0.75rem; color: var(--adm-muted);"><?= htmlspecialchars($img['image_url']) ?></span>
              </td>
              <td>
                <span class="adm-badge" style="background: rgba(121, 56, 226, 0.2); color: #c4a1ff;">
                  <?= htmlspecialchars($img['category']) ?>
                </span>
              </td>
              <td><?= $img['display_order'] ?></td>
              <td class="status-cell">
                <?php if ($img['is_active']): ?>
                  <span class="pill-active">Visible</span>
                <?php else: ?>
                  <span class="pill-inactive">Oculta</span>
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 8px;">
                  <!-- Toggle Visibility Button (Instant AJAX + Form Fallback) -->
                  <button type="button" class="adm-btn adm-btn-outline adm-btn-sm btn-photo-toggle" data-id="<?= $img['id'] ?>" title="Cambiar visibilidad">
                    <i class="fas <?= $img['is_active'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                  </button>

                  <!-- Delete Button (Instant AJAX + Form Fallback) -->
                  <button type="button" class="adm-btn adm-btn-danger adm-btn-sm btn-photo-delete" data-id="<?= $img['id'] ?>" data-title="<?= htmlspecialchars($img['title']) ?>" title="Eliminar foto">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Unificado de Gestión de Categorías (Crear, Editar, Eliminar) -->
<?php 
$categorySelectId = 'category'; 
require_once __DIR__ . '/modal-category.php'; 
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const CSRF = '<?= csrf_token() ?>';
  const ACTION_URL = '<?= BASE_URL ?>/admin/home-images';

  // Instant AJAX Toggle Visibility
  document.querySelectorAll('.btn-photo-toggle').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const photoId = btn.getAttribute('data-id');
      const icon = btn.querySelector('i');
      const row = document.getElementById(`photoRow_${photoId}`);
      const statusCell = row?.querySelector('.status-cell');

      btn.disabled = true;
      icon.className = 'fas fa-spinner fa-spin';

      const fd = new FormData();
      fd.append('action', 'toggle');
      fd.append('id', photoId);
      fd.append('ajax', '1');
      fd.append('csrf_token', CSRF);

      try {
        const res = await fetch(ACTION_URL, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: fd
        });
        const data = await res.json();

        if (data.success) {
          if (data.is_active == 1) {
            icon.className = 'fas fa-eye-slash';
            if (statusCell) statusCell.innerHTML = '<span class="pill-active">Visible</span>';
          } else {
            icon.className = 'fas fa-eye';
            if (statusCell) statusCell.innerHTML = '<span class="pill-inactive">Oculta</span>';
          }
        } else {
          alert(data.error || 'Error al actualizar visibilidad.');
          icon.className = 'fas fa-eye';
        }
      } catch (err) {
        // Fallback: regular submit
        window.location.reload();
      } finally {
        btn.disabled = false;
      }
    });
  });

  // Instant AJAX Delete Photo
  document.querySelectorAll('.btn-photo-delete').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const photoId = btn.getAttribute('data-id');
      const title = btn.getAttribute('data-title') || 'esta fotografía';

      if (!confirm(`¿Estás seguro de eliminar definitivamente "${title}"?`)) {
        return;
      }

      const row = document.getElementById(`photoRow_${photoId}`);
      const icon = btn.querySelector('i');

      btn.disabled = true;
      icon.className = 'fas fa-spinner fa-spin';

      const fd = new FormData();
      fd.append('action', 'delete');
      fd.append('id', photoId);
      fd.append('ajax', '1');
      fd.append('csrf_token', CSRF);

      try {
        const res = await fetch(ACTION_URL, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: fd
        });
        const data = await res.json();

        if (data.success) {
          if (row) {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '0';
            row.style.transform = 'scale(0.95)';
            setTimeout(() => row.remove(), 300);
          }
        } else {
          alert(data.error || 'Error al eliminar la fotografía.');
          icon.className = 'fas fa-trash-alt';
          btn.disabled = false;
        }
      } catch (err) {
        window.location.reload();
      }
    });
  });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
