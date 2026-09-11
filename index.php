<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Fotografía y video para empresas y marcas";
$pageDescription = "Soy Emmanuel Ramírez \"Funktographer\", creo imágenes para empresas, marcas y restaurantes a partir de lo que necesitan comunicar.";
$pageImage = 'uploads/home/Foto-evento-Algo-electrico-con-quimica-Funktographer-24.jpg';
$ogType = 'website';
$pageCanonical = BASE_URL . '/';

// Fetch active home images from MySQL ordered by display_order
try {
    $stmt = $pdo ? $pdo->query("SELECT * FROM home_images WHERE is_active = 1 ORDER BY display_order ASC, id DESC") : null;
    $homeImages = $stmt ? $stmt->fetchAll() : [];
} catch (Exception $e) {
    $homeImages = [];
}

// Fetch featured projects for the highlight section
try {
    $stmtProj = $pdo ? $pdo->query("SELECT * FROM projects WHERE is_featured = 1 ORDER BY display_order ASC LIMIT 3") : null;
    $featuredProjects = $stmtProj ? $stmtProj->fetchAll() : [];
} catch (Exception $e) {
    $featuredProjects = [];
}

// Fetch dynamic categories ordered by display_order for filter pills
try {
    $catStmt = $pdo ? $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order ASC, id ASC") : null;
    $categories = $catStmt ? $catStmt->fetchAll() : [];
} catch (Exception $e) {
    $categories = [];
}

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="hero-video-bg">
    <video autoplay muted loop playsinline poster="<?= BASE_URL ?>/assets/video/hero-poster.jpg">
      <source src="<?= BASE_URL ?>/assets/video/hero-bg.mp4" type="video/mp4">
    </video>
    <div class="hero-video-overlay"></div>
  </div>
  <div class="hero-bg-glow"></div>
  <div class="container hero-content">
    <div class="hero-badge">
      <span class="hero-badge-dot"></span> Santiago de Chile | Cobertura Nacional e Internacional
    </div>
    <h1 class="hero-title">
      Fotografía y video para <span class="highlight">empresas y marcas</span>
    </h1>
    <p class="hero-description">
      Soy Emmanuel Ramírez &ldquo;Funktographer&rdquo;, creo imágenes para empresas, marcas y restaurantes a partir de lo que necesitan comunicar. Desde retratos y eventos corporativos hasta productos y gastronomía, cada proyecto comienza desde tu idea y con mi ayuda creativa podrás materializarlo.
    </p>
    <div class="hero-actions">
      <a href="<?= BASE_URL ?>/proyectos" class="btn btn-primary">
        <i class="fas fa-th-large"></i> Ver Proyectos
      </a>
      <a href="<?= BASE_URL ?>/contacto" class="btn btn-outline">
        <i class="fas fa-paper-plane"></i> Cotizar Sesión
      </a>
    </div>
  </div>
</section>

<!-- Gallery Showcase Section -->
<section class="section" style="padding-top: 20px;">
  <div class="container-fluid">
    <div class="section-title-wrap">
      <span class="section-tag">Galería Destacada</span>
      <h2 class="section-title">Portafolio Visual</h2>
      <p class="section-subtitle">Explora una selección de nuestras capturas más recientes</p>
    </div>

    <!-- Category Filters (Dynamic Pills Ordered by Admin) -->
    <div class="filter-container">
      <button type="button" class="filter-btn active" data-filter="all">Todos</button>
      <?php foreach ($categories as $cat): ?>
        <button type="button" class="filter-btn" data-filter="<?= htmlspecialchars($cat['slug']) ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Dynamic Masonry Gallery -->
    <div class="gallery-grid">
      <?php foreach ($homeImages as $img): ?>
        <div class="gallery-item" 
             data-category="<?= htmlspecialchars($img['category']) ?>"
             data-full-src="<?= BASE_URL ?>/<?= htmlspecialchars($img['image_url']) ?>"
             data-title="<?= htmlspecialchars($img['title']) ?>">
          <img src="<?= BASE_URL ?>/<?= htmlspecialchars($img['image_url']) ?>" 
               alt="<?= htmlspecialchars($img['title']) ?>" 
               loading="lazy">
          <div class="gallery-overlay">
            <span class="gallery-badge"><?= htmlspecialchars($img['category']) ?></span>
            <div class="gallery-title"><?= htmlspecialchars($img['title']) ?></div>
            <div class="gallery-expand-icon">
              <i class="fas fa-expand-arrows-alt"></i>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Action Cards Section -->
