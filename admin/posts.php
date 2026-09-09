<?php
$adminTitle = "Gestión de Artículos del Blog";
require_once __DIR__ . '/header.php';

// Handle Toggle Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_status') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT status FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetchColumn();
    if ($current) {
        $newStatus = ($current === 'published') ? 'draft' : 'published';
        $pdo->prepare("UPDATE blog_posts SET status = ? WHERE id = ?")->execute([$newStatus, $id]);
        set_flash('success', 'Estado del artículo actualizado a: ' . ($newStatus === 'published' ? 'Publicado' : 'Borrador'));
    }
    header("Location: " . BASE_URL . "/admin/posts");
    exit;
}

// Handle Toggle Featured
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_featured') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT is_featured FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetchColumn();
    if ($current !== false) {
        $newVal = $current ? 0 : 1;
        $pdo->prepare("UPDATE blog_posts SET is_featured = ? WHERE id = ?")->execute([$newVal, $id]);
        set_flash('success', $newVal ? 'Artículo marcado como Destacado en el Blog.' : 'Artículo desmarcado como Destacado.');
    }
    header("Location: " . BASE_URL . "/admin/posts");
    exit;
}

// Handle Delete Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT cover_image FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if ($post) {
        if (str_starts_with($post['cover_image'], 'uploads/blog/')) {
            @unlink(ROOT_PATH . '/' . $post['cover_image']);
        }
        $pdo->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$id]);
        set_flash('success', 'Artículo eliminado correctamente.');
    }
    header("Location: " . BASE_URL . "/admin/posts");
    exit;
}

// Fetch categories for filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY display_order ASC, name ASC")->fetchAll();

// Filtering
$filterCat = trim($_GET['cat'] ?? '');
$filterStatus = trim($_GET['status'] ?? '');
$filterSearch = trim($_GET['q'] ?? '');

$where = [];
$params = [];

if ($filterCat !== '') {
    $where[] = "p.category = ?";
    $params[] = $filterCat;
}
if ($filterStatus !== '') {
    $where[] = "p.status = ?";
    $params[] = $filterStatus;
}
if ($filterSearch !== '') {
    $where[] = "(p.title LIKE ? OR p.excerpt LIKE ? OR p.content LIKE ?)";
    $params[] = "%$filterSearch%";
    $params[] = "%$filterSearch%";
    $params[] = "%$filterSearch%";
}

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$sql = "SELECT p.*, c.name as category_name 
        FROM blog_posts p 
        LEFT JOIN categories c ON p.category_id = c.id 
        {$whereSql} 
        ORDER BY p.is_featured DESC, p.published_at DESC, p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>

<div class="adm-header">
  <div>
    <h1 class="adm-title">Blog &amp; Artículos Editoriales</h1>
    <p class="adm-subtitle">Publica casos de estudio, consejos fotográficos y novedades para atraer clientes y posicionar en Google</p>
  </div>
  <div style="display: flex; gap: 12px; align-items: center;">
    <a href="<?= BASE_URL ?>/blog" target="_blank" class="adm-btn adm-btn-secondary">
      <i class="fas fa-external-link-alt"></i> Ver Blog en Vivo
    </a>
    <a href="<?= BASE_URL ?>/admin/post-form" class="adm-btn adm-btn-primary">
      <i class="fas fa-plus"></i> Nuevo Artículo
    </a>
  </div>
</div>

<!-- Filters Bar -->
<div class="adm-card" style="margin-bottom: 20px; padding: 16px 20px;">
  <form method="GET" action="<?= BASE_URL ?>/admin/posts" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: center;">
    <div style="flex: 1; min-width: 220px; position: relative;">
      <input type="text" name="q" class="adm-input" placeholder="Buscar por título o contenido..." value="<?= htmlspecialchars($filterSearch) ?>" style="padding-left: 36px;">
      <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--adm-muted);"></i>
    </div>

    <div style="min-width: 180px;">
      <select name="cat" class="adm-input">
        <option value="">Todas las categorías</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat['name']) ?>" <?= ($filterCat === $cat['name']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="min-width: 150px;">
      <select name="status" class="adm-input">
        <option value="">Todos los estados</option>
        <option value="published" <?= ($filterStatus === 'published') ? 'selected' : '' ?>>Publicados</option>
        <option value="draft" <?= ($filterStatus === 'draft') ? 'selected' : '' ?>>Borradores</option>
      </select>
    </div>

    <button type="submit" class="adm-btn adm-btn-secondary" style="padding: 10px 18px;">
      <i class="fas fa-filter"></i> Filtrar
    </button>
    <?php if ($filterCat !== '' || $filterStatus !== '' || $filterSearch !== ''): ?>
      <a href="<?= BASE_URL ?>/admin/posts" class="adm-btn adm-btn-secondary" style="color: #f87171; border-color: rgba(239, 68, 68, 0.3);">
        <i class="fas fa-times"></i> Limpiar
      </a>
    <?php endif; ?>
  </form>
</div>

