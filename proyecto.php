<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');
$id = intval($_GET['id'] ?? 0);

if (!empty($slug)) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $project = $stmt->fetch();
} elseif ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
} else {
    $project = null;
}

if (!$project) {
    header("Location: " . BASE_URL . "/proyectos");
    exit;
}

// Fetch Gallery Images
$stmtGallery = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY display_order ASC, id ASC");
$stmtGallery->execute([$project['id']]);
$gallery = $stmtGallery->fetchAll();

// Fetch 3 Other Projects
$stmtOther = $pdo->prepare("SELECT * FROM projects WHERE id != ? ORDER BY is_featured DESC, id DESC LIMIT 3");
$stmtOther->execute([$project['id']]);
$otherProjects = $stmtOther->fetchAll();

$pageTitle = $project['title'];
$pageDescription = !empty($project['description']) ? substr(strip_tags($project['description']), 0, 160) : $project['title'] . " — Cobertura profesional por Funktographer";

include __DIR__ . '/includes/header.php';
?>

<!-- Project Detail Hero Banner -->
<section class="project-hero-section">
  <div class="project-hero-bg" style="background-image: url('<?= BASE_URL ?>/<?= htmlspecialchars($project['cover_image']) ?>');"></div>
  <div class="project-hero-overlay"></div>
  <div class="hero-bg-glow"></div>

  <div class="container project-hero-content">
    <div style="margin-bottom: 24px;">
      <a href="<?= BASE_URL ?>/proyectos" class="btn btn-outline btn-sm">
        <i class="fas fa-arrow-left"></i> Volver a Proyectos
      </a>
    </div>

    <div class="project-meta-badges">
      <span class="section-tag" style="margin-bottom: 0;">
        <?= htmlspecialchars($project['category']) ?>
      </span>
      <?php if (!empty($project['client'])): ?>
        <span class="project-pill-info">
          <i class="far fa-building"></i> <?= htmlspecialchars($project['client']) ?>
        </span>
      <?php endif; ?>
      <?php if (!empty($project['event_date'])): ?>
        <span class="project-pill-info">
          <i class="far fa-calendar-alt"></i> <?= htmlspecialchars($project['event_date']) ?>
        </span>
      <?php endif; ?>
    </div>

    <h1 class="project-detail-title">
      <?= htmlspecialchars($project['title']) ?>
    </h1>
  </div>
</section>

<!-- Project Narrative & Details -->
<section class="section" style="padding-top: 50px; padding-bottom: 40px;">
  <div class="container">
    <div class="project-content-grid">
      <!-- Main Text Column -->
      <div class="project-narrative">
        <span class="section-tag">Sobre el Proyecto</span>
        <h2 class="project-subheading">La Historia Visual</h2>
        
        <?php if (!empty($project['description'])): ?>
          <div class="project-description-text">
            <?= nl2br(htmlspecialchars($project['description'])) ?>
          </div>
        <?php else: ?>
          <p class="project-description-text" style="color: var(--text-muted);">
            Cobertura visual especializada desarrollada por el equipo de Funktographer, cuidando meticulosamente cada plano, iluminación y detalle estético.
          </p>
        <?php endif; ?>
      </div>

      <!-- Sidebar Meta Box -->
      <aside class="project-sidebar-meta">
        <div class="project-info-card">
          <h4 style="font-family: var(--font-heading); font-size: 1.1rem; color: #fff; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            Ficha Técnica
          </h4>

          <div class="project-spec-item">
            <span class="spec-label">Categoría</span>
            <span class="spec-value" style="color: var(--primary); font-weight: 700;"><?= htmlspecialchars($project['category']) ?></span>
          </div>

          <?php if (!empty($project['client'])): ?>
            <div class="project-spec-item">
              <span class="spec-label">Cliente</span>
              <span class="spec-value"><?= htmlspecialchars($project['client']) ?></span>
            </div>
          <?php endif; ?>

          <?php if (!empty($project['event_date'])): ?>
            <div class="project-spec-item">
              <span class="spec-label">Fecha</span>
              <span class="spec-value"><?= htmlspecialchars($project['event_date']) ?></span>
            </div>
          <?php endif; ?>

          <div class="project-spec-item">
            <span class="spec-label">Producción</span>
            <span class="spec-value">Funktographer Chile</span>
          </div>

          <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border-color);">
            <a href="https://wa.me/<?= urlencode(get_setting('whatsapp', '56947573794')) ?>?text=Hola,%20me%20interesó%20el%20proyecto:%20<?= urlencode($project['title']) ?>.%20Quisiera%20cotizar%20algo%20similar." target="_blank" class="btn btn-primary" style="width: 100%; justify-content: center;">
              <i class="fab fa-whatsapp"></i> Cotizar Similar
            </a>
          </div>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- Gallery Showcase Grid -->
<?php if (!empty($gallery)): ?>
  <section class="section" style="padding-top: 20px; padding-bottom: 60px;">
    <div class="container">
      <div class="section-title-wrap" style="text-align: left; margin-bottom: 30px;">
        <span class="section-tag">Galería de Imágenes</span>
        <h2 class="section-title">Registro &amp; Fotografías</h2>
        <p class="section-subtitle">Haz clic en cualquier toma para visualizar en alta resolución</p>
      </div>

      <div class="gallery-grid">
        <?php foreach ($gallery as $g): ?>
          <div class="gallery-item" 
               data-full-src="<?= BASE_URL ?>/<?= htmlspecialchars($g['image_url']) ?>" 
               data-title="<?= htmlspecialchars($project['title']) ?> — <?= htmlspecialchars($g['caption'] ?: $project['category']) ?>">
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($g['image_url']) ?>" 
                 alt="<?= htmlspecialchars($g['caption'] ?: $project['title']) ?>" 
                 loading="lazy">
            <div class="gallery-overlay">
              <div class="gallery-info">
                <span class="gallery-cat"><?= htmlspecialchars($project['category']) ?></span>
                <h4 class="gallery-title"><?= htmlspecialchars($g['caption'] ?: $project['title']) ?></h4>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<!-- Dynamic Video Section & Extra Content (Optional) -->
