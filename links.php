<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Fetch active bio links
$stmtLinks = $pdo->query("SELECT * FROM bio_links WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
$bioLinks = $stmtLinks->fetchAll();

// Fetch recent projects (limit 4)
$stmtProjects = $pdo->query("SELECT * FROM projects ORDER BY is_featured DESC, id DESC LIMIT 4");
$recentProjects = $stmtProjects->fetchAll();

// Site settings
$sitePhone = get_setting('phone', '+56 9 4757 3794');
$siteWhatsApp = get_setting('whatsapp', '56947573794');
$siteEmail = get_setting('email', 'contacto@funktographer.cl');
$siteInstagram = get_setting('instagram', 'https://www.instagram.com/funktographer/');
$siteCoverage = get_setting('coverage', 'Santiago de Chile • Cobertura Nacional e Internacional');

$pageTitle = "Funktographer — Bio Links y Hub Oficial";
$pageDescription = "Enlaces oficiales, redes sociales, contacto directo y portafolio audiovisual de Funktographer en Santiago de Chile.";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
  <link rel="canonical" href="<?= BASE_URL ?>/links">

  <!-- Open Graph / Facebook / WhatsApp Debugger -->
  <meta property="og:site_name" content="Funktographer">
  <meta property="og:type" content="profile">
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta property="og:url" content="<?= BASE_URL ?>/links">
  <meta property="og:image" content="<?= BASE_URL ?>/uploads/home/Foto-evento-Algo-electrico-con-quimica-Funktographer-24.jpg">
  <meta property="og:image:secure_url" content="<?= BASE_URL ?>/uploads/home/Foto-evento-Algo-electrico-con-quimica-Funktographer-24.jpg">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:locale" content="es_CL">

  <!-- Twitter Cards -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta name="twitter:image" content="<?= BASE_URL ?>/uploads/home/Foto-evento-Algo-electrico-con-quimica-Funktographer-24.jpg">

  <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/favicon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/links.css">
</head>
<body class="bio-body">

  <!-- Ambient Dynamic Glow Canvas -->
  <div class="bio-ambient-canvas" aria-hidden="true">
    <div class="bio-glow-orb-1"></div>
    <div class="bio-glow-orb-2"></div>
    <div class="bio-glow-orb-3"></div>
  </div>

  <div class="bio-hub-wrapper">

    <!-- Top Bar Controls -->
    <header class="bio-top-bar">
      <a href="<?= BASE_URL ?>/" class="bio-logo-text" title="Ir al Sitio Web Oficial">
        <i class="fas fa-camera-retro" style="color: var(--bio-gold);"></i> FUNK<span>TOGRAPHER</span>
      </a>
      <button type="button" class="bio-action-btn" id="shareBtn" aria-label="Compartir perfil" title="Compartir">
        <i class="fas fa-share-nodes"></i>
      </button>
    </header>

    <!-- Profile Identity -->
    <div class="bio-profile-header">
      <div class="bio-avatar-container">
        <div class="bio-avatar-halo"></div>
        <div class="bio-avatar-img-wrap">
          <img src="<?= BASE_URL ?>/assets/img/logo-circular.png" alt="Funktographer Logo" class="bio-avatar-img">
        </div>
        <div class="bio-verified-badge" title="Cuenta Oficial Verificada">
          <i class="fas fa-check"></i>
        </div>
      </div>

      <h1 class="bio-profile-title">
        Funktographer
      </h1>
      <div class="bio-handle">@funktographer</div>
      <p class="bio-tagline">
        Fotografía &bull; Video &bull; Marketing Visual
      </p>

      <div class="bio-location-chip">
        <i class="fas fa-map-marker-alt"></i> Santiago de Chile &bull; Cobertura Global
      </div>
    </div>

    <!-- Quick Social Icons Bar -->
    <div class="bio-social-bar">
      <a href="https://wa.me/<?= urlencode($siteWhatsApp) ?>" target="_blank" rel="noopener noreferrer" class="bio-social-icon social-wa" aria-label="WhatsApp" title="WhatsApp Directo">
        <i class="fab fa-whatsapp"></i>
      </a>
      <a href="<?= htmlspecialchars($siteInstagram) ?>" target="_blank" rel="noopener noreferrer" class="bio-social-icon social-ig" aria-label="Instagram" title="Instagram Oficial">
        <i class="fab fa-instagram"></i>
      </a>
      <a href="https://www.youtube.com/@yeeeahlatam/videos" target="_blank" rel="noopener noreferrer" class="bio-social-icon social-yt" aria-label="YouTube" title="Canal YouTube">
        <i class="fab fa-youtube"></i>
      </a>
      <a href="https://www.behance.net/funktographer" target="_blank" rel="noopener noreferrer" class="bio-social-icon social-be" aria-label="Behance" title="Portafolio Behance">
        <i class="fab fa-behance"></i>
      </a>
      <a href="https://www.tiktok.com/@funktographer" target="_blank" rel="noopener noreferrer" class="bio-social-icon" aria-label="TikTok" title="TikTok Oficial">
        <i class="fab fa-tiktok"></i>
      </a>
      <a href="https://www.facebook.com/Funktographer" target="_blank" rel="noopener noreferrer" class="bio-social-icon" aria-label="Facebook" title="Facebook Oficial">
        <i class="fab fa-facebook-f"></i>
      </a>
    </div>

    <!-- Section Title: Links -->
    <div class="bio-section-title">
      <span>Accesos Destacados</span>
    </div>

    <!-- Innovative Cut-Corner Link Buttons List -->
    <nav class="bio-links-list" aria-label="Enlaces principales">
      <?php foreach ($bioLinks as $link): ?>
        <?php
          $targetUrl = $link['url'];
          $isExternal = str_starts_with($targetUrl, 'http://') || str_starts_with($targetUrl, 'https://');
          if (!$isExternal) {
              $targetUrl = rtrim(BASE_URL, '/') . '/' . ltrim($targetUrl, '/');
          }
        ?>
        <a href="<?= htmlspecialchars($targetUrl) ?>" 
           class="bio-btn-card style-<?= htmlspecialchars($link['style_type']) ?>" 
           target="<?= $isExternal ? '_blank' : '_self' ?>" 
           rel="<?= $isExternal ? 'noopener noreferrer' : '' ?>">
          
          <div class="bio-btn-icon-wrap">
            <i class="<?= htmlspecialchars($link['icon'] ?: 'fas fa-link') ?>"></i>
          </div>

          <div class="bio-btn-body">
            <div class="bio-btn-title-row">
              <span class="bio-btn-title"><?= htmlspecialchars($link['title']) ?></span>
              <?php if (!empty($link['badge'])): ?>
                <span class="bio-btn-badge"><?= htmlspecialchars($link['badge']) ?></span>
              <?php endif; ?>
            </div>
            <?php if (!empty($link['subtitle'])): ?>
              <span class="bio-btn-subtitle"><?= htmlspecialchars($link['subtitle']) ?></span>
            <?php endif; ?>
          </div>

          <div class="bio-btn-arrow">
            <i class="fas fa-chevron-right"></i>
          </div>
        </a>
      <?php endforeach; ?>
    </nav>

    <!-- Contact & Coverage Card (As Requested by User) -->
    <section class="bio-contact-card" aria-labelledby="contactCardTitle">
      <div class="bio-contact-card-header">
        <div class="bio-contact-card-title" id="contactCardTitle">
          <i class="fas fa-address-card"></i> Contacto y Cobertura
        </div>
        <div class="bio-beacon-wrap" title="Disponibilidad activa para eventos y proyectos">
          <span class="bio-beacon-dot"></span> Disponible
        </div>
      </div>

      <div class="bio-contact-grid">
        <!-- Phone -->
        <a href="tel:<?= htmlspecialchars($sitePhone) ?>" class="bio-contact-row" title="Llamar directamente">
          <div class="bio-contact-icon icon-phone">
            <i class="fas fa-phone-alt"></i>
          </div>
          <div class="bio-contact-info">
            <span class="bio-contact-label">Teléfono Directo</span>
            <span class="bio-contact-val"><?= htmlspecialchars($sitePhone) ?></span>
          </div>
          <span class="bio-contact-action">Llamar</span>
        </a>

        <!-- WhatsApp -->
        <a href="https://wa.me/<?= urlencode($siteWhatsApp) ?>?text=Hola,%20vi%20tu%20perfil%20en%20Funktographer%20y%20quisiera%20cotizar%20un%20servicio" target="_blank" rel="noopener noreferrer" class="bio-contact-row" title="Conversar por WhatsApp">
          <div class="bio-contact-icon icon-wa">
            <i class="fab fa-whatsapp"></i>
          </div>
          <div class="bio-contact-info">
            <span class="bio-contact-label">WhatsApp Corporativo</span>
            <span class="bio-contact-val">+<?= htmlspecialchars($siteWhatsApp) ?></span>
          </div>
          <span class="bio-contact-action" style="color: #25d366; border-color: rgba(37, 211, 102, 0.4);">Chatear</span>
        </a>

        <!-- Email -->
        <a href="mailto:<?= htmlspecialchars($siteEmail) ?>" class="bio-contact-row" title="Enviar correo">
          <div class="bio-contact-icon icon-email">
            <i class="fas fa-envelope"></i>
          </div>
          <div class="bio-contact-info">
            <span class="bio-contact-label">Correo Electrónico</span>
            <span class="bio-contact-val"><?= htmlspecialchars($siteEmail) ?></span>
          </div>
          <span class="bio-contact-action" style="color: #60a5fa; border-color: rgba(96, 165, 250, 0.4);">Escribir</span>
        </a>

        <!-- Base & Coverage -->
        <div class="bio-contact-row" style="cursor: default;">
          <div class="bio-contact-icon icon-loc">
            <i class="fas fa-globe-americas"></i>
          </div>
          <div class="bio-contact-info">
            <span class="bio-contact-label">Base y Cobertura</span>
            <span class="bio-contact-val" style="font-size: 0.88rem;"><?= htmlspecialchars($siteCoverage) ?></span>
          </div>
        </div>

        <!-- Instagram -->
        <a href="<?= htmlspecialchars($siteInstagram) ?>" target="_blank" rel="noopener noreferrer" class="bio-contact-row" title="Ver Instagram Oficial">
          <div class="bio-contact-icon icon-ig">
            <i class="fab fa-instagram"></i>
          </div>
          <div class="bio-contact-info">
            <span class="bio-contact-label">Instagram Oficial</span>
            <span class="bio-contact-val">@funktographer</span>
          </div>
          <span class="bio-contact-action" style="color: #e1306c; border-color: rgba(225, 48, 108, 0.4);">Seguir</span>
        </a>
      </div>
    </section>

    <!-- Dynamic Projects Feed -->
    <?php if (!empty($recentProjects)): ?>
      <section class="bio-projects-section" aria-labelledby="recentProjectsTitle">
        <div class="bio-projects-header">
          <div>
            <h3 id="recentProjectsTitle">
              <i class="fas fa-photo-film"></i> Últimas Producciones
            </h3>
            <p>Trabajos recientes subidos al portafolio</p>
          </div>
        </div>

        <div class="bio-projects-grid">
          <?php foreach ($recentProjects as $proj): ?>
            <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($proj['slug']) ?>" class="bio-project-card">
              <div class="bio-project-thumb-wrap">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($proj['cover_image']) ?>" 
                     alt="<?= htmlspecialchars($proj['title']) ?>" 
                     class="bio-project-thumb" 
                     loading="lazy">
                <span class="bio-project-pill"><?= htmlspecialchars($proj['category']) ?></span>
              </div>
              <div class="bio-project-body">
                <h4 class="bio-project-title"><?= htmlspecialchars($proj['title']) ?></h4>
                <div class="bio-project-meta">
                  <span>Ver Proyecto</span> <i class="fas fa-arrow-right"></i>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>

        <a href="<?= BASE_URL ?>/proyectos" class="bio-all-projects-btn">
          <span>Ver Catálogo Completo</span>
          <i class="fas fa-arrow-right"></i>
        </a>
      </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bio-footer">
      <p>&copy; <?= date('Y') ?> Funktographer Chile. Todos los derechos reservados.</p>
      <p>
        <a href="<?= BASE_URL ?>/">Visitar Sitio Web Oficial &rarr;</a>
      </p>
    </footer>

  </div>

  <!-- Toast Message -->
  <div class="bio-toast" id="bioToast" role="status" aria-live="polite">
    <i class="fas fa-check-circle"></i>
    <span id="bioToastText">¡Enlace copiado al portapapeles!</span>
  </div>

  <script>
    // Share / Copy Link Handler
    const shareBtn = document.getElementById('shareBtn');
    const toast = document.getElementById('bioToast');
    const toastText = document.getElementById('bioToastText');
    let toastTimeout = null;

    function showToast(msg) {
      if (toastTimeout) clearTimeout(toastTimeout);
      toastText.textContent = msg;
      toast.classList.add('show');
      toastTimeout = setTimeout(() => {
        toast.classList.remove('show');
      }, 3000);
    }

    if (shareBtn) {
      shareBtn.addEventListener('click', async () => {
        const shareData = {
          title: 'Funktographer — Bio Links',
          text: 'Conoce los enlaces y portafolio oficial de Funktographer:',
          url: window.location.href
        };

        if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
          try {
            await navigator.share(shareData);
          } catch (err) {
            if (err.name !== 'AbortError') {
              copyToClipboard();
            }
          }
        } else {
          copyToClipboard();
        }
      });
    }

    function copyToClipboard() {
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(window.location.href).then(() => {
          showToast('¡Enlace copiado al portapapeles!');
        }).catch(() => {
          fallbackCopy();
        });
      } else {
        fallbackCopy();
      }
    }

    function fallbackCopy() {
      const tempInput = document.createElement('input');
      tempInput.value = window.location.href;
      document.body.appendChild(tempInput);
      tempInput.select();
      try {
        document.execCommand('copy');
        showToast('¡Enlace copiado al portapapeles!');
      } catch (e) {
        showToast('Copia la URL de tu navegador.');
      }
      document.body.removeChild(tempInput);
    }
  </script>
</body>
</html>
