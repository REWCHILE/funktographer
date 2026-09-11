<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$currentScript = basename($_SERVER['SCRIPT_NAME']);
$siteTitle = get_setting('site_name', 'Funktographer');
$siteTagline = get_setting('site_tagline', 'Fotografía y Video Profesional en Santiago de Chile');
$phone = get_setting('phone', '+56 9 4757 3794');
$whatsapp = get_setting('whatsapp', '56947573794');
$email = get_setting('email', 'contacto@funktographer.cl');
$instagram = get_setting('instagram', 'https://www.instagram.com/funktographer/');

$pageTitle = isset($pageTitle) ? $pageTitle . " — " . $siteTitle : $siteTitle . " — " . $siteTagline;
$pageDescription = isset($pageDescription) ? $pageDescription : $siteTagline;

// Canonical and Open Graph meta tags for Facebook Debugger / WhatsApp / Twitter
$defaultOgImage = BASE_URL . '/uploads/home/Foto-evento-Algo-electrico-con-quimica-Funktographer-24.jpg';
if (!empty($pageImage)) {
    if (str_starts_with($pageImage, 'http://') || str_starts_with($pageImage, 'https://')) {
        $finalOgImage = $pageImage;
    } else {
        $finalOgImage = rtrim(BASE_URL, '/') . '/' . ltrim($pageImage, '/');
    }
} else {
    $finalOgImage = $defaultOgImage;
}

if (!empty($pageCanonical)) {
    $canonicalUrl = $pageCanonical;
} else {
    $reqUri = $_SERVER['REQUEST_URI'] ?? '';
    $cleanUri = preg_replace('/[?].*$/', '', $reqUri);
    $canonicalUrl = rtrim(BASE_URL, '/') . '/' . ltrim($cleanUri, '/');
}
$ogType = $ogType ?? 'website';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
  <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

  <!-- Open Graph / Facebook / WhatsApp Debugger Meta Tags -->
  <meta property="og:site_name" content="<?= htmlspecialchars($siteTitle) ?>">
  <meta property="og:type" content="<?= htmlspecialchars($ogType) ?>">
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
  <meta property="og:image" content="<?= htmlspecialchars($finalOgImage) ?>">
  <meta property="og:image:secure_url" content="<?= htmlspecialchars($finalOgImage) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta property="og:locale" content="es_CL">

  <!-- Twitter Cards -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta name="twitter:image" content="<?= htmlspecialchars($finalOgImage) ?>">
  <meta name="twitter:image:alt" content="<?= htmlspecialchars($pageTitle) ?>">
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/favicon.png">
  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/assets/img/favicon.png">

  <!-- Icons (FontAwesome or Bootstrap icons lightweight CDN) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>
<body>

  <!-- Site Header -->
  <header class="site-header">
    <div class="container">
      <nav class="navbar">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>/" class="brand-logo" title="<?= htmlspecialchars($siteTitle) ?>">
          <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="<?= htmlspecialchars($siteTitle) ?>">
        </a>

        <!-- Desktop Menu -->
        <ul class="nav-menu">
          <li>
            <a href="<?= BASE_URL ?>/" class="nav-link <?= (in_array($currentScript, ['index.php', 'index', ''])) ? 'active' : '' ?>">Inicio</a>
          </li>
          <li>
            <a href="<?= BASE_URL ?>/proyectos" class="nav-link <?= (in_array($currentScript, ['proyectos.php', 'proyectos', 'proyecto.php'])) ? 'active' : '' ?>">Proyectos</a>
          </li>
          <li>
            <a href="<?= BASE_URL ?>/blog" class="nav-link <?= (in_array($currentScript, ['blog.php', 'blog', 'blog-post.php'])) ? 'active' : '' ?>">Blog</a>
          </li>
          <li>
            <a href="<?= BASE_URL ?>/sobre-mi" class="nav-link <?= (in_array($currentScript, ['sobre-mi.php', 'sobre-mi'])) ? 'active' : '' ?>">Sobre Mí</a>
          </li>
          <li>
            <a href="<?= BASE_URL ?>/contacto" class="nav-link <?= (in_array($currentScript, ['contacto.php', 'contacto'])) ? 'active' : '' ?>">Contacto</a>
          </li>
        </ul>

        <!-- Action buttons -->
        <div class="nav-actions">
          <a href="https://wa.me/<?= urlencode($whatsapp) ?>?text=Hola%20Funktographer,%20me%20gustaria%20cotizar%20un%20servicio" target="_blank" class="btn btn-primary btn-sm btn-header-cta">
            <i class="fab fa-whatsapp"></i> Cotizar
          </a>
          <button type="button" class="btn-icon" id="drawerOpenBtn" title="Menú">
            <i class="fas fa-bars"></i>
          </button>
        </div>
      </nav>
    </div>
  </header>

  <!-- Offcanvas Drawer -->
  <div class="drawer-overlay" id="drawerOverlay">
    <aside class="drawer-panel">
      <div class="drawer-header">
        <a href="<?= BASE_URL ?>/" class="brand-logo" title="<?= htmlspecialchars($siteTitle) ?>">
          <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="<?= htmlspecialchars($siteTitle) ?>">
        </a>
        <button type="button" class="drawer-close" id="drawerCloseBtn">
          Cerrar <i class="fas fa-times"></i>
        </button>
      </div>

      <div style="margin-bottom: 24px;">
        <a href="https://wa.me/<?= urlencode($whatsapp) ?>?text=Hola%20Funktographer,%20me%20gustaria%20cotizar%20un%20servicio" target="_blank" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px; font-size: 0.95rem;">
          <i class="fab fa-whatsapp" style="font-size: 1.15rem;"></i> Cotizar por WhatsApp
        </a>
      </div>

      <ul class="drawer-menu">
        <li><a href="<?= BASE_URL ?>/">Inicio</a></li>
        <li><a href="<?= BASE_URL ?>/proyectos">Proyectos</a></li>
        <li><a href="<?= BASE_URL ?>/blog">Blog</a></li>
        <li><a href="<?= BASE_URL ?>/sobre-mi">Sobre Mí</a></li>
        <li><a href="<?= BASE_URL ?>/contacto">Contacto</a></li>
        <li><a href="<?= BASE_URL ?>/admin/login" style="color: var(--primary); font-size: 1.1rem;"><i class="fas fa-lock"></i> Acceso Admin</a></li>
      </ul>

      <div class="drawer-contact-info">
        <div class="drawer-info-item">
          <i class="fas fa-phone-alt"></i>
          <a href="tel:<?= htmlspecialchars($phone) ?>"><?= htmlspecialchars($phone) ?></a>
        </div>
        <div class="drawer-info-item">
          <i class="fas fa-envelope"></i>
          <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a>
        </div>
        <div class="drawer-info-item">
          <i class="fas fa-map-marker-alt"></i>
          <span>Santiago de Chile</span>
        </div>

        <div class="drawer-socials">
          <a href="<?= htmlspecialchars($instagram) ?>" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="https://wa.me/<?= urlencode($whatsapp) ?>" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>
    </aside>
  </div>

  <!-- Lightbox Modal for Images -->
  <div class="lightbox-modal" id="lightboxModal">
    <button class="lightbox-btn lightbox-close" id="lightboxClose"><i class="fas fa-times"></i></button>
    <button class="lightbox-btn lightbox-prev" id="lightboxPrev"><i class="fas fa-chevron-left"></i></button>
    <button class="lightbox-btn lightbox-next" id="lightboxNext"><i class="fas fa-chevron-right"></i></button>
    <div class="lightbox-content">
      <img src="" alt="" id="lightboxImg">
      <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>
  </div>
