-- Funktographer Database Dump
-- Generated: 2026-09-09 16:36:17

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admins` VALUES
('1', 'admin', 'contacto@funktographer.cl', '$2y$10$AkQUkL7GngmSjwj9WVN40OxAm76GcZWD1MneTpCoy.i57mnUCNKqq', '2026-09-08 15:53:50');

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` VALUES
('1', 'Eventos Corporativos', 'Eventos', '1', '1', '2026-09-09 12:17:19'),
('2', 'Gastronomía y Culinaria', 'Gastronomia', '2', '1', '2026-09-09 12:17:19'),
('3', 'Retratos y Moda', 'Retratos', '3', '1', '2026-09-09 12:17:19'),
('4', 'Producción Audiovisual', 'Video', '4', '1', '2026-09-09 12:17:19'),
('5', 'Equipamiento', 'Equipo', '5', '1', '2026-09-09 12:17:19'),
('6', 'General', 'General', '6', '1', '2026-09-09 12:17:19');

DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` VALUES
('1', 'site_name', 'Funktographer'),
('2', 'site_tagline', 'Fotografía y Video Profesional en Santiago de Chile'),
('3', 'phone', '+56 9 4757 3794'),
('4', 'whatsapp', '56947573794'),
('5', 'email', 'contacto@funktographer.cl'),
('6', 'instagram', 'https://www.instagram.com/funktographer/'),
('7', 'coverage', 'Santiago de Chile / Cobertura Nacional e Internacional'),
('8', 'about_short', 'Capturamos mucho más que momentos. Contamos historias a través de imágenes, preservando las emociones, los lugares y las experiencias que hacen que cada recuerdo sea inolvidable.');

DROP TABLE IF EXISTS `bio_links`;
CREATE TABLE `bio_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-link',
  `badge` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_type` enum('primary','gradient','outline','glow') COLLATE utf8mb4_unicode_ci DEFAULT 'primary',
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bio_links` VALUES
('1', 'Sitio Web Oficial', 'Explora nuestro portafolio y servicios completos', '/', 'fas fa-globe', 'Portafolio', 'glow', '1', '1', '2026-09-09 12:02:52'),
('2', 'WhatsApp Directo', 'Conversa con nosotros y cotiza tu evento o sesión', 'https://wa.me/56947573794', 'fab fa-whatsapp', 'Cotizaciones', 'primary', '2', '1', '2026-09-09 12:02:52'),
('3', 'Instagram Oficial', '@funktographer • Reels, backstage y coberturas', 'https://www.instagram.com/funktographer/', 'fab fa-instagram', 'Comunidad', 'gradient', '3', '1', '2026-09-09 12:02:52'),
('4', 'Proyectos en Video', 'Nuestras producciones y aftermovies en YouTube', 'https://www.youtube.com/@yeeeahlatam/videos', 'fab fa-youtube', 'Audiovisual', 'primary', '4', '1', '2026-09-09 12:02:52'),
('5', 'Portafolio en Behance', 'Curaduría de diseño, fotografía y proyectos visuales', 'https://www.behance.net/funktographer', 'fab fa-behance', 'Diseño', 'outline', '5', '1', '2026-09-09 12:02:52'),
('6', 'Galería 500px', 'Tomas de alta fidelidad fotográfica y resolución', 'https://500px.com/funktographer', 'fas fa-camera-retro', 'Alta Res', 'outline', '6', '1', '2026-09-09 12:02:52');

