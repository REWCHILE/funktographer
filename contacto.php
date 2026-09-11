<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Contacto — Reserva tu Sesión o Evento";
$pageDescription = "Comunícate con Funktographer. Cotiza servicios fotográficos corporativos, gastronómicos o sesiones de retrato en Santiago de Chile.";
$pageImage = 'uploads/home/Fotogo-Ponencia-cisco-mining-summit-2024-Funktographer.jpg';
$ogType = 'website';
$pageCanonical = BASE_URL . '/contacto';

$formSubmitted = false;
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $formError = "La sesión del formulario expiró. Por favor intenta nuevamente.";
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $serviceType = trim($_POST['service_type'] ?? 'General');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            $formError = "Por favor completa todos los campos obligatorios (Nombre, Email y Mensaje).";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $formError = "Por favor ingresa un correo electrónico válido.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, service_type, message) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $phone, $serviceType, $message]);
                $formSubmitted = true;
            } catch (Exception $e) {
                $formError = "Ocurrió un error al enviar el mensaje. Por favor contáctanos directamente vía WhatsApp.";
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Contact Hero Section -->
<section class="hero-section hero-page-header">
  <div class="hero-video-bg">
    <video autoplay muted loop playsinline preload="metadata" aria-hidden="true" poster="<?= BASE_URL ?>/assets/video/hero-poster.webp">
      <source media="(min-width: 769px)" src="<?= BASE_URL ?>/assets/video/hero-bg.mp4" type="video/mp4">
      <track kind="captions" src="data:text/vtt,WEBVTT" label="Silencio" default>
    </video>
    <div class="hero-video-overlay"></div>
  </div>
  <div class="hero-bg-glow"></div>
  <div class="container hero-content">
    <span class="section-tag">Hablemos</span>
    <h1 class="hero-title">Ponte en Contacto</h1>
    <p class="hero-description">
      Estamos listos para hacer realidad tu próxima producción fotográfica o cobertura corporativa. Escríbenos y responderemos a la brevedad.
    </p>
  </div>
</section>

<!-- Formulario de Contacto y Detalles -->
<section class="section" style="padding-top: 20px;">
  <div class="container">
    <div class="contact-layout">
      <!-- Left Column: Direct Info -->
      <div class="contact-card-info">
        <span class="section-tag">Canales Directos</span>
        <h3 style="font-family: var(--font-subtitle); font-size: 1.8rem; font-weight: normal; letter-spacing: 0.3px; color: #fff; margin-bottom: 24px;">
          Información de Contacto
        </h3>
        <p style="color: var(--text-muted); margin-bottom: 36px; line-height: 1.6;">
          Respondemos de forma ágil para coordinar reuniones presenciales o telemáticas y revisar detalles técnicos de tu requerimiento.
        </p>

        <!-- Phone -->
        <div class="contact-info-block">
          <div class="contact-info-icon">
            <i class="fas fa-phone-alt"></i>
          </div>
          <div class="contact-info-content">
            <h6>Teléfono Directo</h6>
            <a href="tel:<?= htmlspecialchars(get_setting('phone', '+56 9 4757 3794')) ?>">
              <?= htmlspecialchars(get_setting('phone', '+56 9 4757 3794')) ?>
            </a>
          </div>
        </div>

        <!-- WhatsApp -->
        <div class="contact-info-block">
          <div class="contact-info-icon" style="background: rgba(37, 211, 102, 0.15); color: #25d366;">
            <i class="fab fa-whatsapp"></i>
          </div>
          <div class="contact-info-content">
            <h6>WhatsApp Corporativo</h6>
            <a href="https://wa.me/<?= urlencode(get_setting('whatsapp', '56947573794')) ?>?text=Hola,%20quisiera%20cotizar%20un%20servicio" target="_blank" style="color: #25d366;">
              Conversar por WhatsApp
            </a>
          </div>
        </div>

        <!-- Email -->
        <div class="contact-info-block">
          <div class="contact-info-icon">
            <i class="fas fa-envelope"></i>
          </div>
          <div class="contact-info-content">
            <h6>Correo Electrónico</h6>
            <a href="mailto:<?= htmlspecialchars(get_setting('email', 'contacto@funktographer.cl')) ?>">
              <?= htmlspecialchars(get_setting('email', 'contacto@funktographer.cl')) ?>
            </a>
          </div>
        </div>

        <!-- Location -->
        <div class="contact-info-block">
          <div class="contact-info-icon">
            <i class="fas fa-map-marked-alt"></i>
          </div>
          <div class="contact-info-content">
            <h6>Base y Cobertura</h6>
            <p style="margin: 0; color: #fff; font-size: 1rem;">
              Santiago de Chile | Cobertura Nacional e Internacional
            </p>
          </div>
        </div>

        <!-- Instagram -->
        <div class="contact-info-block">
          <div class="contact-info-icon" style="background: rgba(225, 48, 108, 0.15); color: #e1306c;">
            <i class="fab fa-instagram"></i>
          </div>
          <div class="contact-info-content">
            <h6>Instagram Oficial</h6>
            <a href="<?= htmlspecialchars(get_setting('instagram', 'https://www.instagram.com/funktographer/')) ?>" target="_blank">
              @funktographer
            </a>
          </div>
        </div>
      </div>

      <!-- Right Column: Interactive Form -->
      <div class="form-box">
        <h3 style="font-family: var(--font-subtitle); font-size: 1.8rem; font-weight: normal; letter-spacing: 0.3px; color: #fff; margin-bottom: 8px;">
          Envíanos un Mensaje
        </h3>
        <p style="color: var(--text-muted); margin-bottom: 28px;">
          Completa los datos y te enviaremos una propuesta formal o nos comunicaremos contigo.
        </p>

        <?php if ($formSubmitted): ?>
          <div class="alert alert-success">
            <i class="fas fa-check-circle" style="font-size: 1.4rem;"></i>
            <div>
              <strong>¡Mensaje recibido con éxito!</strong><br>
              Gracias por contactarnos. Hemos recibido tu solicitud y nos comunicaremos contigo a la brevedad.
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($formError)): ?>
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle" style="font-size: 1.4rem;"></i>
            <div><?= htmlspecialchars($formError) ?></div>
          </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/contacto" method="POST">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="name">Nombre y Apellido *</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Ej: Marcela Gómez" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label" for="email">Correo Electrónico *</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="correo@empresa.cl" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="phone">Teléfono / WhatsApp</label>
              <input type="tel" class="form-control" id="phone" name="phone" placeholder="+56 9 1234 5678" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label" for="service_type">Tipo de Servicio</label>
              <select class="form-control" id="service_type" name="service_type">
                <option value="Eventos Corporativos">Eventos Corporativos y Congresos</option>
                <option value="Fotografía Gastronómica">Fotografía Gastronómica y Carta</option>
                <option value="Retratos y Moda">Retrato Editorial / Headshots</option>
                <option value="Producción Audiovisual">Producción Audiovisual / Video</option>
                <option value="Otro">Otro requerimiento</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="message">Detalles del Requerimiento *</label>
            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Indícanos la fecha estimada, lugar, tipo de evento o detalles de la sesión..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px;">
            <i class="fas fa-paper-plane"></i> Enviar Mensaje
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
