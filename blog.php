<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Blog & Casos de Estudio";
$pageDescription = "Consejos sobre fotografía corporativa, gastronomía, retratos profesionales y producción audiovisual en Santiago de Chile por Funktographer.";

// Fetch all published posts
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name, c.slug as category_slug
    FROM blog_posts p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'published'
    ORDER BY p.is_featured DESC, p.published_at DESC, p.id DESC
");
$stmt->execute();
$allPosts = $stmt->fetchAll();

// Extract featured post if any
$featuredPost = null;
$regularPosts = [];
foreach ($allPosts as $p) {
    if ($p['is_featured'] && $featuredPost === null) {
        $featuredPost = $p;
    } else {
        $regularPosts[] = $p;
    }
}
// If no featured post was marked, use the first post as featured if more than 2 posts exist
if (!$featuredPost && count($allPosts) > 0) {
    $featuredPost = array_shift($allPosts);
    $regularPosts = $allPosts;
}

// Fetch dynamic categories ordered by display_order
$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order ASC, id ASC")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- Blog Page Header -->
<section class="hero-section hero-page-header">
  <div class="hero-video-bg">
    <video autoplay muted loop playsinline poster="<?= BASE_URL ?>/assets/video/hero-poster.jpg">
      <source src="<?= BASE_URL ?>/assets/video/hero-bg.mp4" type="video/mp4">
    </video>
    <div class="hero-video-overlay"></div>
  </div>
  <div class="hero-bg-glow"></div>
  <div class="container hero-content">
    <span class="section-tag">Revista &amp; Casos de Estudio</span>
    <h1 class="hero-title">Blog Funktographer</h1>
    <p class="hero-description">
      Estrategias visuales, consejos de iluminación para eventos corporativos, estilismo gastronómico y detrás de cámaras en Santiago de Chile.
    </p>

    <!-- Search Box -->
    <div class="blog-search-bar" style="max-width: 540px; margin: 24px auto 30px auto; position: relative;">
      <input type="text" id="blogSearchInput" class="form-control" placeholder="Buscar por tema, evento, gastronomía..." style="background: rgba(18, 18, 26, 0.85); backdrop-filter: blur(10px); border: 1px solid rgba(255, 197, 1, 0.3); border-radius: 40px; padding: 14px 24px 14px 48px; color: #fff; font-size: 0.95rem; width: 100%; outline: none; box-shadow: 0 8px 30px rgba(0,0,0,0.5);">
      <i class="fas fa-search" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: var(--primary); font-size: 1rem;"></i>
    </div>

    <!-- Category Filter Pills -->
    <div class="filter-container">
      <button type="button" class="filter-btn active" data-filter="all">Todos los Artículos</button>
      <?php foreach ($categories as $cat): ?>
        <button type="button" class="filter-btn" data-filter="<?= htmlspecialchars($cat['name']) ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Main Blog Section -->