DROP TABLE IF EXISTS `home_images`;
CREATE TABLE `home_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `home_images` VALUES
('1', 'Retrato Modelo Genesis 6 Funktographer', 'uploads/home/Retrato-modelo-genesis-6-Funktographer.jpg', 'Retratos', '1', '0', '2026-09-08 15:53:50'),
('2', 'Evento Orsan 2024 B  ', 'uploads/home/Evento-Orsan_2024_b-scaled.jpg', 'Eventos', '2', '1', '2026-09-08 15:53:50'),
('3', 'Evento Orsan Funktographer 5  ', 'uploads/home/Evento-Orsan-Funktographer-5-scaled.jpg', 'Eventos', '3', '1', '2026-09-08 15:53:50'),
('4', 'Giselle Bog Park', 'uploads/home/giselle_bog_park.jpg', 'Retratos', '4', '1', '2026-09-08 15:53:50'),
('5', 'Evento Orsan Funktographer 7  ', 'uploads/home/Evento-Orsan-Funktographer-7-scaled.jpg', 'Eventos', '5', '1', '2026-09-08 15:53:50'),
('6', 'Giselle Esc Bog Park', 'uploads/home/giselle_esc_bog_park.jpg', 'Retratos', '6', '1', '2026-09-08 15:53:50'),
('7', 'Ari Bokeh  ', 'uploads/home/ari_bokeh_1.jpg', 'Retratos', '7', '1', '2026-09-08 15:53:50'),
('8', 'Foto Evento Algo Electrico Con Quimica Funktographer 24', 'uploads/home/Foto-evento-Algo-electrico-con-quimica-Funktographer-24.jpg', 'Eventos', '8', '1', '2026-09-08 15:53:50'),
('9', 'Pryscila Workshop Gaston', 'uploads/home/Pryscila_workshop_gaston.jpg', 'Retratos', '9', '1', '2026-09-08 15:53:50'),
('10', 'Photo 2022    22  0.46.03', 'uploads/home/photo_2022-11-22-10.46.03.jpeg', 'Retratos', '10', '1', '2026-09-08 15:53:50'),
('11', 'Ramen One Fideos Web4466', 'uploads/home/Ramen-One-Fideos-Web4466.jpg', 'Gastronomia', '11', '1', '2026-09-08 15:53:50'),
('12', 'Tartaleta De Frutas Pine Cone  ', 'uploads/home/Tartaleta-de-frutas_Pine-Cone_1.jpg', 'Gastronomia', '12', '1', '2026-09-08 15:53:50'),
('13', 'Genesis 2', 'uploads/home/Genesis_2.jpg', 'Retratos', '13', '1', '2026-09-08 15:53:50'),
('14', 'DSF2  0 Editar', 'uploads/home/DSF2110-Editar.jpg', 'Retratos', '14', '1', '2026-09-08 15:53:50'),
('15', 'Ari Oficina', 'uploads/home/Ari_oficina.jpg', 'Retratos', '15', '1', '2026-09-08 15:53:50'),
('16', 'Foto Surf And Turf Dgang Funktographer', 'uploads/home/Foto-Surf-and-Turf-Dgang-Funktographer.jpg', 'Gastronomia', '16', '1', '2026-09-08 15:53:50'),
('17', 'Postre Pine Cone', 'uploads/home/Postre_Pine-Cone.jpg', 'Gastronomia', '17', '1', '2026-09-08 15:53:50'),
('18', 'Evento Orsan Funktographer 2', 'uploads/home/Evento-Orsan-Funktographer-2.jpg', 'Eventos', '18', '1', '2026-09-08 15:53:50'),
('19', 'Pedro   Flyer Gris', 'uploads/home/Pedro_1_Flyer_Gris.jpg', 'Retratos', '19', '1', '2026-09-08 15:53:50'),
('20', 'Croissant', 'uploads/home/Croissant.jpg', 'Gastronomia', '20', '1', '2026-09-08 15:53:50'),
('21', 'Genesis 4', 'uploads/home/Genesis_4.jpg', 'Retratos', '21', '1', '2026-09-08 15:53:50'),
('22', 'Profiterol Pine Cone', 'uploads/home/Profiterol_Pine-Cone.jpg', 'Gastronomia', '22', '1', '2026-09-08 15:53:50'),
('23', 'Diana Arbol 3', 'uploads/home/diana_arbol_3.jpg', 'Retratos', '23', '1', '2026-09-08 15:53:50'),
('24', 'Evento Orsan Funktographer 6  ', 'uploads/home/Evento-Orsan-Funktographer-6-scaled.jpg', 'Eventos', '24', '1', '2026-09-08 15:53:50'),
('25', 'Giselle Red Bog Park    ', 'uploads/home/giselle_red_bog_park-scaled-1.jpg', 'Retratos', '25', '1', '2026-09-08 15:53:50'),
('26', 'Solo Divas Cl2920  ', 'uploads/home/Solo-divas-cl2920-scaled.jpg', 'Retratos', '26', '1', '2026-09-08 15:53:50'),
('27', 'Orsan    ', 'uploads/home/Orsan-1-scaled.jpg', 'Eventos', '27', '1', '2026-09-08 15:53:50'),
('28', 'Genesis  0', 'uploads/home/genesis-10.jpg', 'Retratos', '28', '1', '2026-09-08 15:53:50'),
('29', 'Gisell Tree', 'uploads/home/gisell_tree.jpg', 'Retratos', '29', '1', '2026-09-08 15:53:50'),
('30', 'Evento Orsan Funktographer 3', 'uploads/home/Evento-Orsan-Funktographer-3.jpg', 'Eventos', '30', '1', '2026-09-08 15:53:50'),
('31', 'Foto Camara Fujifilm Xt4 Funktographer Jpg', 'uploads/home/foto-camara-fujifilm-xt4-Funktographer-jpg.jpg', 'Equipo', '31', '1', '2026-09-08 15:53:50'),
('32', 'Evento Orsan Funktographer  ', 'uploads/home/Evento-Orsan-Funktographer-1.jpg', 'Eventos', '32', '1', '2026-09-08 15:53:50'),
('33', 'Diana Ec Arbol 2', 'uploads/home/Diana_ec_arbol_2.jpg', 'Retratos', '33', '1', '2026-09-08 15:53:50'),
('34', 'Alison   Prueba', 'uploads/home/alison_1_prueba.jpg', 'Retratos', '34', '1', '2026-09-08 15:53:50'),
('35', 'Evento Orsan Funktographer 4  ', 'uploads/home/Evento-Orsan-Funktographer-4-scaled.jpg', 'Eventos', '35', '1', '2026-09-08 15:53:50'),
('36', 'Fotogo Ponencia Cisco Mining Summit 2024 Funktographer', 'uploads/home/Fotogo-Ponencia-cisco-mining-summit-2024-Funktographer.jpg', 'Eventos', '36', '1', '2026-09-08 15:53:50'),
('37', 'Torta Pine Cone', 'uploads/home/Torta_Pine-Cone.jpg', 'Gastronomia', '37', '1', '2026-09-08 15:53:50'),
('38', 'JEN    ', 'uploads/home/JEN-scaled-1.jpg', 'Retratos', '38', '1', '2026-09-08 15:53:50'),
('39', 'Genesis 3', 'uploads/home/Genesis_3.jpg', 'Retratos', '39', '1', '2026-09-08 15:53:50'),
('40', 'Diana Ec  ', 'uploads/home/diana_ec_1.jpg', 'Retratos', '40', '1', '2026-09-08 15:53:50'),
('41', 'Yona  2', 'uploads/home/Yona_12.jpg', 'Retratos', '41', '1', '2026-09-08 15:53:50'),
('42', 'Cocteles Dgang 27 Dic8887', 'uploads/home/Cocteles-Dgang-27-Dic8887.jpg', 'Gastronomia', '42', '1', '2026-09-08 15:53:50'),
('43', 'foto ejemplo', 'uploads/home/funk_6aa17ee611c271.29327197.png', 'Eventos', '1', '1', '2026-09-09 12:44:38');

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Eventos',
  `client` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_date` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `extra_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_content` text COLLATE utf8mb4_unicode_ci,
  `video_type` enum('none','youtube','upload') COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `video_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` VALUES
