<?php
$adminTitle = "Dashboard";
require_once __DIR__ . '/header.php';

// Stats
$activeHomeImgs = $pdo->query("SELECT COUNT(*) FROM home_images WHERE is_active = 1")->fetchColumn();
$totalHomeImgs = $pdo->query("SELECT COUNT(*) FROM home_images")->fetchColumn();
$totalProjects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$unreadMsgs = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
$totalMsgs = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();

// Recent messages
$stmtRecentMsgs = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$recentMsgs = $stmtRecentMsgs->fetchAll();

// Recent home images
$stmtRecentImgs = $pdo->query("SELECT * FROM home_images ORDER BY id DESC LIMIT 6");
$recentImgs = $stmtRecentImgs->fetchAll();
?>

<div class="adm-header">
  <div>
    <h1 class="adm-title">Panel de Control</h1>
    <p class="adm-subtitle">Bienvenido al administrador de contenidos de Funktographer</p>
  </div>
  <div style="display: flex; gap: 12px;">
    <a href="<?= BASE_URL ?>/admin/home-images#uploadForm" class="adm-btn adm-btn-outline">
      <i class="fas fa-plus"></i> Subir Foto Home
    </a>
    <a href="<?= BASE_URL ?>/admin/project-form" class="adm-btn adm-btn-primary">
      <i class="fas fa-folder-plus"></i> Nuevo Proyecto
    </a>
  </div>
</div>

<!-- Stats Grid -->
<div class="adm-stats-grid">
  <!-- Card 1 -->
  <div class="adm-stat-card">
    <div class="adm-stat-icon">
      <i class="fas fa-images"></i>
    </div>
    <div>
      <div class="adm-stat-val"><?= $activeHomeImgs ?> <span style="font-size: 1rem; color: var(--adm-muted); font-weight: 400;">/ <?= $totalHomeImgs ?></span></div>
      <div class="adm-stat-lbl">Fotos Activas en Home</div>
    </div>
  </div>

  <!-- Card 2 -->
  <div class="adm-stat-card">
    <div class="adm-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
      <i class="fas fa-folder"></i>
    </div>
    <div>
      <div class="adm-stat-val"><?= $totalProjects ?></div>
      <div class="adm-stat-lbl">Proyectos Publicados</div>
    </div>
  </div>

  <!-- Card 3 -->
  <div class="adm-stat-card">
    <div class="adm-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
      <i class="fas fa-envelope"></i>
    </div>
    <div>
      <div class="adm-stat-val"><?= $unreadMsgs ?> <span style="font-size: 1rem; color: var(--adm-muted); font-weight: 400;">/ <?= $totalMsgs ?></span></div>
      <div class="adm-stat-lbl">Mensajes Sin Leer</div>
    </div>
  </div>
</div>

<!-- Quick Overview Layout -->
<div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px;">
  <!-- Recent Messages Box -->
  <div class="adm-card">
    <div class="adm-card-header">
      <h3 class="adm-card-title"><i class="fas fa-inbox" style="color: var(--adm-primary); margin-right: 8px;"></i> Últimas Solicitudes de Contacto</h3>
      <a href="<?= BASE_URL ?>/admin/messages" class="adm-btn adm-btn-outline adm-btn-sm">Ver Todas</a>
    </div>

    <?php if (empty($recentMsgs)): ?>
      <p style="color: var(--adm-muted); text-align: center; padding: 30px 0;">No hay mensajes registrados aún.</p>
    <?php else: ?>
      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th>Remitente</th>
              <th>Servicio</th>
              <th>Fecha</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentMsgs as $m): ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($m['name']) ?></strong><br>
                  <span style="font-size: 0.8rem; color: var(--adm-muted);"><?= htmlspecialchars($m['email']) ?></span>
                </td>
                <td><?= htmlspecialchars($m['service_type'] ?: 'General') ?></td>
                <td style="font-size: 0.85rem; color: var(--adm-muted);"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                <td>
                  <?php if ($m['is_read']): ?>
                    <span class="pill-active" style="background: rgba(255,255,255,0.05); color: var(--adm-muted); border-color: var(--adm-border);">Leído</span>
                  <?php else: ?>
                    <span class="pill-active">Nuevo</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- Recent Home Photos Box -->
  <div class="adm-card">
    <div class="adm-card-header">
      <h3 class="adm-card-title"><i class="fas fa-photo-video" style="color: var(--adm-primary); margin-right: 8px;"></i> Galería del Home</h3>
      <a href="<?= BASE_URL ?>/admin/home-images" class="adm-btn adm-btn-outline adm-btn-sm">Administrar</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
      <?php foreach ($recentImgs as $rImg): ?>
        <div style="position: relative; aspect-ratio: 1; border-radius: 8px; overflow: hidden; background: #000;">
          <img src="<?= BASE_URL ?>/<?= htmlspecialchars($rImg['image_url']) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
          <div style="position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0,0,0,0.7); padding: 4px 6px; font-size: 0.72rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #fff;">
            <?= htmlspecialchars($rImg['title']) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
