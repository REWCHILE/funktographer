<?php
$adminTitle = "Mensajes de Contacto";
require_once __DIR__ . '/header.php';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);

    if ($action === 'toggle_read') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = IF(is_read = 1, 0, 1) WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Estado del mensaje actualizado.');
        header("Location: " . BASE_URL . "/admin/messages");
        exit;
    }

    if ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Mensaje eliminado.');
        header("Location: " . BASE_URL . "/admin/messages");
        exit;
    }
}

// Fetch all messages
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>

<div class="adm-header">
  <div>
    <h1 class="adm-title">Bandeja de Contacto</h1>
    <p class="adm-subtitle">Consultas y solicitudes de cotización recibidas desde el sitio web</p>
  </div>
</div>

<div class="adm-card">
  <div class="adm-card-header">
    <h3 class="adm-card-title">Mensajes Recibidos (<?= count($messages) ?>)</h3>
  </div>

  <?php if (empty($messages)): ?>
    <p style="color: var(--adm-muted); text-align: center; padding: 40px 0;">No se han recibido mensajes aún.</p>
  <?php else: ?>
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Remitente</th>
            <th>Servicio Solicitado</th>
            <th>Mensaje</th>
            <th>Estado</th>
            <th style="text-align: right;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($messages as $m): ?>
            <tr style="<?= ($m['is_read'] == 0) ? 'background: rgba(121, 56, 226, 0.05);' : '' ?>">
              <td style="font-size: 0.85rem; color: var(--adm-muted); white-space: nowrap;">
                <?= date('d/m/Y', strtotime($m['created_at'])) ?><br>
                <span style="font-size: 0.75rem;"><?= date('H:i', strtotime($m['created_at'])) ?> hrs</span>
              </td>
              <td>
                <strong style="color: #fff;"><?= htmlspecialchars($m['name']) ?></strong><br>
                <a href="mailto:<?= htmlspecialchars($m['email']) ?>" style="font-size: 0.82rem; color: var(--adm-primary);">
                  <?= htmlspecialchars($m['email']) ?>
                </a>
                <?php if ($m['phone']): ?>
                  <br><span style="font-size: 0.8rem; color: var(--adm-muted);"><i class="fas fa-phone-alt"></i> <?= htmlspecialchars($m['phone']) ?></span>
                <?php endif; ?>
              </td>
              <td>
                <span class="adm-badge" style="background: rgba(255,255,255,0.06); color: #fff;">
                  <?= htmlspecialchars($m['service_type'] ?: 'General') ?>
                </span>
              </td>
              <td style="max-width: 320px; font-size: 0.9rem; line-height: 1.4;">
                <div style="background: rgba(0,0,0,0.3); padding: 10px 14px; border-radius: 8px; border: 1px solid var(--adm-border);">
                  <?= nl2br(htmlspecialchars($m['message'])) ?>
                </div>
              </td>
              <td>
                <?php if ($m['is_read']): ?>
                  <span class="pill-inactive" style="border-color: var(--adm-border); color: var(--adm-muted); background: transparent;">Atendido</span>
                <?php else: ?>
                  <span class="pill-active">Nuevo</span>
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 6px;">
                  <!-- Reply via email -->
                  <a href="mailto:<?= htmlspecialchars($m['email']) ?>?subject=<?= urlencode('Respuesta a tu consulta — Funktographer') ?>" class="adm-btn adm-btn-outline adm-btn-sm" title="Responder por email">
                    <i class="fas fa-reply"></i>
                  </a>

                  <!-- Reply via WhatsApp if phone provided -->
                  <?php if ($m['phone']): 
                    $cleanPhone = preg_replace('/[^0-9]/', '', $m['phone']);
                  ?>
                    <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= urlencode('Hola ' . $m['name'] . ', te escribimos desde Funktographer respecto a tu consulta:') ?>" target="_blank" class="adm-btn adm-btn-outline adm-btn-sm" style="color: #25d366; border-color: rgba(37,211,102,0.3);" title="Escribir por WhatsApp">
                      <i class="fab fa-whatsapp"></i>
                    </a>
                  <?php endif; ?>

                  <!-- Toggle Read -->
                  <form action="<?= BASE_URL ?>/admin/messages" method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="toggle_read">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <button type="submit" class="adm-btn adm-btn-outline adm-btn-sm" title="Marcar como leído/no leído">
                      <i class="fas <?= $m['is_read'] ? 'fa-envelope-open' : 'fa-check' ?>"></i>
                    </button>
                  </form>

                  <!-- Delete -->
                  <form action="<?= BASE_URL ?>/admin/messages" method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm" title="Eliminar mensaje" data-confirm="¿Deseas eliminar este mensaje?">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