('1', 'Reportaje Corporativo WEG: ¡Conectados para Impulsar!', 'reportaje-corporativo-weg', 'Eventos', 'WEG Chile', 'Marzo 2024', 'Cobertura integral fotográfica y audiovisual del evento anual corporativo de WEG Chile. Captura de ponencias, ambiente networking, interacción entre líderes y momentos clave con iluminación controlada en salón.', 'uploads/home/Fotogo-Ponencia-cisco-mining-summit-2024-Funktographer.jpg', '1', '1', '2026-09-08 15:53:50', 'Aftermovie Oficial WEG Chile', 'Registro cinematográfico de alta calidad con cobertura completa del evento aniversario y tomas de ambiente.', 'youtube', 'https://www.youtube.com/watch?v=0Y2jYlX2eFU', NULL),
('2', 'Convención Anual Orsan 2024', 'convencion-anual-orsan-2024', 'Eventos', 'Orsan', 'Enero 2024', 'Registro dinámico del congreso corporativo y celebración empresarial para Orsan. Retratos protocolares en photocall, momentos de premiación, espectáculos en vivo y cierre festivo.', 'uploads/home/Evento-Orsan_2024_b-scaled.jpg', '1', '2', '2026-09-08 15:53:50', NULL, NULL, 'none', NULL, NULL),
('3', 'Carta Visual Gourmet: D’Gang y Pine Cone', 'carta-visual-dgang-pinecone', 'Gastronomia', 'D’Gang / Pastelería Pine Cone', 'Febrero 2024', 'Fotografía publicitaria y gastronómica de alta definición para menú impreso y plataformas digitales. Trabajo de texturas, iluminación lateral para realzar frescura y volumen de pastelería fina, platos calientes y coctelería de autor.', 'uploads/home/Foto-Surf-and-Turf-Dgang-Funktographer.jpg', '1', '3', '2026-09-08 15:53:50', NULL, NULL, 'none', NULL, NULL),
('4', 'Editorial de Retrato y Moda Urbana', 'editorial-retrato-moda-urbana', 'Retratos', 'Editorial Independiente / Modelos', '2024', 'Sesiones de retrato en locación urbana y estudio con énfasis en expresión, textura y paleta de color cinematográfica. Uso de ópticas fijas con gran apertura para aislar sujetos con desenfoque suave (bokeh).', 'uploads/home/Retrato-modelo-genesis-6-Funktographer.jpg', '1', '4', '2026-09-08 15:53:50', NULL, NULL, 'none', NULL, NULL);

