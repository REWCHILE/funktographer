<?php
$siteTitle = get_setting('site_name', 'Funktographer');
$phone = get_setting('phone', '+56 9 4757 3794');
$email = get_setting('email', 'contacto@funktographer.cl');
$instagram = get_setting('instagram', 'https://www.instagram.com/funktographer/');
$coverage = get_setting('coverage', 'Santiago de Chile / Cobertura Nacional e Internacional');
?>
  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-top">
        <div class="footer-brand">
          <a href="<?= BASE_URL ?>/" class="brand-logo" title="<?= htmlspecialchars($siteTitle) ?>">
            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="<?= htmlspecialchars($siteTitle) ?>">
          </a>
          <p>
            Fotografía y producción audiovisual especializada en capturar la esencia de eventos corporativos, creaciones gastronómicas y retratos profesionales en Santiago de Chile.
          </p>
          <div class="drawer-socials" style="margin-top: 0;">
            <a href="<?= htmlspecialchars($instagram) ?>" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://wa.me/<?= urlencode(get_setting('whatsapp', '56947573794')) ?>" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
          </div>
        </div>

        <div class="footer-col">
          <h5 class="footer-title">Navegación</h5>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>/">Inicio</a></li>
            <li><a href="<?= BASE_URL ?>/proyectos">Proyectos</a></li>
            <li><a href="<?= BASE_URL ?>/blog">Blog &amp; Artículos</a></li>
            <li><a href="<?= BASE_URL ?>/nosotros">Nosotros</a></li>
            <li><a href="<?= BASE_URL ?>/contacto">Contacto</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h5 class="footer-title">Especialidades</h5>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>/proyectos?cat=Eventos">Eventos Corporativos</a></li>
            <li><a href="<?= BASE_URL ?>/proyectos?cat=Gastronomia">Fotografía Gastronómica</a></li>
            <li><a href="<?= BASE_URL ?>/proyectos?cat=Retratos">Retratos & Moda</a></li>
            <li><a href="<?= BASE_URL ?>/contacto">Producción Audiovisual</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h5 class="footer-title">Contacto</h5>
          <ul class="footer-links">
            <li><i class="fas fa-phone-alt" style="color: var(--primary); margin-right: 8px;"></i> <a href="tel:<?= htmlspecialchars($phone) ?>"><?= htmlspecialchars($phone) ?></a></li>
            <li><i class="fas fa-envelope" style="color: var(--primary); margin-right: 8px;"></i> <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></li>
            <li><i class="fas fa-map-marker-alt" style="color: var(--primary); margin-right: 8px;"></i> <span><?= htmlspecialchars($coverage) ?></span></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($siteTitle) ?>. Todos los derechos reservados.</p>
        <div>
          <a href="<?= BASE_URL ?>/admin/login" style="color: var(--text-dim);"><i class="fas fa-lock" style="font-size: 0.8rem;"></i> Panel Admin</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Main JavaScript -->
  <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
