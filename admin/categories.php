<?php
$adminTitle = "Gestión de Categorías y Filtros";
require_once __DIR__ . '/header.php';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Token CSRF inválido.');
        header("Location: " . BASE_URL . "/admin/categories");
        exit;
    }

    $action = $_POST['action'] ?? '';

    // 1. Add Category
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $order = intval($_POST['display_order'] ?? 0);

        if (empty($name)) {
            set_flash('danger', 'El nombre de la categoría es obligatorio.');
        } else {
            $slug = !empty($slug) ? slugify($slug) : slugify($name);
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug, display_order, is_active) VALUES (?, ?, ?, 1)");
                $stmt->execute([$name, $slug, $order]);
                set_flash('success', "Categoría '$name' creada con éxito.");
            } catch (Exception $e) {
                set_flash('danger', "Error: la categoría o slug '$slug' ya existe.");
            }
        }
        header("Location: " . BASE_URL . "/admin/categories");
        exit;
    }

    // 2. Edit Category
    if ($action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $order = intval($_POST['display_order'] ?? 0);

        if (empty($name) || $id <= 0) {
            set_flash('danger', 'Datos incompletos para actualizar la categoría.');
        } else {
            $slug = !empty($slug) ? slugify($slug) : slugify($name);
            try {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, display_order = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $order, $id]);
                set_flash('success', "Categoría '$name' actualizada correctamente.");
            } catch (Exception $e) {
                set_flash('danger', "Error: el slug '$slug' ya está siendo utilizado.");
            }
        }
        header("Location: " . BASE_URL . "/admin/categories");
        exit;
    }

    // 3. Batch Reorder (Update display orders)
    if ($action === 'reorder') {
        $orders = $_POST['orders'] ?? [];
        if (is_array($orders)) {
            $stmtUp = $pdo->prepare("UPDATE categories SET display_order = ? WHERE id = ?");
            foreach ($orders as $catId => $ordVal) {
                $stmtUp->execute([intval($ordVal), intval($catId)]);
            }
            set_flash('success', 'Orden de categorías actualizado. Los cambios ya se reflejan en el Home y Portafolio.');
        }
        header("Location: " . BASE_URL . "/admin/categories");
        exit;
    }

    // 4. Toggle Active Status
    if ($action === 'toggle') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE categories SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Estado de visibilidad de la categoría actualizado.');
        header("Location: " . BASE_URL . "/admin/categories");
        exit;
    }

    // 5. Delete Category
    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            set_flash('success', 'Categoría eliminada del catálogo.');
        }
        header("Location: " . BASE_URL . "/admin/categories");
        exit;
    }
}

// Fetch all categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY display_order ASC, id ASC")->fetchAll();

// Count projects and home images per category
$projectCounts = [];
$homeImgCounts = [];
foreach ($categories as $cat) {
    $cSlug = $cat['slug'];
    $cName = $cat['name'];
    $pStmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE category = ? OR category = ?");
    $pStmt->execute([$cSlug, $cName]);
    $projectCounts[$cat['id']] = $pStmt->fetchColumn();

    $hStmt = $pdo->prepare("SELECT COUNT(*) FROM home_images WHERE category = ? OR category = ?");
    $hStmt->execute([$cSlug, $cName]);
    $homeImgCounts[$cat['id']] = $hStmt->fetchColumn();
}

$editId = intval($_GET['edit'] ?? 0);
$editCategory = null;
if ($editId > 0) {
    $stmtE = $pdo->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
    $stmtE->execute([$editId]);
    $editCategory = $stmtE->fetch();
}
?>