<?php 
$hasExtraTitle = !empty($project['extra_title']);
$hasExtraContent = !empty($project['extra_content']);
$hasVideo = ($project['video_type'] !== 'none');
$ytEmbedUrl = ($project['video_type'] === 'youtube') ? get_youtube_embed_url($project['video_url']) : null;
$hasVideoFile = ($project['video_type'] === 'upload' && !empty($project['video_file']));

if ($hasExtraTitle || $hasExtraContent || $ytEmbedUrl || $hasVideoFile):
?>
  <section class="section project-video-section">
    <div class="container">
      <div class="project-video-box">
        <?php if ($hasExtraTitle): ?>
          <div class="section-title-wrap" style="margin-bottom: 24px;">
            <span class="section-tag"><i class="fas fa-film"></i> Producción Audiovisual</span>
            <h2 class="section-title"><?= htmlspecialchars($project['extra_title']) ?></h2>
            <?php if ($hasExtraContent): ?>
              <p class="section-subtitle" style="max-width: 800px; margin: 0 auto;">
                <?= nl2br(htmlspecialchars($project['extra_content'])) ?>
              </p>
            <?php endif; ?>
          </div>
        <?php elseif ($hasExtraContent): ?>
          <div style="max-width: 800px; margin: 0 auto 30px auto; text-align: center; color: var(--text-muted); font-size: 1.05rem;">
            <?= nl2br(htmlspecialchars($project['extra_content'])) ?>
          </div>
        <?php endif; ?>

        <!-- Video Player Display -->
        <?php if ($ytEmbedUrl): ?>
          <div class="video-responsive-wrap">
            <iframe src="<?= htmlspecialchars($ytEmbedUrl) ?>" 
                    title="<?= htmlspecialchars($project['title']) ?>" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    allowfullscreen 
                    loading="lazy"></iframe>
          </div>
        <?php elseif ($hasVideoFile): ?>
          <div class="video-player-wrap">
            <video controls preload="metadata" playsinline poster="<?= BASE_URL ?>/<?= htmlspecialchars($project['cover_image']) ?>">
              <source src="<?= BASE_URL ?>/<?= htmlspecialchars($project['video_file']) ?>" type="video/mp4">
              Tu navegador no soporta reproducción de video HTML5.
            </video>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<!-- Related / Other Projects -->
<?php if (!empty($otherProjects)): ?>
  <section class="section" style="background: #0d0d12; border-top: 1px solid var(--border-color);">
    <div class="container">
      <div class="section-title-wrap" style="text-align: left; margin-bottom: 36px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: gap; gap: 16px;">
        <div>
          <span class="section-tag">Más Trabajos</span>
          <h2 class="section-title" style="margin-bottom: 0;">Otros Proyectos Destacados</h2>
        </div>
        <a href="<?= BASE_URL ?>/proyectos" class="btn btn-outline btn-sm">
          Ver Todo el Catálogo <i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <div class="projects-grid">
        <?php foreach ($otherProjects as $op): ?>
          <article class="project-card">
            <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($op['slug']) ?>" class="project-thumb-wrap" style="display: block;">
              <img src="<?= BASE_URL ?>/<?= htmlspecialchars($op['cover_image']) ?>" alt="<?= htmlspecialchars($op['title']) ?>" class="project-thumb" loading="lazy">
              <span class="project-meta-pill"><?= htmlspecialchars($op['category']) ?></span>
            </a>
            <div class="project-card-body">
              <div class="project-date"><i class="far fa-calendar-alt"></i> <?= htmlspecialchars($op['event_date'] ?: 'Reciente') ?></div>
              <h3 class="project-card-title">
                <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($op['slug']) ?>" style="color: inherit;">
                  <?= htmlspecialchars($op['title']) ?>
                </a>
              </h3>
              <p class="project-card-desc"><?= htmlspecialchars($op['description']) ?></p>
              <div class="project-card-footer">
                <div class="project-client">Cliente: <span><?= htmlspecialchars($op['client'] ?: 'Confidencial') ?></span></div>
                <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($op['slug']) ?>" class="btn btn-outline btn-sm">
                  Ver Proyecto <i class="fas fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<!-- Bottom CTA Section -->
<section class="section" style="text-align: center; padding: 90px 0;">
  <div class="container" style="max-width: 780px;">
    <span class="section-tag">¿Hablemos de tu Proyecto?</span>
    <h2 class="section-title">Llevemos tu Narrativa Visual al Próximo Nivel</h2>
    <p class="section-subtitle" style="margin-bottom: 34px;">
      Cuéntanos los requerimientos de tu evento corporativo, cobertura editorial o producción gastronómica.
    </p>
    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
      <a href="<?= BASE_URL ?>/contacto" class="btn btn-primary">
        <i class="fas fa-paper-plane"></i> Cotizar Cobertura
      </a>
      <a href="https://wa.me/<?= urlencode(get_setting('whatsapp', '56947573794')) ?>" target="_blank" class="btn btn-outline">
        <i class="fab fa-whatsapp"></i> Chat Directo
      </a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
