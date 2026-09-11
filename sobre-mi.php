<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Sobre Mí — Emmanuel Ramírez | Funktographer";
$pageDescription = "Soy Emmanuel Ramírez, la persona detrás de Funktographer. Fotógrafo y creador audiovisual con formación en publicidad y pasión por el funk.";
$pageImage = 'uploads/sobre-mi/emmanuel-ramirez-portrait.jpg';
$ogType = 'profile';
$pageCanonical = BASE_URL . '/sobre-mi';

include __DIR__ . '/includes/header.php';
?>

<!-- About Hero Section -->
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
    <span class="section-tag">Sobre Mí</span>
    <h1 class="hero-title">Emmanuel Ramírez y <span class="highlight">Funktographer</span></h1>
    <p class="hero-description">
      Fotógrafo y creador audiovisual con formación en publicidad y pasión por el funk, creando imágenes con estilo, ritmo y personalidad únicos.
    </p>
  </div>
</section>

<!-- Main Narrative Section -->
<section class="section" style="padding-top: 20px;">
  <div class="container">
    <div class="about-hero">
      <div class="about-text-content">
        <span class="section-tag">La Persona Detrás del Lente</span>
        <h2 class="section-title" style="text-align: left;">Creatividad, Ritmo y Enfoque Publicitario</h2>
        
        <p style="color: var(--text-main); margin-bottom: 20px; font-size: 1.1rem; line-height: 1.8; font-weight: 500;">
          Soy Emmanuel Ramírez, la persona detrás de Funktographer. Soy fotógrafo y creador audiovisual con formación en publicidad y pasión por el funk, la música que inspiró el nombre de mi marca. Combino estas influencias para crear imágenes con estilo, ritmo y personalidad únicos.
        </p>

        <p style="color: var(--text-muted); margin-bottom: 20px; font-size: 1.05rem; line-height: 1.8;">
          Para mí, cada imagen cuenta una historia. Con formación en publicidad y especialización en fotografía comercial, documental y dirección cinematográfica, veo la fotografía no solo como un registro estético, sino como una herramienta estratégica para conectar, emocionar y comunicar.
        </p>

        <p style="color: var(--text-muted); margin-bottom: 24px; font-size: 1.05rem; line-height: 1.8;">
          He desarrollado proyectos en Venezuela, Colombia, Ecuador, Perú y Chile. Hoy, desde Santiago, realizo fotografía y video corporativo, comercial y gastronómico, además de retratos, con una mirada cercana y atención al detalle.
        </p>

        <div style="background: rgba(79, 44, 163, 0.18); border-left: 4px solid var(--primary); padding: 18px 24px; border-radius: 0 14px 14px 0; margin-bottom: 30px;">
          <p style="font-family: var(--font-subtitle); color: var(--primary); font-size: 1.25rem; font-weight: normal; margin: 0; letter-spacing: 0.3px; line-height: 1.4;">
            &ldquo;Cuéntame tu idea. Construyamos juntos su expresión visual.&rdquo;
          </p>
        </div>

        <div style="display: flex; gap: 30px; margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 24px;">
          <div>
            <div style="font-family: var(--font-title); font-size: 2.4rem; font-weight: normal; color: #fff;">+10</div>
            <div style="font-family: var(--font-subtitle); color: var(--text-dim); font-size: 0.88rem; font-weight: normal; letter-spacing: 0.3px;">Años de Trayectoria</div>
          </div>
          <div>
            <div style="font-family: var(--font-title); font-size: 2.4rem; font-weight: normal; color: var(--primary);">5</div>
            <div style="font-family: var(--font-subtitle); color: var(--text-dim); font-size: 0.88rem; font-weight: normal; letter-spacing: 0.3px;">Países con Proyectos</div>
          </div>
          <div>
            <div style="font-family: var(--font-title); font-size: 2.4rem; font-weight: normal; color: var(--accent);">100%</div>
            <div style="font-family: var(--font-subtitle); color: var(--text-dim); font-size: 0.88rem; font-weight: normal; letter-spacing: 0.3px;">Compromiso y Ritmo</div>
          </div>
        </div>
      </div>

      <div class="about-img-grid">
        <div class="about-img-main">
          <img src="<?= BASE_URL ?>/uploads/sobre-mi/emmanuel-ramirez-portrait.jpg" alt="Emmanuel Ramírez - Funktographer" width="600" height="750" loading="lazy">
        </div>
        <div class="about-img-float">
          <img src="<?= BASE_URL ?>/uploads/sobre-mi/emmanuel-ramirez-guitar.jpg" alt="Emmanuel Ramírez - Creador visual" width="300" height="375" loading="lazy">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Services / Specialities Section -->
<section class="section" style="background: var(--bg-surface); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag">Lo Que Hacemos</span>
      <h2 class="section-title">Especialidades Fotográficas</h2>
      <p class="section-subtitle">Soluciones de imagen profesionales diseñadas para potenciar tu marca</p>
    </div>

    <div class="services-grid">
      <!-- Service 1 -->
      <div class="service-card">
        <div class="service-num" style="color: var(--primary);">01</div>
        <h4>Eventos Corporativos y Congresos</h4>
        <p>
          Cobertura discreta y dinámica para convenciones, foros empresariales, lanzamientos de productos y aniversarios. Entrega ágil para prensa y redes.
        </p>
      </div>

      <!-- Service 2 -->
      <div class="service-card">
        <div class="service-num" style="color: var(--primary);">02</div>
        <h4>Fotografía Gastronómica y Bebidas</h4>
        <p>
          Estilismo culinario e iluminación controlada para restaurantes, pastelerías y marcas de autor. Platos que despiertan el apetito a través de la pantalla.
        </p>
      </div>

      <!-- Service 3 -->
      <div class="service-card">
        <div class="service-num" style="color: var(--primary);">03</div>
        <h4>Retrato Editorial y Corporativo</h4>
        <p>
          Headshots para directores ejecutivos, equipos de trabajo, modelos y artistas. Dirección de poses natural y retoque de piel de alta fidelidad.
        </p>
      </div>

      <!-- Service 4 -->
      <div class="service-card">
        <div class="service-num" style="color: var(--primary);">04</div>
        <h4>Producción Audiovisual y Video</h4>
        <p>
          Cápsulas en video 4K, reels dinámicos para Instagram/TikTok y aftermovies de eventos con edición rítmica y corrección de color profesional.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Equipamiento y Calidad -->
<section class="section">
  <div class="container">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 50px 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
      <div>
        <span class="section-tag">Tecnología de Vanguardia</span>
        <h3 style="font-family: var(--font-title); font-size: 2.2rem; font-weight: normal; color: #fff; margin-bottom: 18px; line-height: 1.2;">
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
