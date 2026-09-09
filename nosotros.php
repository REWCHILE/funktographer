<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Sobre Nosotros — La Visión de Funktographer";
$pageDescription = "Conoce la filosofía, enfoque y equipo técnico detrás de Funktographer. Fotografía profesional en Santiago de Chile.";

include __DIR__ . '/includes/header.php';
?>

<!-- About Hero Section -->
<section class="hero-section hero-page-header">
  <div class="hero-video-bg">
    <video autoplay muted loop playsinline poster="<?= BASE_URL ?>/assets/video/hero-poster.jpg">
      <source src="<?= BASE_URL ?>/assets/video/hero-bg.mp4" type="video/mp4">
    </video>
    <div class="hero-video-overlay"></div>
  </div>
  <div class="hero-bg-glow"></div>
  <div class="container hero-content">
    <span class="section-tag">Nuestra Identidad</span>
    <h1 class="hero-title">Narrativa Visual con <span class="highlight">Fuerza &amp; Precisión</span></h1>
    <p class="hero-description">
      En <strong>Funktographer</strong> entendemos la fotografía no como un simple registro estático, sino como un puente emocional que comunica la grandeza de cada ocasión y cada producto.
    </p>
  </div>
</section>

<!-- Main Narrative Section -->
<section class="section" style="padding-top: 20px;">
  <div class="container">
    <div class="about-hero">
      <div class="about-text-content">
        <span class="section-tag">Filosofía</span>
        <h2 class="section-title" style="text-align: left;">Capturamos la Energía Invisible</h2>
        <p style="color: var(--text-muted); margin-bottom: 20px; font-size: 1.05rem; line-height: 1.7;">
          Detrás de cada evento corporativo hay meses de preparación, visión y liderazgo. Detrás de cada plato gastronómico hay arte, textura y pasión culinaria. Y detrás de cada retrato hay una personalidad única esperando ser revelada.
        </p>
        <p style="color: var(--text-muted); margin-bottom: 28px; font-size: 1.05rem; line-height: 1.7;">
          Nuestra misión es inmortalizar esos elementos efímeros con una mirada cinematográfica, cuidando meticulosamente la luz, el encuadre y el ritmo visual para entregar material publicitario y documental que destaque a nuestros clientes en cualquier medio.
        </p>

        <div style="display: flex; gap: 30px; margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 24px;">
          <div>
            <div style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 800; color: #fff;">+10</div>
            <div style="color: var(--text-dim); font-size: 0.88rem; font-weight: 600;">Años de Trayectoria</div>
          </div>
          <div>
            <div style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 800; color: var(--primary);">+300</div>
            <div style="color: var(--text-dim); font-size: 0.88rem; font-weight: 600;">Eventos &amp; Sesiones</div>
          </div>
          <div>
            <div style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 800; color: var(--accent);">100%</div>
            <div style="color: var(--text-dim); font-size: 0.88rem; font-weight: 600;">Compromiso Visual</div>
          </div>
        </div>
      </div>

      <div class="about-img-grid">
        <div class="about-img-main">
          <img src="<?= BASE_URL ?>/uploads/home/Foto-evento-Algo-electrico-con-quimica-Funktographer-24.jpg" alt="Cobertura en terreno Funktographer">
        </div>
        <div class="about-img-float">
          <img src="<?= BASE_URL ?>/uploads/home/Retrato-modelo-genesis-6-Funktographer.jpg" alt="Retrato y detalle">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Services / Specialities Section -->