<section class="section" style="padding-top: 20px;">
  <div class="container">

    <!-- Featured Post Hero Showcase -->
    <?php if ($featuredPost): ?>
      <div class="blog-featured-card" data-category="<?= htmlspecialchars($featuredPost['category_name'] ?: $featuredPost['category']) ?>">
        <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($featuredPost['slug']) ?>" class="featured-thumb-wrap">
          <img src="<?= BASE_URL ?>/<?= htmlspecialchars($featuredPost['cover_image']) ?>" alt="<?= htmlspecialchars($featuredPost['title']) ?>" class="featured-thumb">
          <span class="featured-badge"><i class="fas fa-star"></i> Destacado</span>
        </a>
        <div class="featured-body">
          <div class="blog-post-meta">
            <span class="blog-category-tag"><?= htmlspecialchars($featuredPost['category_name'] ?: $featuredPost['category']) ?></span>
            <span class="meta-sep">&bull;</span>
            <span><i class="far fa-calendar-alt"></i> <?= date('d M, Y', strtotime($featuredPost['published_at'])) ?></span>
            <span class="meta-sep">&bull;</span>
            <span><i class="far fa-clock"></i> <?= $featuredPost['reading_time'] ?> min de lectura</span>
          </div>

          <h2 class="featured-title">
            <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($featuredPost['slug']) ?>">
              <?= htmlspecialchars($featuredPost['title']) ?>
            </a>
          </h2>

          <p class="featured-excerpt">
            <?= htmlspecialchars($featuredPost['excerpt'] ?: substr(strip_tags($featuredPost['content']), 0, 200) . '...') ?>
          </p>

          <div class="featured-footer">
            <div class="blog-author-mini">
              <img src="<?= BASE_URL ?>/assets/img/logo-circular.png" alt="Manuel" class="author-avatar-sm">
              <span class="author-name"><?= htmlspecialchars($featuredPost['author']) ?></span>
            </div>
            <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($featuredPost['slug']) ?>" class="btn btn-primary btn-sm">
              Leer Artículo <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Regular Posts Grid -->
    <div class="blog-grid" id="blogGrid">
      <?php foreach ($regularPosts as $post): ?>
        <article class="blog-card" data-category="<?= htmlspecialchars($post['category_name'] ?: $post['category']) ?>" data-title="<?= htmlspecialchars(strtolower($post['title'])) ?>" data-excerpt="<?= htmlspecialchars(strtolower($post['excerpt'] ?? '')) ?>">
          <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" class="blog-card-thumb-wrap">
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($post['cover_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="blog-card-thumb" loading="lazy">
            <span class="blog-card-category"><?= htmlspecialchars($post['category_name'] ?: $post['category']) ?></span>
          </a>

          <div class="blog-card-body">
            <div class="blog-post-meta" style="margin-bottom: 12px;">
              <span><i class="far fa-calendar-alt"></i> <?= date('d M, Y', strtotime($post['published_at'])) ?></span>
              <span class="meta-sep">&bull;</span>
              <span><i class="far fa-clock"></i> <?= $post['reading_time'] ?> min</span>
            </div>

            <h3 class="blog-card-title">
              <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>">
                <?= htmlspecialchars($post['title']) ?>
              </a>
            </h3>

            <p class="blog-card-excerpt">
              <?= htmlspecialchars($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 130) . '...') ?>
            </p>

            <div class="blog-card-footer">
              <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" class="blog-read-link">
                Leer más <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <!-- Empty State for filters/search -->
    <div id="blogEmptyState" style="display: none; text-align: center; padding: 70px 20px; color: var(--text-dim);">
      <i class="fas fa-search" style="font-size: 3rem; color: var(--primary); opacity: 0.4; margin-bottom: 16px;"></i>
      <h3 style="color: #fff; margin-bottom: 8px;">No se encontraron artículos</h3>
      <p>Prueba con otros términos de búsqueda o selecciona otra categoría.</p>
      <button type="button" class="btn btn-outline btn-sm" id="btnResetFilters" style="margin-top: 16px;">
        Ver todos los artículos
      </button>
    </div>

  </div>
</section>

<!-- Bottom CTA Section -->
<section class="section" style="background: linear-gradient(180deg, transparent 0%, rgba(121, 56, 226, 0.08) 50%, transparent 100%); padding: 70px 0;">
  <div class="container text-center" style="max-width: 760px;">
    <span class="section-tag">Conversemos</span>
    <h2 style="font-size: 2.2rem; font-family: var(--font-heading); color: #fff; margin-bottom: 16px;">
      ¿Planeas una producción o cobertura en tu empresa?
    </h2>
    <p style="color: var(--text-dim); font-size: 1.05rem; line-height: 1.7; margin-bottom: 30px;">
      Cada marca y evento tiene una historia que merece ser contada con precisión cinematográfica. Agenda una llamada o cotiza directamente con Manuel por WhatsApp.
    </p>
    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
      <a href="https://wa.me/56947573794?text=Hola%20Manuel,%20estuve%20leyendo%20su%20blog%20y%20me%20gustaría%20cotizar%20un%20servicio" target="_blank" class="btn btn-primary">
        <i class="fab fa-whatsapp"></i> Conversar por WhatsApp
      </a>
      <a href="<?= BASE_URL ?>/proyectos" class="btn btn-outline">
        <i class="fas fa-folder-open"></i> Ver Portafolio
      </a>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('blogSearchInput');
  const filterBtns = document.querySelectorAll('.filter-btn');
  const blogCards = document.querySelectorAll('.blog-card');
  const featuredCard = document.querySelector('.blog-featured-card');
  const emptyState = document.getElementById('blogEmptyState');
  const btnReset = document.getElementById('btnResetFilters');

  let activeCategory = 'all';
  let searchTerm = '';

  function filterPosts() {
    let visibleCount = 0;

    blogCards.forEach(card => {
      const cardCat = card.getAttribute('data-category') || '';
      const cardTitle = card.getAttribute('data-title') || '';
      const cardExcerpt = card.getAttribute('data-excerpt') || '';

      const matchCat = (activeCategory === 'all') || (cardCat.toLowerCase() === activeCategory.toLowerCase());
      const matchSearch = (searchTerm === '') || (cardTitle.includes(searchTerm) || cardExcerpt.includes(searchTerm));

      if (matchCat && matchSearch) {
        card.style.display = 'flex';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    // Also toggle featured card if search is active or filter is specific
    if (featuredCard) {
      const fCat = featuredCard.getAttribute('data-category') || '';
      const fTitle = (featuredCard.querySelector('.featured-title')?.textContent || '').toLowerCase();
      const matchCat = (activeCategory === 'all') || (fCat.toLowerCase() === activeCategory.toLowerCase());
      const matchSearch = (searchTerm === '') || fTitle.includes(searchTerm);

      if (matchCat && matchSearch) {
        featuredCard.style.display = 'grid';
        visibleCount++;
      } else {
        featuredCard.style.display = 'none';
      }
    }

    if (emptyState) {
      emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
  }

  // Category filter clicks
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeCategory = btn.getAttribute('data-filter') || 'all';
      filterPosts();
    });
  });

  // Search input typing
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      searchTerm = searchInput.value.trim().toLowerCase();
      filterPosts();
    });
  }

  // Reset button
  if (btnReset) {
    btnReset.addEventListener('click', () => {
      if (searchInput) searchInput.value = '';
      searchTerm = '';
      activeCategory = 'all';
      filterBtns.forEach(b => b.classList.remove('active'));
      const allBtn = document.querySelector('.filter-btn[data-filter="all"]');
      if (allBtn) allBtn.classList.add('active');
      filterPosts();
    });
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