<main class="adm-main">
  <!-- Top Bar -->
  <div class="adm-topbar">
    <div class="adm-breadcrumbs">
      <a href="<?= BASE_URL ?>/admin/index">Dashboard</a>
      <i class="fas fa-chevron-right"></i>
      <span>Categorías y Filtros</span>
    </div>
    <div class="adm-user-info">
      <a href="<?= BASE_URL ?>/" target="_blank" class="btn btn-outline btn-sm">
        <i class="fas fa-eye"></i> Ver Home
      </a>
      <a href="<?= BASE_URL ?>/proyectos" target="_blank" class="btn btn-outline btn-sm">
        <i class="fas fa-th-large"></i> Ver Portafolio
      </a>
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
          <i class="fas fa-tags" style="color: var(--adm-primary);"></i> Categorías y Filtros (Pills)
        </h1>
        <p style="color: var(--adm-text-muted); font-size: 0.92rem;">
          Controla qué categorías existen y <strong>en qué orden aparecen los botones de filtro (pills)</strong> en el Home y Portafolio.
        </p>
      </div>
      <?php if ($editCategory): ?>
        <a href="<?= BASE_URL ?>/admin/categories" class="btn btn-outline btn-sm">
          <i class="fas fa-times"></i> Cancelar Edición
        </a>
      <?php endif; ?>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1.8fr; gap: 28px; align-items: flex-start;">

      <!-- Left Column: Add / Edit Category Form -->
      <div class="adm-card">
        <h3 style="font-size: 1.15rem; color: #fff; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--adm-border);">
          <i class="fas <?= $editCategory ? 'fa-pen' : 'fa-plus-circle' ?>" style="color: var(--adm-primary);"></i>
          <?= $editCategory ? 'Editar Categoría #' . $editCategory['id'] : 'Nueva Categoría' ?>
        </h3>

        <form method="POST" action="<?= BASE_URL ?>/admin/categories">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="action" value="<?= $editCategory ? 'edit' : 'add' ?>">
          <?php if ($editCategory): ?>
            <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
          <?php endif; ?>

          <div class="adm-form-group">
            <label class="adm-label">Nombre de la Categoría *</label>
            <input type="text" name="name" class="adm-input" required 
                   placeholder="Ej: Fotografía Aérea y Drones" 
                   value="<?= htmlspecialchars($editCategory['name'] ?? '') ?>"
                   id="catNameInput">
          </div>

          <div class="adm-form-group">
            <label class="adm-label">Clave / Slug (Filtro interno)</label>
            <input type="text" name="slug" class="adm-input" 
                   placeholder="Ej: Aerea o eventos" 
                   value="<?= htmlspecialchars($editCategory['slug'] ?? '') ?>"
                   id="catSlugInput">
            <small style="color: var(--adm-text-muted); font-size: 0.78rem; display: block; margin-top: 4px;">
              Si lo dejas vacío, se autogenera automáticamente.
            </small>
          </div>

          <div class="adm-form-group">
            <label class="adm-label">Orden de Visualización (Pills)</label>
            <input type="number" name="display_order" class="adm-input" 
                   value="<?= intval($editCategory['display_order'] ?? count($categories) + 1) ?>" min="1">
            <small style="color: var(--adm-text-muted); font-size: 0.78rem; display: block; margin-top: 4px;">
              El número <strong>1</strong> será el primer botón que se muestre después de "Todos".
            </small>
          </div>

          <div style="margin-top: 22px;">
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; background: var(--adm-primary); color: #09090c; font-weight: 700;">
              <i class="fas fa-save"></i> <?= $editCategory ? 'Guardar Cambios' : 'Crear Categoría' ?>
            </button>
          </div>
        </form>
      </div>

      <!-- Right Column: Current Categories List with Order Controls -->
      <div class="adm-card">
        <form method="POST" action="<?= BASE_URL ?>/admin/categories">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="action" value="reorder">

          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--adm-border);">
            <h3 style="font-size: 1.15rem; color: #fff; margin: 0;">
              Orden de Píldoras en Home y Portafolio
            </h3>
            <button type="submit" class="btn btn-primary btn-sm" style="background: var(--adm-primary); color: #09090c; font-weight: 700;">
              <i class="fas fa-sort-numeric-down"></i> Guardar Nuevo Orden
            </button>
          </div>

          <p style="color: var(--adm-text-muted); font-size: 0.85rem; margin-bottom: 16px;">
            Edita los números de orden a continuación y haz clic en <strong>"Guardar Nuevo Orden"</strong> para reordenar las píldoras en vivo.
          </p>

          <div style="display: flex; flex-direction: column; gap: 10px;">
            <?php foreach ($categories as $c): ?>
              <div style="display: flex; align-items: center; gap: 14px; padding: 12px 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--adm-border); border-radius: 10px;">
                
                <!-- Order input -->
                <div style="width: 60px; flex-shrink: 0;">
                  <input type="number" name="orders[<?= $c['id'] ?>]" value="<?= intval($c['display_order']) ?>" 
                         class="adm-input" style="padding: 6px 10px; text-align: center; font-weight: 700; border-color: rgba(255,197,1,0.4);" min="1">
                </div>

                <!-- Category Info -->
                <div style="flex-grow: 1; min-width: 0;">
                  <div style="display: flex; align-items: center; gap: 8px;">
                    <strong style="color: #fff; font-size: 1rem;"><?= htmlspecialchars($c['name']) ?></strong>
                    <code style="font-size: 0.75rem; color: var(--adm-primary); background: rgba(255,197,1,0.1); padding: 2px 6px; border-radius: 4px;">
                      <?= htmlspecialchars($c['slug']) ?>
                    </code>
                  </div>
                  <div style="font-size: 0.78rem; color: var(--adm-text-muted); margin-top: 3px;">
                    <span><i class="fas fa-folder"></i> <?= $projectCounts[$c['id']] ?? 0 ?> proyectos</span> &bull; 
                    <span><i class="fas fa-image"></i> <?= $homeImgCounts[$c['id']] ?? 0 ?> fotos home</span>
                  </div>
                </div>

                <!-- Status Toggle -->
                <form method="POST" action="<?= BASE_URL ?>/admin/categories" style="margin: 0;">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
                  <button type="submit" class="adm-badge" style="cursor: pointer; border: none; background: <?= $c['is_active'] ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)' ?>; color: <?= $c['is_active'] ? '#34d399' : '#f87171' ?>;">
                    <?= $c['is_active'] ? 'Visible' : 'Oculto' ?>
                  </button>
                </form>

                <!-- Actions -->
                <div style="display: flex; gap: 6px;">
                  <a href="<?= BASE_URL ?>/admin/categories?edit=<?= $c['id'] ?>" class="btn btn-outline btn-sm" title="Editar categoría" style="padding: 6px 10px;">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form method="POST" action="<?= BASE_URL ?>/admin/categories" style="margin: 0;" onsubmit="return confirm('¿Eliminar esta categoría? Los proyectos que la usen mantendrán su texto asignado.');">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <button type="submit" class="btn btn-outline btn-sm" title="Eliminar categoría" style="padding: 6px 10px; color: #f87171;">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </div>

              </div>
            <?php endforeach; ?>
          </div>
        </form>
      </div>

    </div>

  </div>
</main>

<script>
  // Auto-slug generator on input
  const catNameInput = document.getElementById('catNameInput');
  const catSlugInput = document.getElementById('catSlugInput');
  if (catNameInput && catSlugInput) {
    catNameInput.addEventListener('input', () => {
      if (!catSlugInput.dataset.manual) {
        catSlugInput.value = catNameInput.value
          .toLowerCase()
          .trim()
          .replace(/[\s\W-]+/g, '-')
          .replace(/^-+|-+$/g, '');
      }
    });
    catSlugInput.addEventListener('input', () => {
      catSlugInput.dataset.manual = 'true';
    });
  }
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