<!-- Posts Table -->
<div class="adm-card">
  <div class="adm-card-header">
    <h3 class="adm-card-title">Listado de Artículos (<?= count($posts) ?>)</h3>
  </div>

  <?php if (empty($posts)): ?>
    <div style="text-align: center; padding: 50px 20px; color: var(--adm-muted);">
      <i class="fas fa-newspaper" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
      <p style="font-size: 1.1rem; margin-bottom: 12px;">No se encontraron artículos con los filtros aplicados.</p>
      <a href="<?= BASE_URL ?>/admin/post-form" class="adm-btn adm-btn-primary">
        <i class="fas fa-plus"></i> Redactar el Primer Artículo
      </a>
    </div>
  <?php else: ?>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th style="width: 80px;">Portada</th>
            <th>Artículo &amp; URL</th>
            <th>Categoría</th>
            <th>Lectura &amp; Vistas</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th style="text-align: right;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $p): ?>
            <tr>
              <td>
                <?php if ($p['cover_image']): ?>
                  <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['cover_image']) ?>" alt="" class="adm-thumb" style="width: 70px; height: 50px; object-fit: cover; border-radius: 6px;">
                <?php else: ?>
                  <div style="width: 70px; height: 50px; background: rgba(255,255,255,0.05); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--adm-muted);">
                    <i class="fas fa-image"></i>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <div style="display: flex; align-items: center; gap: 8px;">
                  <strong><?= htmlspecialchars($p['title']) ?></strong>
                  <?php if ($p['is_featured']): ?>
                    <span class="adm-badge" style="background: rgba(255, 197, 1, 0.2); color: #FFC501; font-size: 0.7rem;" title="Artículo Destacado">
                      <i class="fas fa-star"></i> Destacado
                    </span>
                  <?php endif; ?>
                </div>
                <div style="font-size: 0.78rem; color: var(--adm-muted); margin-top: 4px;">
                  /blog/<span style="color: var(--adm-primary);"><?= htmlspecialchars($p['slug']) ?></span>
                </div>
              </td>
              <td>
                <span class="adm-badge" style="background: rgba(121, 56, 226, 0.2); color: #c4a1ff;">
                  <?= htmlspecialchars($p['category_name'] ?: $p['category']) ?>
                </span>
              </td>
              <td>
                <div style="font-size: 0.85rem;">
                  <i class="fas fa-clock" style="color: var(--adm-muted); margin-right: 4px;"></i> <?= $p['reading_time'] ?> min
                </div>
                <div style="font-size: 0.78rem; color: var(--adm-muted); margin-top: 2px;">
                  <i class="fas fa-eye" style="margin-right: 4px;"></i> <?= number_format($p['views']) ?> visitas
                </div>
              </td>
              <td>
                <form method="POST" action="<?= BASE_URL ?>/admin/posts" style="display: inline;">
                  <input type="hidden" name="action" value="toggle_status">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <?php if ($p['status'] === 'published'): ?>
                    <button type="submit" class="adm-badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: none; cursor: pointer;" title="Clic para pasar a Borrador">
                      <i class="fas fa-check-circle"></i> Publicado
                    </button>
                  <?php else: ?>
                    <button type="submit" class="adm-badge" style="background: rgba(255, 255, 255, 0.1); color: var(--adm-muted); border: none; cursor: pointer;" title="Clic para Publicar">
                      <i class="fas fa-file-alt"></i> Borrador
                    </button>
                  <?php endif; ?>
                </form>
              </td>
              <td>
                <span style="font-size: 0.85rem; color: var(--adm-text);">
                  <?= date('d/m/Y', strtotime($p['published_at'])) ?>
                </span>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <!-- Toggle Featured button -->
                <form method="POST" action="<?= BASE_URL ?>/admin/posts" style="display: inline;">
                  <input type="hidden" name="action" value="toggle_featured">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" class="adm-btn adm-btn-secondary" style="padding: 6px 10px; font-size: 0.8rem; color: <?= $p['is_featured'] ? '#FFC501' : 'var(--adm-muted)' ?>;" title="<?= $p['is_featured'] ? 'Quitar de Destacados' : 'Marcar como Destacado' ?>">
                    <i class="fas fa-star"></i>
                  </button>
                </form>

                <!-- View on frontend -->
                <a href="<?= BASE_URL ?>/blog/<?= urlencode($p['slug']) ?>" target="_blank" class="adm-btn adm-btn-secondary" style="padding: 6px 10px; font-size: 0.8rem;" title="Ver en la Web">
                  <i class="fas fa-external-link-alt"></i>
                </a>

                <!-- Edit -->
                <a href="<?= BASE_URL ?>/admin/post-form?id=<?= $p['id'] ?>" class="adm-btn adm-btn-primary" style="padding: 6px 10px; font-size: 0.8rem;" title="Editar Artículo">
                  <i class="fas fa-edit"></i>
                </a>

                <!-- Delete -->
                <form method="POST" action="<?= BASE_URL ?>/admin/posts" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este artículo? Esta acción no se puede deshacer.');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" class="adm-btn adm-btn-danger" style="padding: 6px 10px; font-size: 0.8rem;" title="Eliminar">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
