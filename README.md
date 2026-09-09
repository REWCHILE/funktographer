# Funktographer — Fotografía y Video Profesional

Sitio web y panel de administración para **Funktographer**, estudio de fotografía corporativa, gastronómica, retratos y producción audiovisual en Santiago de Chile.

Desarrollado en PHP nativo modular y de alto rendimiento, con URLs limpias sin extensiones `.php`, base de datos MySQL, panel de control administrativo y diseño de vanguardia.

---

## 🚀 Características Principales

### 1. Frontend de Vanguardia
* **Hero Cinematográfico:** Video de fondo optimizado con superposición y acentos dorados (`#FFC501`) y morados (`#7938e2`).
* **Portafolio Dinámico (`/proyectos`):** Catálogo de proyectos con filtros de píldoras dinámicas administrables.
* **Páginas de Detalle (`/proyecto/{slug}`):** Ficha técnica, historia visual, galería en alta resolución con lightbox interactivo, sección secundaria de notas y video embebido (YouTube o subida directa optimizada con FFmpeg).
* **Bio Links Hub Oficial (`/links` o `/bio`):** Alternativa propia y de vanguardia a Linktree con identidad de marca, botones con corte geométrico (*chamfered*), tarjeta de Contacto & Cobertura con pulso *beacon* y feed dinámico de últimos proyectos.
* **Módulo de Blog & Revista Editorial (`/blog` y `/blog/{slug}`):**
  * Vitrina horizontal para artículo destacado.
  * Buscador en vivo por palabras clave.
  * Píldoras de filtrado por categoría.
  * Barra superior dorada de progreso de lectura (*reading progress bar*).
  * Caja de autor con biografía de Manuel y conversión directa a WhatsApp con mensaje personalizado.
  * Botones para compartir en WhatsApp, LinkedIn, X y copiado de enlace.
* **Secciones Institucionales:** `/nosotros` y formulario de `/contacto` conectado a base de datos.

### 2. Panel de Administración (`/admin`)
* **Dashboard:** Métricas en tiempo real de fotos, proyectos, enlaces de bio, categorías, mensajes y artículos.
* **Gestor de Bio Links (`/admin/bio-links`):** CRUD completo para los enlaces del hub con ordenamiento, selector de iconos FontAwesome, estilos visuales e interruptor de visibilidad.
* **Gestor de Fotos del Home (`/admin/home-images`):** Zona drag & drop para subir fotos a la portada, toggle de visibilidad instantáneo y eliminación vía AJAX.
* **Gestor de Proyectos (`/admin/projects` y `/admin/project-form`):** Creación y edición con portada, galerías secundarias, video de YouTube o procesamiento de video directo, y asignación de orden y destacado.
* **Gestor de Artículos de Blog (`/admin/posts` y `/admin/post-form`):** Editor enriquecido (H2, H3, citas, listas, imágenes, videos de YouTube, botones CTA de WhatsApp), cálculo automático de tiempo de lectura y módulo SEO con previsualización SERP en vivo de Google.
* **Sistema Dinámico de Categorías:**
  * Modal rápido `(+)` integrado en todos los formularios para **Crear, Editar nombres, reordenar píldoras o Eliminar categorías en la misma pestaña** vía AJAX sin recargar ni perder datos.
  * Administrador general en `/admin/categories`.
* **Mensajes de Contacto (`/admin/messages`):** Bandeja de entrada con contador de no leídos.
* **Ajustes Generales (`/admin/settings`):** Configuración de WhatsApp, teléfono, correo, Instagram y cobertura territorial.

---

## 🛠️ Requisitos e Instalación

### Requisitos
* **PHP:** 8.0 o superior (con extensiones `pdo_mysql`, `fileinfo`, `mbstring`).
* **MySQL / MariaDB:** 5.7 o superior.
* **Servidor Web:** Apache (con `mod_rewrite`) o PHP Built-in Server.

### Instalación Rápida
1. Clonar el repositorio:
   ```bash
   git clone https://github.com/REWCHILE/funktographer.git
   cd funktographer
   ```
2. Crear la base de datos e importar la estructura y datos:
   ```sql
   CREATE DATABASE `funktographer_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
   Importar el archivo `database/dump.sql` (o `database/schema.sql`).
3. Configurar conexión en `includes/config.php` (por defecto conecta a `127.0.0.1:3306`, usuario `root`, sin contraseña).
4. Iniciar servidor local:
   ```bash
   php -S 127.0.0.1:8088 router.php
   ```

### Credenciales de Administrador
* **URL de Acceso:** `http://127.0.0.1:8088/admin/login`
* **Usuario:** `admin`
* **Contraseña:** `admin1234`

---

## 📁 Estructura del Proyecto

```
funktographer/
├── admin/                  # Panel de administración y endpoints AJAX
│   ├── index.php           # Dashboard
│   ├── bio-links.php       # Gestor de Bio Links Hub
│   ├── home-images.php     # Fotos de portada
│   ├── projects.php        # Listado de proyectos
│   ├── project-form.php    # Editor de proyectos
│   ├── categories.php      # Gestor de categorías y píldoras
│   ├── posts.php           # Listado de artículos de blog
│   ├── post-form.php       # Editor enriquecido de blog
│   ├── modal-category.php  # Modal AJAX de categorías en misma pestaña
│   ├── ajax-category.php   # API AJAX para CRUD de categorías
│   └── ...
├── assets/                 # Hojas de estilo CSS, JS, fuentes, imágenes y video
│   ├── css/style.css       # Estilos globales y magazine
│   ├── css/admin.css       # Estilos del panel de control
│   ├── css/links.css       # Estilos del Bio Links Hub
│   └── js/                 # Scripts interactivos
├── database/
│   ├── schema.sql          # Estructura de tablas MySQL
│   └── dump.sql            # Volcado completo con datos iniciales
├── includes/               # Configuración, conexión PDO y funciones helper
├── uploads/                # Archivos subidos (fotos, galerías, blog)
├── blog.php                # Vista pública del Blog (/blog)
├── blog-post.php           # Vista pública de lectura (/blog/{slug})
├── links.php               # Bio Links Hub (/links y /bio)
├── proyecto.php            # Detalle de proyecto (/proyecto/{slug})
├── proyectos.php           # Portafolio (/proyectos)
├── router.php              # Enrutador para servidor de desarrollo PHP
└── .htaccess               # Reglas mod_rewrite para Apache en producción
```

---

## 🎨 Paleta de Marca
* **Color Primario:** `#FFC501` (Dorado vibrante Funktographer)
* **Acento:** `#7938e2` (Púrpura)
* **Fondo Oscuro:** `#09090c` / `#111118`

---

&copy; Funktographer. Todos los derechos reservados.
