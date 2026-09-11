<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Proyectos y Portafolio Fotográfico";
$pageDescription = "Explora los proyectos fotográficos y audiovisuales de Funktographer: eventos corporativos, gastronomía de autor y retratos editoriales.";
$pageImage = 'uploads/home/Fotogo-Ponencia-cisco-mining-summit-2024-Funktographer.jpg';
$ogType = 'website';
$pageCanonical = BASE_URL . '/proyectos';

// Fetch active dynamic categories ordered by display_order
try {
    $catStmt = $pdo ? $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order ASC, id ASC") : null;
    $categories = $catStmt ? $catStmt->fetchAll() : [];
} catch (Exception $e) {
    $categories = [];
}

// Category map for pretty display and filter matching
$categoryMap = [];
foreach ($categories as $cat) {
    $categoryMap[$cat['slug']] = $cat['name'];
    $categoryMap[$cat['name']] = $cat['name'];
}

// Fetch all projects
try {
    $stmt = $pdo ? $pdo->query("SELECT * FROM projects ORDER BY display_order ASC, id DESC") : null;
    $projects = $stmt ? $stmt->fetchAll() : [];
} catch (Exception $e) {
    $projects = [];
}

// Attach images to each project
$projectIds = array_column($projects, 'id');
$projectImagesMap = [];
if (!empty($projectIds)) {
    $in = implode(',', array_map('intval', $projectIds));
    $stmtImg = $pdo->query("SELECT * FROM project_images WHERE project_id IN ($in) ORDER BY display_order ASC");
    while ($row = $stmtImg->fetch()) {
        $projectImagesMap[$row['project_id']][] = $row;
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Projects Page Header -->
<section class="hero-section hero-page-header">
  <div class="hero-video-bg">
    <video autoplay muted loop playsinline poster="<?= BASE_URL ?>/assets/video/hero-poster.jpg">
      <source src="<?= BASE_URL ?>/assets/video/hero-bg.mp4" type="video/mp4">
    </video>
    <div class="hero-video-overlay"></div>
  </div>
  <div class="hero-bg-glow"></div>
  <div class="container hero-content">
    <span class="section-tag">Portafolio Profesional</span>
    <h1 class="hero-title">Proyectos y Coberturas</h1>
    <p class="hero-description">
      Cada proyecto es un relato único. Conoce cómo transformamos eventos corporativos, creaciones culinarias y sesiones editoriales en piezas visuales de alto impacto.
    </p>

    <!-- Filters (Dynamic Pills Ordered by Admin) -->
    <div class="filter-container">
      <button type="button" class="filter-btn active" data-filter="all">Todos los Proyectos</button>
      <?php foreach ($categories as $cat): ?>
        <button type="button" class="filter-btn" data-filter="<?= htmlspecialchars($cat['slug']) ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Projects Grid -->
<section class="section" style="padding-top: 0;">
  <div class="container">
    <?php if (empty($projects)): ?>
      <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
        <i class="fas fa-camera-retro" style="font-size: 3rem; color: var(--primary); margin-bottom: 20px; display: block;"></i>
        <h3 style="font-family: var(--font-title); font-size: 1.8rem; color: #fff; margin-bottom: 10px;">Próximamente Más Proyectos</h3>
        <p>Estamos preparando nuevas galerías y coberturas para ti.</p>
      </div>
    <?php else: ?>
      <div class="projects-grid">
        <?php foreach ($projects as $p): 
          $gallery = $projectImagesMap[$p['id']] ?? [];
          $catName = $categoryMap[$p['category']] ?? $p['category'];
          $cleanDesc = trim(strip_tags($p['description'] ?? ''));
          if (mb_strlen($cleanDesc) > 160) {
              $cleanDesc = mb_substr($cleanDesc, 0, 157) . '...';
          }
        ?>
          <article class="project-card" id="proj-<?= $p['id'] ?>" data-category="<?= htmlspecialchars($p['category'] . ' ' . $catName) ?>">
            <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($p['slug']) ?>" class="project-thumb-wrap" style="display: block;">
              <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['cover_image']) ?>" 
                   alt="<?= htmlspecialchars($p['title']) ?>" 
                   class="project-thumb" 
                   loading="lazy">
              <span class="project-meta-pill"><?= htmlspecialchars($catName) ?></span>
            </a>

            <div class="project-card-body">
              <div class="project-date">
                <i class="far fa-calendar-alt"></i> <?= htmlspecialchars($p['event_date'] ?: 'Reciente') ?>
                <?php if ($p['client']): ?>
                  &bull; <i class="far fa-building"></i> <?= htmlspecialchars($p['client']) ?>
                <?php endif; ?>
              </div>

              <h2 class="project-card-title">
                <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($p['slug']) ?>" style="color: inherit;">
                  <?= htmlspecialchars($p['title']) ?>
                </a>
              </h2>
              <p class="project-card-desc"><?= htmlspecialchars($cleanDesc) ?></p>

            <!-- Project Mini Gallery / Thumbnails -->
            <?php if (!empty($gallery)): ?>
              <div style="margin: 15px 0 20px 0;">
                <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--text-dim); margin-bottom: 8px;">
                  Galería del Proyecto (<?= count($gallery) ?> fotos):
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                  <?php foreach (array_slice($gallery, 0, 5) as $g): ?>
                    <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($p['slug']) ?>" 
                       style="width: 58px; height: 58px; margin: 0; border-radius: 6px; overflow: hidden; display: inline-block;">
                      <img src="<?= BASE_URL ?>/<?= htmlspecialchars($g['image_url']) ?>" 
                           alt="<?= htmlspecialchars($g['caption'] ?: $p['title']) ?>" 
                           style="width: 100%; height: 100%; object-fit: cover;">
                    </a>
                  <?php endforeach; ?>
                  <?php if (count($gallery) > 5): ?>
                    <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($p['slug']) ?>" style="width: 58px; height: 58px; border-radius: 6px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">
                      +<?= count($gallery) - 5 ?>
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>

            <div class="project-card-footer">
              <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($p['slug']) ?>" class="btn btn-primary btn-sm">
                Ver Proyecto <i class="fas fa-arrow-right"></i>
              </a>
              <a href="https://wa.me/<?= urlencode(get_setting('whatsapp', '56947573794')) ?>?text=Hola,%20me%20interesa%20un%20proyecto%20similar%20a:%20<?= urlencode($p['title']) ?>" target="_blank" class="btn btn-outline btn-sm">
                <i class="fab fa-whatsapp"></i> Cotizar
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Call to Action Banner -->
<section class="section" style="background: linear-gradient(180deg, var(--bg-dark) 0%, #120e20 100%); text-align: center;">
  <div class="container">
    <div class="section-title-wrap" style="margin-bottom: 30px;">
      <span class="section-tag">¿Planeas tu próximo evento o producción?</span>
      <h2 class="section-title">Trabajemos Juntos</h2>
      <p class="section-subtitle">
        Contáctanos para revisar requerimientos específicos, cronograma y cotización personalizada sin compromiso.
      </p>
    </div>
    <a href="<?= BASE_URL ?>/contacto" class="btn btn-primary">
      <i class="fas fa-envelope"></i> Iniciar Conversación
    </a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
