<?php
require_once __DIR__ . '/auth.php';

$currentAdminScript = basename($_SERVER['SCRIPT_NAME']);

// Count unread contact messages
$unreadStmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
$unreadCount = $unreadStmt->fetchColumn();

// Count home images
$totalHomeImgs = $pdo->query("SELECT COUNT(*) FROM home_images")->fetchColumn();

// Count projects
$totalProjects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();

// Count bio links
$totalBioLinks = $pdo->query("SELECT COUNT(*) FROM bio_links")->fetchColumn();

// Count categories
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

// Count blog posts
$totalPosts = $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($adminTitle ?? 'Administración') ?> — Funktographer</title>
  <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body>

<div class="adm-layout">
  <!-- Sidebar -->
  <aside class="adm-sidebar">
    <div class="adm-brand" style="justify-content: center;">
      <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Funktographer" style="height: 52px; width: auto;">
    </div>

    <ul class="adm-nav">
      <li>
        <a href="<?= BASE_URL ?>/admin/index" class="<?= (in_array($currentAdminScript, ['index.php', 'index', ''])) ? 'active' : '' ?>">
          <i class="fas fa-chart-line"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/bio-links" class="<?= (in_array($currentAdminScript, ['bio-links.php', 'bio-links'])) ? 'active' : '' ?>">
          <i class="fas fa-link"></i> Bio Links (Hub)
          <span class="adm-badge" style="background: rgba(255,255,255,0.1);"><?= $totalBioLinks ?></span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/home-images" class="<?= (in_array($currentAdminScript, ['home-images.php', 'home-images'])) ? 'active' : '' ?>">
          <i class="fas fa-images"></i> Fotos del Home
          <span class="adm-badge" style="background: rgba(255,255,255,0.1);"><?= $totalHomeImgs ?></span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/projects" class="<?= (in_array($currentAdminScript, ['projects.php', 'projects', 'project-form.php', 'project-form'])) ? 'active' : '' ?>">
          <i class="fas fa-folder-open"></i> Proyectos
          <span class="adm-badge" style="background: rgba(255,255,255,0.1);"><?= $totalProjects ?></span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/categories" class="<?= (in_array($currentAdminScript, ['categories.php', 'categories'])) ? 'active' : '' ?>">
          <i class="fas fa-tags"></i> Categorías (Pills)
          <span class="adm-badge" style="background: rgba(255,255,255,0.1);"><?= $totalCategories ?></span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/posts" class="<?= (in_array($currentAdminScript, ['posts.php', 'posts', 'post-form.php', 'post-form'])) ? 'active' : '' ?>">
          <i class="fas fa-newspaper"></i> Blog / Artículos
          <span class="adm-badge" style="background: rgba(255,255,255,0.1);"><?= $totalPosts ?></span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/messages" class="<?= (in_array($currentAdminScript, ['messages.php', 'messages'])) ? 'active' : '' ?>">
          <i class="fas fa-envelope"></i> Mensajes
          <?php if ($unreadCount > 0): ?>
            <span class="adm-badge" style="background: var(--adm-primary);"><?= $unreadCount ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/settings" class="<?= (in_array($currentAdminScript, ['settings.php', 'settings'])) ? 'active' : '' ?>">
          <i class="fas fa-cog"></i> Ajustes
        </a>
      </li>
      <li style="margin-top: 16px; border-top: 1px solid var(--adm-border); padding-top: 12px;">
        <a href="<?= BASE_URL ?>/links" target="_blank" style="color: var(--adm-primary);">
          <i class="fas fa-mobile-alt"></i> Ver /links en vivo
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/" target="_blank">
          <i class="fas fa-external-link-alt"></i> Ver Sitio Web
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/logout" style="color: #ef4444;" data-confirm="¿Deseas cerrar sesión?">
          <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
      </li>
    </ul>

    <div class="adm-user-box">
      <div class="adm-user-info">
        <div class="adm-avatar">
          <?= strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)) ?>
        </div>
        <div>
          <div style="font-weight: 700; font-size: 0.9rem;"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></div>
          <div style="font-size: 0.75rem; color: var(--adm-muted);">Administrador</div>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main Content Body -->
  <main class="adm-main">
    <?php if ($flash): ?>
      <div style="background: <?= ($flash['type'] === 'success') ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; border: 1px solid <?= ($flash['type'] === 'success') ? 'rgba(16, 185, 129, 0.4)' : 'rgba(239, 68, 68, 0.4)' ?>; color: <?= ($flash['type'] === 'success') ? '#34d399' : '#f87171' ?>; padding: 14px 20px; border-radius: 10px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-weight: 500;">
        <i class="fas <?= ($flash['type'] === 'success') ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
        <span><?= htmlspecialchars($flash['message']) ?></span>
      </div>
    <?php endif; ?>
