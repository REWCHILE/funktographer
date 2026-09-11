<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');
$id = intval($_GET['id'] ?? 0);

if (!empty($slug)) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name 
        FROM blog_posts p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.slug = ? LIMIT 1
    ");
    $stmt->execute([$slug]);
    $post = $stmt->fetch();
} elseif ($id > 0) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name 
        FROM blog_posts p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ? LIMIT 1
    ");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
} else {
    $post = null;
}

if (!$post) {
    header("Location: " . BASE_URL . "/blog");
    exit;
}

// Check if draft and not admin
if ($post['status'] === 'draft' && !is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/blog");
    exit;
}

// Increment views counter (only once per session per post)
$sessionKey = 'viewed_post_' . $post['id'];
if (empty($_SESSION[$sessionKey])) {
    $pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);
    $_SESSION[$sessionKey] = true;
    $post['views']++;
}

// Fetch 3 Related Articles
$stmtRelated = $pdo->prepare("
    SELECT * FROM blog_posts 
    WHERE id != ? AND status = 'published' 
    ORDER BY (category = ?) DESC, published_at DESC 
    LIMIT 3
");
$stmtRelated->execute([$post['id'], $post['category']]);
$relatedPosts = $stmtRelated->fetchAll();

// SEO Meta
$pageTitle = !empty($post['meta_title']) ? $post['meta_title'] : $post['title'];
$pageDescription = !empty($post['meta_description']) ? $post['meta_description'] : (!empty($post['excerpt']) ? $post['excerpt'] : substr(strip_tags($post['content']), 0, 160));
$pageImage = $post['cover_image'];
$ogType = 'article';
$pageCanonical = BASE_URL . '/blog/' . urlencode($post['slug']);

include __DIR__ . '/includes/header.php';

$currentUrl = BASE_URL . '/blog/' . urlencode($post['slug']);
$shareTitle = urlencode($post['title'] . ' — Funktographer');
?>

<!-- Reading Progress Bar -->
<div id="readingProgressBar" style="position: fixed; top: 0; left: 0; height: 4px; width: 0%; background: linear-gradient(90deg, var(--primary) 0%, #ffdf6d 100%); z-index: 99999; transition: width 0.1s ease-out; box-shadow: 0 0 10px rgba(255, 197, 1, 0.5);"></div>

<!-- Article Header Section -->
<section class="section" style="padding-top: 140px; padding-bottom: 30px; position: relative; z-index: 5;">
  <div class="container" style="max-width: 920px;">
    
    <!-- Top Nav / Breadcrumbs -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; position: relative; z-index: 10;">
      <a href="<?= BASE_URL ?>/blog" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 8px; position: relative; z-index: 20; cursor: pointer;">
        <i class="fas fa-arrow-left"></i> Volver al Blog
      </a>
      <div style="font-size: 0.85rem; color: var(--text-dim);">
        <a href="<?= BASE_URL ?>/" style="color: inherit;">Inicio</a> / 
        <a href="<?= BASE_URL ?>/blog" style="color: inherit;">Blog</a> / 
        <span style="color: var(--primary);"><?= htmlspecialchars($post['category_name'] ?: $post['category']) ?></span>
      </div>
    </div>

    <!-- Category & Meta Tags -->
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px; flex-wrap: wrap;">
      <span class="blog-category-tag">
        <?= htmlspecialchars($post['category_name'] ?: $post['category']) ?>
      </span>
      <?php if ($post['is_featured']): ?>
        <span style="background: rgba(255, 197, 1, 0.2); color: #FFC501; font-size: 0.8rem; font-weight: 700; padding: 4px 12px; border-radius: 20px;">
          <i class="fas fa-star"></i> Destacado
        </span>
      <?php endif; ?>
      <?php if ($post['status'] === 'draft'): ?>
        <span style="background: rgba(239, 68, 68, 0.2); color: #f87171; font-size: 0.8rem; font-weight: 700; padding: 4px 12px; border-radius: 20px;">
          <i class="fas fa-eye-slash"></i> Borrador (Solo Admin)
        </span>
      <?php endif; ?>
    </div>

    <!-- Main Title -->
    <h1 style="font-size: clamp(2rem, 4vw, 3.2rem); font-family: var(--font-title); color: #fff; line-height: 1.2; margin-bottom: 20px; font-weight: normal; letter-spacing: 0.5px;">
      <?= htmlspecialchars($post['title']) ?>
    </h1>

    <!-- Excerpt / Subtitle -->
    <?php if (!empty($post['excerpt'])): ?>
      <p style="font-size: 1.2rem; color: #d1d5db; line-height: 1.6; margin-bottom: 24px; font-weight: 400;">
        <?= htmlspecialchars($post['excerpt']) ?>
      </p>
    <?php endif; ?>

    <!-- Author & Reading Stats Bar -->
    <div class="blog-meta-bar" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); padding: 16px 0; margin-bottom: 34px; flex-wrap: wrap; gap: 16px;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <img src="<?= BASE_URL ?>/assets/img/logo-circular.png" alt="Manuel" style="width: 46px; height: 46px; border-radius: 50%; border: 2px solid var(--primary); object-fit: contain; background: #0e0e15;">
        <div>
          <div style="font-weight: 700; color: #fff; font-size: 0.95rem;"><?= htmlspecialchars($post['author']) ?></div>
          <div style="font-size: 0.8rem; color: var(--text-dim);">Fotógrafo y Realizador Audiovisual</div>
        </div>
      </div>

      <div style="display: flex; align-items: center; gap: 16px; color: var(--text-dim); font-size: 0.85rem;">
        <span><i class="far fa-calendar-alt" style="color: var(--primary); margin-right: 6px;"></i> <?= date('d M, Y', strtotime($post['published_at'])) ?></span>
        <span>&bull;</span>
        <span><i class="far fa-clock" style="color: var(--primary); margin-right: 6px;"></i> <?= $post['reading_time'] ?> min de lectura</span>
        <span>&bull;</span>
        <span><i class="far fa-eye" style="color: var(--primary); margin-right: 6px;"></i> <?= number_format($post['views']) ?> vistas</span>
      </div>
    </div>

    <!-- Article Cover Image -->
    <?php if (!empty($post['cover_image'])): ?>
      <div class="blog-hero-image-wrap" style="position: relative; border-radius: 14px; overflow: hidden; margin-bottom: 40px; box-shadow: 0 15px 40px rgba(0,0,0,0.6); border: 1px solid rgba(255, 197, 1, 0.15);">
        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($post['cover_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" style="width: 100%; max-height: 520px; object-fit: cover; display: block;">
      </div>
    <?php endif; ?>

  </div>
</section>

<!-- Article Body Section -->
<article class="section" style="padding-top: 0; padding-bottom: 60px;">
  <div class="container" style="max-width: 820px;">
    
    <!-- Rendered Content with Typography Styles -->
    <div class="blog-post-content" id="blogPostContent">
      <?= $post['content'] ?>
    </div>

    <!-- Social Share Buttons -->
    <div class="blog-share-box" style="margin-top: 50px; padding: 24px; background: rgba(18, 18, 26, 0.7); border: 1px solid var(--border-color); border-radius: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
      <div style="font-weight: 700; color: #fff; font-size: 0.95rem;">
        <i class="fas fa-share-alt" style="color: var(--primary); margin-right: 8px;"></i> ¿Te pareció útil? Comparte este artículo:
      </div>
      <div style="display: flex; gap: 10px; align-items: center;">
        <!-- WhatsApp -->
        <a href="https://api.whatsapp.com/send?text=<?= $shareTitle ?>%20<?= urlencode($currentUrl) ?>" target="_blank" class="share-btn share-wa" title="Compartir en WhatsApp">
          <i class="fab fa-whatsapp"></i>
        </a>
        <!-- LinkedIn -->
        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($currentUrl) ?>" target="_blank" class="share-btn share-li" title="Compartir en LinkedIn">
          <i class="fab fa-linkedin-in"></i>
        </a>
        <!-- X / Twitter -->
        <a href="https://twitter.com/intent/tweet?text=<?= $shareTitle ?>&url=<?= urlencode($currentUrl) ?>" target="_blank" class="share-btn share-tw" title="Compartir en X">
          <i class="fab fa-x-twitter"></i>
        </a>
        <!-- Copy Link -->
        <button type="button" class="share-btn share-copy" id="btnCopyPostLink" title="Copiar Enlace">
          <i class="fas fa-link"></i>
        </button>
      </div>
    </div>

    <!-- Author Bio & Direct WhatsApp Conversion Box -->
    <div class="blog-author-card" style="margin-top: 40px; padding: 30px; background: linear-gradient(135deg, rgba(121, 56, 226, 0.12) 0%, rgba(255, 197, 1, 0.08) 100%); border: 1px solid rgba(255, 197, 1, 0.3); border-radius: 14px; display: flex; gap: 24px; align-items: center; flex-wrap: wrap;">
      <img src="<?= BASE_URL ?>/assets/img/logo-circular.png" alt="Manuel" style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid var(--primary); object-fit: contain; background: #0f0f18; flex-shrink: 0;">
      <div style="flex: 1; min-width: 260px;">
        <span class="section-tag" style="margin-bottom: 4px; font-size: 0.75rem;">Detrás del Lente</span>
        <h4 style="color: #fff; font-size: 1.25rem; margin-bottom: 6px;"><?= htmlspecialchars($post['author']) ?></h4>
        <p style="color: var(--text-dim); font-size: 0.9rem; line-height: 1.5; margin-bottom: 14px;">
          Fotógrafo y creador visual en Santiago de Chile. Especialista en eventos corporativos, estilismo gastronómico y retratos con identidad de marca.
        </p>
        <a href="https://wa.me/56947573794?text=Hola%20Manuel,%20leí%20tu%20artículo%20'<?= urlencode($post['title']) ?>'%20y%20me%20gustaría%20cotizar" target="_blank" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 8px;">
          <i class="fab fa-whatsapp"></i> Conversar con Manuel
        </a>
      </div>
    </div>

  </div>
</article>

<!-- Related Articles Section -->
<?php if (!empty($relatedPosts)): ?>
  <section class="section" style="background: rgba(18, 8, 38, 0.6); border-top: 1px solid var(--border-color); padding: 60px 0;">
    <div class="container">
      <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 36px; flex-wrap: wrap; gap: 16px;">
        <div>
          <span class="section-tag">Continúa Leyendo</span>
          <h2 style="font-size: 2.2rem; font-family: var(--font-title); font-weight: normal; color: #fff; margin-bottom: 0; letter-spacing: 0.5px;">
            Artículos Relacionados
          </h2>
        </div>
        <a href="<?= BASE_URL ?>/blog" class="btn btn-outline btn-sm">
          Ver Todo el Blog <i class="fas fa-arrow-right"></i>
        </a>
      </div>

      <div class="blog-grid">
        <?php foreach ($relatedPosts as $rel): ?>
          <article class="blog-card">
            <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($rel['slug']) ?>" class="blog-card-thumb-wrap">
              <img src="<?= BASE_URL ?>/<?= htmlspecialchars($rel['cover_image']) ?>" alt="<?= htmlspecialchars($rel['title']) ?>" class="blog-card-thumb" loading="lazy">
              <span class="blog-card-category"><?= htmlspecialchars($rel['category']) ?></span>
            </a>
            <div class="blog-card-body">
              <div class="blog-post-meta" style="margin-bottom: 10px;">
                <span><i class="far fa-calendar-alt"></i> <?= date('d M, Y', strtotime($rel['published_at'])) ?></span>
                <span class="meta-sep">&bull;</span>
                <span><i class="far fa-clock"></i> <?= $rel['reading_time'] ?> min</span>
              </div>
              <h3 class="blog-card-title">
                <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($rel['slug']) ?>">
                  <?= htmlspecialchars($rel['title']) ?>
                </a>
              </h3>
              <p class="blog-card-excerpt">
                <?= htmlspecialchars($rel['excerpt'] ?: substr(strip_tags($rel['content']), 0, 110) . '...') ?>
              </p>
              <div class="blog-card-footer">
                <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($rel['slug']) ?>" class="blog-read-link">
                  Leer artículo <i class="fas fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Reading Progress Bar
  const progressBar = document.getElementById('readingProgressBar');
  window.addEventListener('scroll', () => {
    const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    if (progressBar) progressBar.style.width = scrolled + '%';
  });

  // Copy Link Button
  const btnCopy = document.getElementById('btnCopyPostLink');
  if (btnCopy) {
    btnCopy.addEventListener('click', () => {
      navigator.clipboard.writeText(window.location.href).then(() => {
        btnCopy.innerHTML = '<i class="fas fa-check" style="color: #34d399;"></i>';
        setTimeout(() => {
          btnCopy.innerHTML = '<i class="fas fa-link"></i>';
        }, 2000);
      });
    });
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