DROP TABLE IF EXISTS `project_images`;
CREATE TABLE `project_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `project_images_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `project_images` VALUES
('1', '1', 'uploads/home/Fotogo-Ponencia-cisco-mining-summit-2024-Funktographer.jpg', 'Reportaje Corporativo WEG: ¡Conectados para Impulsar!', '1'),
('2', '1', 'uploads/home/Foto-evento-Algo-electrico-con-quimica-Funktographer-24.jpg', 'Reportaje Corporativo WEG: ¡Conectados para Impulsar!', '2'),
('3', '1', 'uploads/home/Evento-Orsan-Funktographer-5-scaled.jpg', 'Reportaje Corporativo WEG: ¡Conectados para Impulsar!', '3'),
('4', '2', 'uploads/home/Evento-Orsan_2024_b-scaled.jpg', 'Convención Anual Orsan 2024', '1'),
('5', '2', 'uploads/home/Evento-Orsan-Funktographer-2.jpg', 'Convención Anual Orsan 2024', '2'),
('6', '2', 'uploads/home/Evento-Orsan-Funktographer-3.jpg', 'Convención Anual Orsan 2024', '3'),
('7', '2', 'uploads/home/Evento-Orsan-Funktographer-7-scaled.jpg', 'Convención Anual Orsan 2024', '4'),
('8', '2', 'uploads/home/Orsan-1-scaled.jpg', 'Convención Anual Orsan 2024', '5'),
('9', '3', 'uploads/home/Foto-Surf-and-Turf-Dgang-Funktographer.jpg', 'Carta Visual Gourmet: D’Gang y Pine Cone', '1'),
('10', '3', 'uploads/home/Tartaleta-de-frutas_Pine-Cone_1.jpg', 'Carta Visual Gourmet: D’Gang y Pine Cone', '2'),
('11', '3', 'uploads/home/Postre_Pine-Cone.jpg', 'Carta Visual Gourmet: D’Gang y Pine Cone', '3'),
('12', '3', 'uploads/home/Ramen-One-Fideos-Web4466.jpg', 'Carta Visual Gourmet: D’Gang y Pine Cone', '4'),
('13', '3', 'uploads/home/Croissant.jpg', 'Carta Visual Gourmet: D’Gang y Pine Cone', '5'),
('14', '3', 'uploads/home/Cocteles-Dgang-27-Dic8887.jpg', 'Carta Visual Gourmet: D’Gang y Pine Cone', '6'),
('15', '4', 'uploads/home/Retrato-modelo-genesis-6-Funktographer.jpg', 'Editorial de Retrato y Moda Urbana', '1'),
('16', '4', 'uploads/home/giselle_bog_park.jpg', 'Editorial de Retrato y Moda Urbana', '2'),
('17', '4', 'uploads/home/Diana_ec_arbol_2.jpg', 'Editorial de Retrato y Moda Urbana', '3'),
('18', '4', 'uploads/home/ari_bokeh_1.jpg', 'Editorial de Retrato y Moda Urbana', '4'),
('19', '4', 'uploads/home/Pryscila_workshop_gaston.jpg', 'Editorial de Retrato y Moda Urbana', '5'),
('20', '4', 'uploads/home/Genesis_2.jpg', 'Editorial de Retrato y Moda Urbana', '6');

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE `blog_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'General',
  `author` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Manuel / Funktographer',
  `reading_time` int DEFAULT '3',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('published','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `views` int DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `published_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `blog_posts` VALUES