<section class="section" style="padding-top: 40px; padding-bottom: 70px;">
  <div class="container">
    <div class="action-cards-grid">
      <!-- Card 1: Portafolio -->
      <div class="action-card">
        <div class="action-card-icon">
          <i class="fas fa-camera"></i>
        </div>
        <div class="action-card-body">
          <h5>Ver Portafolio</h5>
          <p>Conoce en profundidad nuestros proyectos completos, clientes corporativos y producciones editoriales.</p>
          <a href="<?= BASE_URL ?>/proyectos" class="action-card-link">
            Explorar Trabajos <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <!-- Card 2: Contacto -->
      <div class="action-card">
        <div class="action-card-icon">
          <i class="fas fa-phone-volume"></i>
        </div>
        <div class="action-card-body">
          <h5>Contáctanos Directo</h5>
          <p>¿Tienes un evento próximo o requieres una sesión fotográfica? Conversemos de inmediato.</p>
          <a href="tel:<?= htmlspecialchars(get_setting('phone', '+56 9 4757 3794')) ?>" class="action-card-link">
            Llamar al <?= htmlspecialchars(get_setting('phone', '+56 9 4757 3794')) ?> <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <!-- Card 3: Cobertura -->
      <div class="action-card">
        <div class="action-card-icon">
          <i class="fas fa-globe-americas"></i>
        </div>
        <div class="action-card-body">
          <h5>Zona de Cobertura</h5>
          <p>Base en Santiago de Chile con disponibilidad para desplazamientos a cualquier región o destino internacional.</p>
          <a href="<?= BASE_URL ?>/contacto" class="action-card-link">
            Consultar Disponibilidad <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Section: Nuestra Fotografía (Featured Projects) -->
<section class="section" style="background: #0d0d12; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag">Enfoque y Excelencia</span>
      <h2 class="section-title">Nuestra Fotografía</h2>
      <p class="section-subtitle">
        Capturamos mucho más que momentos. Contamos historias a través de imágenes, preservando las emociones, los lugares y las experiencias que hacen que cada recuerdo sea inolvidable.
      </p>
    </div>

    <div class="projects-grid">
      <?php foreach ($featuredProjects as $proj): ?>
        <article class="project-card" data-category="<?= htmlspecialchars($proj['category']) ?>">
          <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($proj['slug']) ?>" class="project-thumb-wrap" style="display: block;">
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($proj['cover_image']) ?>" 
                 alt="<?= htmlspecialchars($proj['title']) ?>" 
                 class="project-thumb" 
                 loading="lazy">
            <span class="project-meta-pill"><?= htmlspecialchars($proj['category']) ?></span>
          </a>
          <div class="project-card-body">
            <div class="project-date"><i class="far fa-calendar-alt"></i> <?= htmlspecialchars($proj['event_date']) ?></div>
            <h3 class="project-card-title">
              <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($proj['slug']) ?>" style="color: inherit;">
                <?= htmlspecialchars($proj['title']) ?>
              </a>
            </h3>
            <p class="project-card-desc"><?= htmlspecialchars($proj['description']) ?></p>
            <div class="project-card-footer">
              <div class="project-client">Cliente: <span><?= htmlspecialchars($proj['client'] ?: 'Confidencial') ?></span></div>
              <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($proj['slug']) ?>" class="btn btn-outline btn-sm">
                Ver Proyecto <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Instagram Marquee Ribbon -->
<section class="marquee-section">
  <div class="marquee-header">
    <a href="<?= htmlspecialchars(get_setting('instagram', 'https://www.instagram.com/funktographer/')) ?>" target="_blank">
      <i class="fab fa-instagram"></i> Síguenos en @funktographer
    </a>
  </div>
  <div class="marquee-track">
    <?php 
    // Show a ribbon of photos
    $ribbon = array_slice($homeImages, 0, 16);
    // Duplicate for seamless infinite loop
    $loopRibbon = array_merge($ribbon, $ribbon);
    foreach ($loopRibbon as $r):
    ?>
      <div class="marquee-item">
        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($r['image_url']) ?>" alt="<?= htmlspecialchars($r['title']) ?>" loading="lazy">
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
