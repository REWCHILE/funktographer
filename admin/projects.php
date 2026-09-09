<?php
$adminTitle = "Gestión de Proyectos";
require_once __DIR__ . '/header.php';

// Handle Delete Project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    // Fetch project
    $stmt = $pdo->prepare("SELECT cover_image FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $proj = $stmt->fetch();

    if ($proj) {
        // Fetch sub-images
        $stmtSub = $pdo->prepare("SELECT image_url FROM project_images WHERE project_id = ?");
        $stmtSub->execute([$id]);
        $subImgs = $stmtSub->fetchAll();

        // Delete files if in uploads
        if (str_starts_with($proj['cover_image'], 'uploads/')) {
            @unlink(ROOT_PATH . '/' . $proj['cover_image']);
        }
        foreach ($subImgs as $si) {
            if (str_starts_with($si['image_url'], 'uploads/')) {
                @unlink(ROOT_PATH . '/' . $si['image_url']);
            }
        }

        // Delete from DB (foreign key cascades sub-images)
        $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
        set_flash('success', 'Proyecto eliminado correctamente.');
    }
    header("Location: " . BASE_URL . "/admin/projects");
    exit;
}

// Fetch all projects with sub-image count
$sql = "SELECT p.*, COUNT(pi.id) as total_gallery 
        FROM projects p 
        LEFT JOIN project_images pi ON p.id = pi.project_id 
        GROUP BY p.id 
        ORDER BY p.display_order ASC, p.id DESC";
$projects = $pdo->query($sql)->fetchAll();
?>

<div class="adm-header">
  <div>
    <h1 class="adm-title">Proyectos &amp; Coberturas</h1>
    <p class="adm-subtitle">Crea, edita y organiza los proyectos mostrados en el catálogo</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/project-form" class="adm-btn adm-btn-primary">
    <i class="fas fa-plus"></i> Nuevo Proyecto
  </a>
</div>

<div class="adm-card">
  <div class="adm-card-header">
    <h3 class="adm-card-title">Listado de Proyectos (<?= count($projects) ?>)</h3>
  </div>

  <?php if (empty($projects)): ?>
    <p style="color: var(--adm-muted); text-align: center; padding: 40px 0;">No hay proyectos registrados. Haz clic en "Nuevo Proyecto".</p>
  <?php else: ?>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th style="width: 80px;">Portada</th>
            <th>Título del Proyecto</th>
            <th>Categoría</th>
            <th>Cliente / Fecha</th>
            <th>Fotos Galería</th>
            <th>Destacado</th>
            <th style="text-align: right;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($projects as $p): ?>
            <tr>
              <td>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['cover_image']) ?>" alt="" class="adm-thumb">
              </td>
              <td>
                <strong><?= htmlspecialchars($p['title']) ?></strong><br>
                <span style="font-size: 0.75rem; color: var(--adm-muted);">slug: <?= htmlspecialchars($p['slug']) ?></span>
              </td>
              <td>
                <span class="adm-badge" style="background: rgba(121, 56, 226, 0.2); color: #c4a1ff;">
                  <?= htmlspecialchars($p['category']) ?>
                </span>
              </td>
              <td>
                <?= htmlspecialchars($p['client'] ?: '—') ?><br>
                <span style="font-size: 0.8rem; color: var(--adm-muted);"><?= htmlspecialchars($p['event_date'] ?: '—') ?></span>
              </td>
              <td>
                <div style="margin-bottom: 4px;">
                  <i class="fas fa-images" style="color: var(--adm-primary); margin-right: 4px;"></i>
                  <?= $p['total_gallery'] ?> fotos
                </div>
                <?php if (($p['video_type'] ?? '') === 'youtube'): ?>
                  <span class="adm-badge" style="background: rgba(239, 68, 68, 0.2); color: #f87171; font-size: 0.72rem;">
                    <i class="fab fa-youtube"></i> YouTube
                  </span>
                <?php elseif (($p['video_type'] ?? '') === 'upload'): ?>
                  <span class="adm-badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399; font-size: 0.72rem;">
                    <i class="fas fa-video"></i> Video MP4
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($p['is_featured']): ?>
                  <span class="pill-active">En Portada</span>
                <?php else: ?>
                  <span class="pill-inactive">Normal</span>
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 8px;">
                  <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($p['slug']) ?>" target="_blank" class="adm-btn adm-btn-outline adm-btn-sm" title="Ver en la web">
                    <i class="fas fa-external-link-alt"></i>
                  </a>
                  <a href="<?= BASE_URL ?>/admin/project-form?id=<?= $p['id'] ?>" class="adm-btn adm-btn-outline adm-btn-sm" title="Editar proyecto">
                    <i class="fas fa-edit"></i> Editar
                  </a>
                  <form action="<?= BASE_URL ?>/admin/projects" method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm" title="Eliminar proyecto" data-confirm="¿Seguro que deseas eliminar este proyecto y todas sus fotos?">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