('1', 'Claves para una Cobertura Fotográfica de Alto Impacto en Eventos Corporativos', 'claves-cobertura-fotografica-eventos-corporativos', 'Descubre cómo una cobertura fotográfica profesional eleva el valor de marca, genera engagement en LinkedIn y documenta momentos clave en cumbres de negocios y congresos.', '<p>En el mundo empresarial contemporáneo, un evento corporativo no termina cuando se apagan las luces del auditorio. De hecho, es ahí donde comienza su verdadero retorno de inversión comunicacional. La <strong>fotografía corporativa de alto impacto</strong> es el activo digital que permite a directores, ponentes y asistentes revivir y compartir el valor generado en cumbres, seminarios y galas anuales.</p>\n<h2>1. Narrativa Visual: Más Allá de la Foto Protocolar</h2>\n<p>Durante años, el registro de eventos se limitó a fotos de apretones de manos y cortes de cinta. Hoy en día, las marcas líderes buscan una narrativa documental auténtica: la energía en los pasillos de networking, las expresiones de concentración ante una ponencia inspiradora y la interacción espontánea de los equipos de trabajo.</p>\n<blockquote>\"La fotografía corporativa moderna no solo documenta quién estuvo presente; comunica la trascendencia, innovación y liderazgo de la organización.\"</blockquote>\n<h2>2. Equipo Técnico y Dominio de Iluminación Difícil</h2>\n<p>Los centros de convenciones y hoteles plantean desafíos lumínicos extremos: pantallas LED de alta intensidad, iluminación ambiental tenue y focos de colores saturados. Contar con ópticas fijas luminosas (f/1.4 - f/1.8) y cámaras con alto rango dinámico permite captar imágenes nítidas, limpias y con tonos de piel naturales sin interrumpir la solemnidad del encuentro.</p>\n<h2>3. Flujo de Entrega Inmediata para Redes Sociales</h2>\n<p>En la era de LinkedIn y Twitter/X, el momento es ahora. Proporcionar una selección curada de 15 a 20 imágenes editadas en tiempo real durante el mismo evento permite a los equipos de comunicaciones y prensa publicar mientras la conversación aún es tendencia.</p>', 'uploads/home/Evento-Orsan-Funktographer-1.jpg', '1', 'Eventos Corporativos', 'Manuel / Funktographer', '4', '1', 'published', '144', 'Fotografía de Eventos Corporativos en Santiago — Claves y Estrategia', 'Guía completa sobre cómo capturar fotografía y video de alto impacto para eventos empresariales, cumbres y conferencias en Chile.', '2026-09-09 12:45:57', '2026-09-09 12:45:57', '2026-09-09 12:52:06'),
('2', 'El Arte de Fotografiar Gastronomía: Texturas, Colores y Appetizing Appeal', 'arte-fotografiar-gastronomia-texturas-colores', 'La fotografía gastronómica profesional es la puerta de entrada para seducir comensales. Analizamos la importancia de la luz lateral, los brillos y el estilismo culinario.', '<p>Comemos primero con los ojos. En la era digital, la decisión de un cliente para visitar un restaurante o pedir un plato comienza en la pantalla de su smartphone. Una imagen gastronómica profesional tiene la capacidad de transmitir temperatura, crocancia, aroma y sofisticación culinaria en una fracción de segundo.</p>\n<h2>La Magia de la Luz Lateral y Contraluz</h2>\n<p>En fotografía de alimentos, la iluminación frontal plana es el enemigo número uno. La luz rasante o lateral es la responsable de revelar el relieve de un pan crujiente, el brillo en una salsa reducida o la textura porosa de un queso de autor. El contraluz suave acentúa el vapor y los elementos translúcidos de las bebidas.</p>\n<h2>Dirección de Arte y Colaboración con el Chef</h2>\n<p>Cada plato tiene su momento óptimo de disparo: un helado dura segundos con su textura ideal; las hierbas frescas y microbrotes se marchitan bajo el calor ambiente; las carnes deben fotografiarse justo al salir de la parrilla para conservar sus jugos naturales. La sincronización milimétrica entre la cocina y la cámara es la clave del éxito.</p>\n<blockquote>\"Un buen bodegón culinario no solo muestra comida deliciosa; cuenta la historia del restaurante, el origen de los ingredientes y la pasión del autor.\"</blockquote>', 'uploads/home/Ramen-One-Fideos-Web4466.jpg', '2', 'Gastronomía y Culinaria', 'Manuel / Funktographer', '3', '0', 'published', '100', 'Fotografía Gastronómica en Santiago de Chile — Tips y Estilismo Culinario', 'Aprende los secretos detrás de la fotografía culinaria profesional: iluminación, textura y apetito visual para restaurantes y marcas gourmet.', '2026-09-09 12:45:57', '2026-09-09 12:45:57', '2026-09-09 13:01:07'),
('3', 'Detrás del Lente: Configuración de Equipo y Filosofía Visual de Funktographer', 'detras-del-lente-equipo-filosofia-visual-funktographer', 'Un recorrido íntimo por los cuerpos de cámara, ópticas y estabilizadores que utilizamos para capturar tanto la precisión corporativa como el dinamismo urbano.', '<p>La tecnología es una extensión de la mirada. Cada producción fotográfica o audiovisual requiere herramientas cuidadosamente seleccionadas para garantizar agilidad, discreción y la máxima fidelidad cromática posible.</p>\n<h2>La Versatilidad del Sistema Mirrorless</h2>\n<p>Trabajar con cuerpos compactos y silenciosos permite moverse con libertad absoluta dentro de salones ejecutivos sin perturbar a los expositores, o acercarse a escasos centímetros de una copa de cóctel sin invadir el espacio del barista.</p>\n<h2>Ópticas que Definen el Look</h2>\n<p>La combinación de focales fijas de 35mm y 85mm nos permite alternar con fluidez entre planos generales contextuales y retratos íntimos con un desenfoque cremoso de fondo (bokeh) que aísla al protagonista del bullicio exterior.</p>\n<blockquote>\"El mejor equipo no es el más grande ni el más pesado, sino aquel que te permite reaccionar a la velocidad de la emoción humana.\"</blockquote>', 'uploads/home/foto-camara-fujifilm-xt4-Funktographer-jpg.jpg', '5', 'Equipamiento', 'Manuel / Funktographer', '3', '0', 'published', '115', 'Equipo y Filosofía Visual — Detrás de Cámaras Funktographer', 'Conoce el equipo fotográfico, cámaras mirrorless y ópticas utilizadas por Manuel en Funktographer para producciones en Santiago.', '2026-09-09 12:45:57', '2026-09-09 12:45:57', '2026-09-09 12:45:57'),
('5', 'El Poder del Retrato Profesional: Cómo Potenciar tu Marca Personal y Presencia Ejecutiva', 'el-poder-del-retrato-profesional-marca-personal', 'En la era digital, tu fotografía de perfil es tu primer apretón de manos. Analizamos por qué un retrato profesional de estudio o locación genera confianza inmediata en clientes e inversores.', '<p>En un entorno empresarial cada vez más conectado, la primera impresión ya no ocurre en una sala de reuniones, sino en una pantalla: tu perfil de LinkedIn, la página de equipo de tu empresa o una nota de prensa corporativa. Un <strong>retrato profesional de alto impacto</strong> va mucho más allá de una foto nítida; es una declaración de autoridad, cercanía y visión de liderazgo.</p>\n<h2>1. El Retrato Corporativo Moderno: Adiós a la Rigidez</h2>\n<p>Atrás quedaron las fotografías acartonadas con fondos grises artificiales y sonrisas forzadas. Hoy, los directores, fundadores de startups y profesionales independientes buscan transmitir autenticidad y calidez humana. Trabajamos con iluminación natural controlada y fondos contextuales (oficinas contemporáneas, texturas urbanas o arquitectura sobria) que comunican dinamismo y sofisticación.</p>\n<blockquote>\"Un retrato auténtico no disfraza a la persona; revela su confianza y liderazgo en el momento exacto en que la cámara y la mirada se encuentran.\"</blockquote>\n<h2>2. Dirección de Expresión y Confort en Sesión</h2>\n<p>Para la mayoría de las personas, pararse frente a un lente profesional puede resultar intimidante. Nuestro rol como fotógrafos es guiar de manera fluida y distendida: desde la postura corporal y la orientación de los hombros hasta la respiración y la mirada. Una atmósfera relajada con música adecuada y conversación amena permite que surjan microexpresiones espontáneas de seguridad y empatía.</p>\n<h2>3. Coherencia Visual con la Identidad de Marca</h2>\n<p>No todos los líderes proyectan lo mismo: el fundador de una fintech tecnológica puede buscar un look vanguardista y creativo, mientras que un socio de un estudio jurídico requiere elegancia y solidez clásica. Adaptamos la paleta de colores, el contraste y la profundidad de campo para que cada retrato se alinee con la propuesta de valor de tu marca.</p>\n<div class=\"blog-cta-box\" style=\"background: rgba(255, 197, 1, 0.08); border: 1px solid rgba(255, 197, 1, 0.25); border-radius: 10px; padding: 18px 24px; margin: 28px 0;\">\n  <div>\n    <h4 style=\"color: #FFC501; margin: 0 0 6px 0; font-size: 1.15rem;\">¿Quieres renovar tu imagen profesional o la de tu equipo?</h4>\n    <p style=\"margin: 0; font-size: 0.9rem; color: #ddd;\">Realizamos sesiones de retrato corporativo en tu empresa o en locación en Santiago de Chile.</p>\n  </div>\n  <div style=\"margin-top: 10px;\">\n    <a href=\"https://wa.me/56947573794?text=Hola%20Manuel,%20leí%20tu%20artículo%20sobre%20Retratos%20Profesionales%20y%20me%20gustaría%20cotizar%20una%20sesión\" target=\"_blank\" style=\"background: #25d366; color: #000; font-weight: 700; padding: 10px 18px; border-radius: 30px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;\">\n      <i class=\"fab fa-whatsapp\"></i> Cotizar Sesión de Retratos\n    </a>\n  </div>\n</div>\n<h2>4. Consejos Clave para Preparar tu Sesión</h2>\n<ul>\n  <li><strong>Vestuario en tonos sólidos o neutros:</strong> Azules oscuros, grafito, tonos tierra y blancos funcionan de forma impecable; evita estampados muy saturados o patrones diminutos que generen efecto moiré.</li>\n  <li><strong>Cuidado de detalles:</strong> Planchado perfecto, accesorios sobrios y peinado natural para que el centro de atención sea tu mirada.</li>\n  <li><strong>Doble outfit:</strong> Te recomendamos contar con al menos dos opciones (una formal ejecutiva y otra más casual o smart-business) para tener versatilidad de uso en diferentes plataformas.</li>\n</ul>', 'uploads/home/Retrato-modelo-genesis-6-Funktographer.jpg', '3', 'Retratos y Moda', 'Manuel / Funktographer', '4', '0', 'published', '66', 'Retratos Profesionales y Marca Personal en Santiago — Funktographer', 'Descubre cómo un retrato profesional y corporativo de alta calidad transforma tu presencia en LinkedIn, directorios de liderazgo y medios de prensa.', '2026-09-09 13:03:53', '2026-09-09 13:03:53', '2026-09-09 13:04:58');

SET FOREIGN_KEY_CHECKS=1;