<section class="section" style="background: #0d0d12; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag">Lo Que Hacemos</span>
      <h2 class="section-title">Especialidades Fotográficas</h2>
      <p class="section-subtitle">Soluciones de imagen profesionales diseñadas para potenciar tu marca</p>
    </div>

    <div class="services-grid">
      <!-- Service 1 -->
      <div class="service-card">
        <div class="service-num">01</div>
        <h4>Eventos Corporativos &amp; Congresos</h4>
        <p>
          Cobertura discreta y dinámica para convenciones, foros empresariales, lanzamientos de productos y aniversarios. Entrega ágil para prensa y redes.
        </p>
      </div>

      <!-- Service 2 -->
      <div class="service-card">
        <div class="service-num">02</div>
        <h4>Fotografía Gastronómica &amp; Bebidas</h4>
        <p>
          Estilismo culinario e iluminación controlada para restaurantes, pastelerías y marcas de autor. Platos que despiertan el apetito a través de la pantalla.
        </p>
      </div>

      <!-- Service 3 -->
      <div class="service-card">
        <div class="service-num">03</div>
        <h4>Retrato Editorial &amp; Corporativo</h4>
        <p>
          Headshots para directores ejecutivos, equipos de trabajo, modelos y artistas. Dirección de poses natural y retoque de piel de alta fidelidad.
        </p>
      </div>

      <!-- Service 4 -->
      <div class="service-card">
        <div class="service-num">04</div>
        <h4>Producción Audiovisual &amp; Video</h4>
        <p>
          Cápsulas en video 4K, reels dinámicos para Instagram/TikTok y aftermovies de eventos con edición rítmica y corrección de color profesional.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Gear & Quality Section -->
<section class="section">
  <div class="container">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 50px 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
      <div>
        <span class="section-tag">Tecnología de Vanguardia</span>
        <h3 style="font-family: var(--font-heading); font-size: 2rem; color: #fff; margin-bottom: 18px; line-height: 1.2;">
          Equipamiento de Máxima Definición
        </h3>
        <p style="color: var(--text-muted); margin-bottom: 20px; line-height: 1.6;">
          Trabajamos con cuerpos de cámara sin espejo como la <strong>Fujifilm X-T4</strong> y ópticas fijas luminosas de alta gama, capaces de ofrecer una riqueza cromática, nitidez y fidelidad tonal insuperables en cualquier condición de luz.
        </p>
        <ul style="list-style: none; color: var(--text-muted); display: grid; gap: 12px;">
          <li><i class="fas fa-check" style="color: var(--primary); margin-right: 10px;"></i> Sensores con colorimetría cinematográfica para tonos de piel perfectos</li>
          <li><i class="fas fa-check" style="color: var(--primary); margin-right: 10px;"></i> Iluminación inalámbrica TTL y modificadores de luz de estudio</li>
          <li><i class="fas fa-check" style="color: var(--primary); margin-right: 10px;"></i> Flujo de trabajo con respaldo doble instantáneo de seguridad</li>
        </ul>
      </div>
      <div>
        <img src="<?= BASE_URL ?>/uploads/home/foto-camara-fujifilm-xt4-Funktographer-jpg.jpg" alt="Fujifilm X-T4 Funktographer" style="border-radius: var(--radius-md); width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
      </div>
    </div>
  </div>
</section>

<!-- Call to Action Banner -->
<section class="section" style="background: linear-gradient(180deg, var(--bg-dark) 0%, #120e20 100%); text-align: center;">
  <div class="container">
    <div class="section-title-wrap" style="margin-bottom: 30px;">
      <span class="section-tag">¿Hablamos de tu proyecto?</span>
      <h2 class="section-title">Iniciemos tu Próxima Sesión</h2>
      <p class="section-subtitle">
        Cuéntanos tu idea o evento y diseñemos la cobertura visual que estás buscando.
      </p>
    </div>
    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
      <a href="<?= BASE_URL ?>/contacto" class="btn btn-primary">
        <i class="fas fa-envelope"></i> Escríbenos
      </a>
      <a href="https://wa.me/<?= urlencode(get_setting('whatsapp', '56947573794')) ?>?text=Hola,%20quisiera%20consultar%20por%20una%20sesion%20fotografica" target="_blank" class="btn btn-outline">
        <i class="fab fa-whatsapp"></i> Chat WhatsApp
      </a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
