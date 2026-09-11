<?php
require_once __DIR__ . '/auth.php';

$id = intval($_GET['id'] ?? 0);
$isEdit = ($id > 0);
$adminTitle = $isEdit ? "Editar Artículo del Blog" : "Redactar Nuevo Artículo";

$post = [
    'title'            => '',
    'slug'             => '',
    'excerpt'          => '',
    'content'          => '',
    'cover_image'      => '',
    'category_id'      => null,
    'category'         => 'Eventos Corporativos',
    'author'           => 'Manuel / Funktographer',
    'reading_time'     => 3,
    'is_featured'      => 0,
    'status'           => 'published',
    'meta_title'       => '',
    'meta_description' => '',
    'published_at'     => date('Y-m-d\TH:i')
];

// Fetch all active categories
$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order ASC, id ASC")->fetchAll();

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if (!$post) {
        set_flash('danger', 'El artículo solicitado no existe.');
        header("Location: " . BASE_URL . "/admin/posts");
        exit;
    }
    // Format published_at for datetime-local input
    if (!empty($post['published_at'])) {
        $post['published_at'] = date('Y-m-d\TH:i', strtotime($post['published_at']));
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Token CSRF inválido.');
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) $slug = slugify($title);
    
    $categoryName = trim($_POST['category'] ?? 'General');
    // Find category_id if matching slug or name
    $catId = null;
    foreach ($categories as $cat) {
        if ($cat['name'] === $categoryName || $cat['slug'] === $categoryName) {
            $catId = $cat['id'];
            $categoryName = $cat['name'];
            break;
        }
    }

    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author = trim($_POST['author'] ?? 'Manuel / Funktographer');
    $readingTime = max(1, intval($_POST['reading_time'] ?? 3));
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $status = in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'published';
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDesc = trim($_POST['meta_description'] ?? '');
    
    $publishedAtRaw = trim($_POST['published_at'] ?? '');
    $publishedAt = !empty($publishedAtRaw) ? date('Y-m-d H:i:s', strtotime($publishedAtRaw)) : date('Y-m-d H:i:s');

    if (empty($title)) {
        set_flash('danger', 'El título del artículo es obligatorio.');
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Check slug uniqueness
    $slugCheckSql = "SELECT id FROM blog_posts WHERE slug = ?" . ($isEdit ? " AND id != ?" : "");
    $slugStmt = $pdo->prepare($slugCheckSql);
    if ($isEdit) {
        $slugStmt->execute([$slug, $id]);
    } else {
        $slugStmt->execute([$slug]);
    }
    if ($slugStmt->fetch()) {
        $slug .= '-' . time();
    }

    // Handle Cover Photo Upload
    $coverImage = $post['cover_image'] ?? '';
    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = upload_image($_FILES['cover_photo'], 'blog');
        if ($upload['success']) {
            $coverImage = $upload['url'];
        } else {
            set_flash('danger', 'Error al subir la portada: ' . $upload['error']);
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    if (empty($coverImage)) {
        // Fallback default image if none uploaded and creating
        $coverImage = 'assets/img/logo.png';
    }

    if ($isEdit) {
        $stmt = $pdo->prepare("
            UPDATE blog_posts SET
                title = :title,
                slug = :slug,
                excerpt = :excerpt,
                content = :content,
                cover_image = :cover_image,
                category_id = :category_id,
                category = :category,
                author = :author,
                reading_time = :reading_time,
                is_featured = :is_featured,
                status = :status,
                meta_title = :meta_title,
                meta_description = :meta_description,
                published_at = :published_at
            WHERE id = :id
        ");
        $stmt->execute([
            ':title'            => $title,
            ':slug'             => $slug,
            ':excerpt'          => $excerpt,
            ':content'          => $content,
            ':cover_image'      => $coverImage,
            ':category_id'      => $catId,
            ':category'         => $categoryName,
            ':author'           => $author,
            ':reading_time'     => $readingTime,
            ':is_featured'      => $isFeatured,
            ':status'           => $status,
            ':meta_title'       => $metaTitle,
            ':meta_description' => $metaDesc,
            ':published_at'     => $publishedAt,
            ':id'               => $id
        ]);
        set_flash('success', '¡Artículo actualizado correctamente!');
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO blog_posts (
                title, slug, excerpt, content, cover_image, category_id, category,
                author, reading_time, is_featured, status, meta_title, meta_description, published_at
            ) VALUES (
                :title, :slug, :excerpt, :content, :cover_image, :category_id, :category,
                :author, :reading_time, :is_featured, :status, :meta_title, :meta_description, :published_at
            )
        ");
        $stmt->execute([
            ':title'            => $title,
            ':slug'             => $slug,
            ':excerpt'          => $excerpt,
            ':content'          => $content,
            ':cover_image'      => $coverImage,
            ':category_id'      => $catId,
            ':category'         => $categoryName,
            ':author'           => $author,
            ':reading_time'     => $readingTime,
            ':is_featured'      => $isFeatured,
            ':status'           => $status,
            ':meta_title'       => $metaTitle,
            ':meta_description' => $metaDesc,
            ':published_at'     => $publishedAt
        ]);
        $id = $pdo->lastInsertId();
        set_flash('success', '¡Artículo publicado con éxito en el Blog!');
    }

    header("Location: " . BASE_URL . "/admin/posts");
    exit;
}

require_once __DIR__ . '/header.php';
?>

<div class="adm-header">
  <div>
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
      <a href="<?= BASE_URL ?>/admin/posts" style="color: var(--adm-muted); text-decoration: none; font-size: 0.9rem;">
        <i class="fas fa-arrow-left"></i> Volver a Artículos
      </a>
      <span style="color: var(--adm-border);">/</span>
      <span style="color: var(--adm-primary); font-size: 0.9rem; font-weight: 600;">
        <?= $isEdit ? 'Editar #' . $id : 'Nuevo Post' ?>
      </span>
    </div>
    <h1 class="adm-title"><?= $isEdit ? 'Editar Artículo' : 'Redactar Nuevo Artículo' ?></h1>
  </div>
  <div style="display: flex; gap: 10px;">
    <?php if ($isEdit): ?>
      <a href="<?= BASE_URL ?>/blog/<?= urlencode($post['slug']) ?>" target="_blank" class="adm-btn adm-btn-secondary">
        <i class="fas fa-external-link-alt"></i> Ver en la Web
      </a>
    <?php endif; ?>
    <button type="button" class="adm-btn adm-btn-primary" onclick="document.getElementById('postForm').submit();">
      <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar Cambios' : 'Publicar Artículo' ?>
    </button>
  </div>
</div>

<form method="POST" action="<?= $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data" id="postForm">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

  <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 26px; align-items: flex-start;">
    
    <!-- LEFT MAIN COLUMN: Editorial Content -->
    <div>
      <div class="adm-card">
        <h3 class="adm-card-title" style="margin-bottom: 20px;">
          <i class="fas fa-pen-nib" style="color: var(--adm-primary); margin-right: 8px;"></i>
          Contenido Editorial
        </h3>

        <!-- Title -->
        <div class="adm-form-group">
          <label class="adm-form-label" for="postTitle">Título del Artículo *</label>
          <input type="text" class="adm-input" id="postTitle" name="title" value="<?= htmlspecialchars($post['title']) ?>" placeholder="Ej: 5 Claves para Lograr Retratos Corporativos Impactantes" style="font-size: 1.15rem; font-weight: 600;" required>
        </div>

        <!-- Slug -->
        <div class="adm-form-group">
          <label class="adm-form-label" for="postSlug">
            URL Limpia (Slug)
            <span style="font-size: 0.8rem; font-weight: normal; color: var(--adm-muted); margin-left: 8px;">/blog/{slug}</span>
          </label>
          <input type="text" class="adm-input" id="postSlug" name="slug" value="<?= htmlspecialchars($post['slug']) ?>" placeholder="ej: 5-claves-retratos-corporativos-impactantes">
        </div>

        <!-- Excerpt -->
        <div class="adm-form-group">
          <label class="adm-form-label" for="postExcerpt">
            Extracto / Resumen Introductorio
            <span style="font-size: 0.8rem; font-weight: normal; color: var(--adm-muted); margin-left: 8px;">(Se muestra en las tarjetas y vista previa)</span>
          </label>
          <textarea class="adm-textarea" id="postExcerpt" name="excerpt" rows="3" placeholder="Breve descripción que motive al lector a hacer clic..."><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
        </div>

        <!-- Rich Content Editor -->
        <div class="adm-form-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label class="adm-form-label" style="margin-bottom: 0;">Cuerpo del Artículo *</label>
            <div style="display: flex; gap: 8px; align-items: center;">
              <span id="wordCountLabel" style="font-size: 0.8rem; color: var(--adm-muted);">0 palabras</span>
              <button type="button" class="adm-btn adm-btn-secondary" id="toggleHtmlMode" style="padding: 4px 10px; font-size: 0.75rem;">
                <i class="fas fa-code"></i> HTML
              </button>
            </div>
          </div>

          <!-- Editor Toolbar -->
          <div class="blog-editor-toolbar" id="editorToolbar">
            <button type="button" class="tool-btn" data-cmd="formatBlock" data-val="h2" title="Encabezado 2"><strong>H2</strong></button>
            <button type="button" class="tool-btn" data-cmd="formatBlock" data-val="h3" title="Encabezado 3"><strong>H3</strong></button>
            <button type="button" class="tool-btn" data-cmd="formatBlock" data-val="p" title="Párrafo normal">P</button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn" data-cmd="bold" title="Negrita"><i class="fas fa-bold"></i></button>
            <button type="button" class="tool-btn" data-cmd="italic" title="Cursiva"><i class="fas fa-italic"></i></button>
            <button type="button" class="tool-btn" data-cmd="formatBlock" data-val="blockquote" title="Cita destacada"><i class="fas fa-quote-left"></i></button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn" data-cmd="insertUnorderedList" title="Lista de viñetas"><i class="fas fa-list-ul"></i></button>
            <button type="button" class="tool-btn" data-cmd="insertOrderedList" title="Lista numerada"><i class="fas fa-list-ol"></i></button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn" id="btnInsertLink" title="Insertar Enlace"><i class="fas fa-link"></i></button>
            <button type="button" class="tool-btn" id="btnInsertImage" title="Insertar Imagen por URL"><i class="fas fa-image"></i></button>
            <button type="button" class="tool-btn" id="btnInsertVideo" title="Insertar Video de YouTube"><i class="fab fa-youtube"></i></button>
            <button type="button" class="tool-btn" id="btnInsertCta" title="Insertar Botón de WhatsApp"><i class="fab fa-whatsapp" style="color: #25d366;"></i> CTA</button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn" data-cmd="removeFormat" title="Limpiar Formato"><i class="fas fa-eraser"></i></button>
          </div>

          <!-- Visual Contenteditable Canvas -->
          <div id="visualEditor" class="blog-visual-editor" contenteditable="true" spellcheck="false"><?= $post['content'] ?></div>

          <!-- Hidden Textarea for Form POST -->
          <textarea id="hiddenContent" name="content" style="display: none;"><?= htmlspecialchars($post['content']) ?></textarea>
        </div>
      </div>

      <!-- Tarjeta de SEO y Meta Tags -->
      <div class="adm-card" style="margin-top: 24px;">
        <h3 class="adm-card-title" style="margin-bottom: 16px;">
          <i class="fab fa-google" style="color: var(--adm-primary); margin-right: 8px;"></i>
          Optimización SEO para Google
        </h3>
        <p style="font-size: 0.85rem; color: var(--adm-muted); margin-bottom: 18px;">
          Configura cómo aparecerá este artículo en los resultados de búsqueda de Google para atraer clientes orgánicos.
        </p>

        <div class="adm-form-group">
          <div style="display: flex; justify-content: space-between;">
            <label class="adm-form-label" for="metaTitle">Meta Título (Google)</label>
            <span id="metaTitleCount" style="font-size: 0.75rem; color: var(--adm-muted);">0 / 60 caracteres recomendados</span>
          </div>
          <input type="text" class="adm-input" id="metaTitle" name="meta_title" value="<?= htmlspecialchars($post['meta_title'] ?? '') ?>" placeholder="Ej: Fotografía Corporativa en Santiago | Guía y Consejos Funktographer">
        </div>

        <div class="adm-form-group">
          <div style="display: flex; justify-content: space-between;">
            <label class="adm-form-label" for="metaDesc">Meta Descripción (Google Snippet)</label>
            <span id="metaDescCount" style="font-size: 0.75rem; color: var(--adm-muted);">0 / 155 caracteres recomendados</span>
          </div>
          <textarea class="adm-textarea" id="metaDesc" name="meta_description" rows="2" placeholder="Resumen persuasivo que invite al usuario a hacer clic desde el buscador..."><?= htmlspecialchars($post['meta_description'] ?? '') ?></textarea>
        </div>

        <!-- Google SERP Live Preview -->
        <div style="background: #1e1f20; border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 16px; margin-top: 16px;">
          <div style="font-size: 0.75rem; color: var(--adm-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 8px;">
            Vista previa en Google
          </div>
          <div style="font-size: 0.8rem; color: #9aa0a6;">
            funktographer.cl &rsaquo; blog &rsaquo; <span id="serpSlugPreview"><?= htmlspecialchars($post['slug'] ?: 'articulo') ?></span>
          </div>
          <div id="serpTitlePreview" style="color: #8ab4f8; font-size: 1.1rem; font-weight: 500; margin: 4px 0; line-height: 1.3;">
            <?= htmlspecialchars($post['meta_title'] ?: ($post['title'] ?: 'Título del Artículo — Funktographer')) ?>
          </div>
          <div id="serpDescPreview" style="color: #bdc1c6; font-size: 0.85rem; line-height: 1.4;">
            <?= htmlspecialchars($post['meta_description'] ?: ($post['excerpt'] ?: 'Extracto de la publicación que se mostrará en los resultados de Google...')) ?>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDEBAR COLUMN: Publishing, Category, Cover Image -->
    <div>
      <!-- Publishing Card -->
      <div class="adm-card">
        <h3 class="adm-card-title" style="margin-bottom: 20px;">
          <i class="fas fa-paper-plane" style="color: var(--adm-primary); margin-right: 8px;"></i>
          Publicación y Parámetros
        </h3>

        <!-- Status -->
        <div class="adm-form-group">
          <label class="adm-form-label" for="postStatus">Estado de Visibilidad</label>
          <select class="adm-select" id="postStatus" name="status">
            <option value="published" <?= ($post['status'] === 'published') ? 'selected' : '' ?>>🟢 Publicado (Visible en web)</option>
            <option value="draft" <?= ($post['status'] === 'draft') ? 'selected' : '' ?>>⚪ Borrador (Solo visible en admin)</option>
          </select>
        </div>

        <!-- Featured Switch -->
        <div class="adm-form-group" style="background: rgba(255, 197, 1, 0.06); border: 1px solid rgba(255, 197, 1, 0.2); border-radius: 8px; padding: 12px 16px;">
          <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
            <input type="checkbox" name="is_featured" value="1" <?= $post['is_featured'] ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--adm-primary);">
            <div>
              <span style="font-weight: 700; color: #FFC501;">Artículo Destacado</span>
              <div style="font-size: 0.75rem; color: var(--adm-muted); margin-top: 2px;">Se mostrará como portada principal en la cabecera del Blog.</div>
            </div>
          </label>
        </div>

        <!-- Category Selector with (+) Modal Button -->
        <div class="adm-form-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label class="adm-form-label" for="categorySelect" style="margin-bottom: 0;">Categoría *</label>
            <button type="button" class="btn-open-cat-modal" id="btnOpenCatModal" title="Crear Nueva Categoría al Vuelo">
              <i class="fas fa-plus"></i>
            </button>
          </div>
          <select class="adm-select" id="categorySelect" name="category" required>
            <?php 
            $foundCurrentCat = false;
            foreach ($categories as $cat): 
              $isSelected = ($post['category'] === $cat['name'] || $post['category'] === $cat['slug']);
              if ($isSelected) $foundCurrentCat = true;
            ?>
              <option value="<?= htmlspecialchars($cat['name']) ?>" <?= $isSelected ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
            <?php if (!empty($post['category']) && !$foundCurrentCat): ?>
              <option value="<?= htmlspecialchars($post['category']) ?>" selected>
                <?= htmlspecialchars($post['category']) ?>
              </option>
            <?php endif; ?>
          </select>
        </div>

        <!-- Author -->
        <div class="adm-form-group">
          <label class="adm-form-label" for="postAuthor">Autor</label>
          <input type="text" class="adm-input" id="postAuthor" name="author" value="<?= htmlspecialchars($post['author'] ?? 'Manuel / Funktographer') ?>">
        </div>

        <!-- Reading Time -->
        <div class="adm-form-group">
          <div style="display: flex; justify-content: space-between;">
            <label class="adm-form-label" for="readingTime">Tiempo de Lectura (minutos)</label>
            <span style="font-size: 0.75rem; color: var(--adm-primary);"><i class="fas fa-magic"></i> Auto-estimado</span>
          </div>
          <input type="number" class="adm-input" id="readingTime" name="reading_time" value="<?= intval($post['reading_time'] ?? 3) ?>" min="1" max="60">
        </div>

        <!-- Published Date -->
        <div class="adm-form-group">
          <label class="adm-form-label" for="publishedAt">Fecha de Publicación</label>
          <input type="datetime-local" class="adm-input" id="publishedAt" name="published_at" value="<?= htmlspecialchars($post['published_at']) ?>">
        </div>

        <button type="submit" class="adm-btn adm-btn-primary" style="width: 100%; justify-content: center; padding: 14px; font-size: 1rem; margin-top: 10px;">
          <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar Cambios' : 'Publicar Ahora' ?>
        </button>
      </div>

      <!-- Cover Photo Card -->
      <div class="adm-card" style="margin-top: 24px;">
        <h3 class="adm-card-title" style="margin-bottom: 16px;">
          <i class="fas fa-image" style="color: var(--adm-primary); margin-right: 8px;"></i>
          Foto de Portada
        </h3>

        <!-- Dropzone Box -->
        <div class="adm-dropzone" id="coverDropzone" style="position: relative; overflow: hidden; min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px dashed var(--adm-border); border-radius: 10px; background: rgba(255,255,255,0.02); text-align: center; padding: 20px; transition: border-color 0.2s;">
          
          <?php if (!empty($post['cover_image'])): ?>
            <div id="coverPreviewContainer" style="margin-bottom: 12px; width: 100%;">
              <img src="<?= BASE_URL ?>/<?= htmlspecialchars($post['cover_image']) ?>" alt="Portada" id="coverPreviewImg" style="width: 100%; height: 160px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
              <div style="font-size: 0.75rem; color: var(--adm-muted); margin-top: 6px;">Imagen actual. Arrastra o selecciona otra para cambiarla.</div>
            </div>
          <?php else: ?>
            <div id="coverPreviewContainer" style="display: none; margin-bottom: 12px; width: 100%;">
              <img src="" alt="Previsualización" id="coverPreviewImg" style="width: 100%; height: 160px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
            </div>
          <?php endif; ?>

          <div id="dropzonePrompt">
            <i class="fas fa-cloud-upload-alt" style="font-size: 2.2rem; color: var(--adm-primary); margin-bottom: 10px;"></i>
            <div style="font-weight: 600; font-size: 0.95rem;">Seleccionar o arrastrar foto</div>
            <div style="font-size: 0.78rem; color: var(--adm-muted); margin-top: 4px;">JPG, PNG, WEBP (Recomendado: 16:9, min 1200px)</div>
          </div>

          <!-- Absolute file input covering the dropzone -->
          <input type="file" name="cover_photo" id="coverPhotoInput" accept="image/jpeg,image/png,image/webp,image/avif" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10;">
        </div>
      </div>

    </div>
  </div>
</form>

<!-- Modal Unificado de Gestión de Categorías (Crear, Editar, Eliminar) -->
<?php 
$categorySelectId = 'categorySelect'; 
require_once __DIR__ . '/modal-category.php'; 
?>

<style>
/* Rich Editor Styles */
.blog-editor-toolbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 4px;
  background: #1a1a24;
  border: 1px solid var(--adm-border);
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
  padding: 8px 10px;
}
.blog-editor-toolbar .tool-btn {
  background: transparent;
  border: 1px solid transparent;
  color: #ddd;
  border-radius: 6px;
  padding: 6px 10px;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.15s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.blog-editor-toolbar .tool-btn:hover {
  background: rgba(255, 197, 1, 0.15);
  color: #FFC501;
  border-color: rgba(255, 197, 1, 0.3);
}
.blog-editor-toolbar .tool-sep {
  width: 1px;
  height: 20px;
  background: var(--adm-border);
  margin: 0 4px;
}
.blog-visual-editor {
  min-height: 420px;
  background: #111118;
  border: 1px solid var(--adm-border);
  border-top: none;
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;
  padding: 20px;
  color: #eee;
  font-family: inherit;
  font-size: 1rem;
  line-height: 1.7;
  outline: none;
  overflow-y: auto;
}
.blog-visual-editor:focus {
  border-color: var(--adm-primary);
}
.blog-visual-editor h2 {
  font-size: 1.5rem;
  color: #fff;
  margin: 24px 0 12px 0;
  border-left: 3px solid var(--adm-primary);
  padding-left: 12px;
}
.blog-visual-editor h3 {
  font-size: 1.25rem;
  color: #FFC501;
  margin: 20px 0 10px 0;
}
.blog-visual-editor p {
  margin-bottom: 16px;
}
.blog-visual-editor blockquote {
  border-left: 4px solid var(--adm-primary);
  margin: 20px 0;
  padding: 12px 20px;
  background: rgba(121, 56, 226, 0.08);
  font-style: italic;
  color: #e0d0ff;
  border-radius: 0 8px 8px 0;
}
.blog-visual-editor img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 16px 0;
}
.blog-visual-editor .blog-video-wrap {
  position: relative;
  padding-bottom: 56.25%;
  height: 0;
  overflow: hidden;
  margin: 24px 0;
  border-radius: 8px;
}
.blog-visual-editor .blog-video-wrap iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border: 0;
}
.blog-visual-editor .blog-cta-box {
  background: rgba(255, 197, 1, 0.08);
  border: 1px solid rgba(255, 197, 1, 0.25);
  border-radius: 10px;
  padding: 16px 20px;
  margin: 24px 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Elements
  const postTitle = document.getElementById('postTitle');
  const postSlug = document.getElementById('postSlug');
  const postExcerpt = document.getElementById('postExcerpt');
  const visualEditor = document.getElementById('visualEditor');
  const hiddenContent = document.getElementById('hiddenContent');
  const postForm = document.getElementById('postForm');
  const wordCountLabel = document.getElementById('wordCountLabel');
  const readingTimeInput = document.getElementById('readingTime');
  const toggleHtmlBtn = document.getElementById('toggleHtmlMode');

  // SEO Elements
  const metaTitle = document.getElementById('metaTitle');
  const metaDesc = document.getElementById('metaDesc');
  const metaTitleCount = document.getElementById('metaTitleCount');
  const metaDescCount = document.getElementById('metaDescCount');
  const serpTitlePreview = document.getElementById('serpTitlePreview');
  const serpDescPreview = document.getElementById('serpDescPreview');
  const serpSlugPreview = document.getElementById('serpSlugPreview');

  // Dropzone Cover Elements
  const coverInput = document.getElementById('coverPhotoInput');
  const coverPreviewContainer = document.getElementById('coverPreviewContainer');
  const coverPreviewImg = document.getElementById('coverPreviewImg');
  const dropzonePrompt = document.getElementById('dropzonePrompt');

  // Auto-slugify title if slug is empty or matches slugified old title
  let userEditedSlug = <?= !empty($post['slug']) ? 'true' : 'false' ?>;
  postSlug.addEventListener('input', () => {
    userEditedSlug = true;
    serpSlugPreview.textContent = postSlug.value || 'articulo';
  });

  postTitle.addEventListener('input', () => {
    if (!userEditedSlug) {
      const slugVal = postTitle.value
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
      postSlug.value = slugVal;
      serpSlugPreview.textContent = slugVal || 'articulo';
    }
    updateSerpPreview();
  });

  // SEO Count and SERP Preview
  function updateSerpPreview() {
    const tVal = metaTitle.value.trim() || postTitle.value.trim() || 'Título del Artículo — Funktographer';
    const dVal = metaDesc.value.trim() || postExcerpt.value.trim() || 'Extracto de la publicación que se mostrará en los resultados de Google...';

    serpTitlePreview.textContent = tVal;
    serpDescPreview.textContent = dVal;
    metaTitleCount.textContent = `${metaTitle.value.length} / 60 caracteres`;
    metaDescCount.textContent = `${metaDesc.value.length} / 155 caracteres`;
  }

  metaTitle.addEventListener('input', updateSerpPreview);
  metaDesc.addEventListener('input', updateSerpPreview);
  postExcerpt.addEventListener('input', updateSerpPreview);
  updateSerpPreview();

  // Cover Image Preview
  coverInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (re) => {
        coverPreviewImg.src = re.target.result;
        coverPreviewContainer.style.display = 'block';
        dropzonePrompt.innerHTML = `<div style="color: #34d399; font-weight: 600;"><i class="fas fa-check-circle"></i> Imagen lista para subir: ${file.name}</div><div style="font-size: 0.75rem; color: var(--adm-muted); margin-top: 4px;">Haz clic o arrastra otra para reemplazarla.</div>`;
      };
      reader.readAsDataURL(file);
    }
  });

  // Calculate words and reading time
  function updateWordCount() {
    const text = visualEditor.innerText || '';
    const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
    wordCountLabel.textContent = `${words} palabras`;
    if (words > 0) {
      const estimatedMin = Math.max(1, Math.round(words / 180));
      readingTimeInput.value = estimatedMin;
    }
  }

  visualEditor.addEventListener('input', updateWordCount);
  updateWordCount();

  // Rich Toolbar Execution
  document.querySelectorAll('#editorToolbar .tool-btn[data-cmd]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const cmd = btn.getAttribute('data-cmd');
      const val = btn.getAttribute('data-val') || null;
      document.execCommand(cmd, false, val);
      visualEditor.focus();
      updateWordCount();
    });
  });

  // Insert Link
  document.getElementById('btnInsertLink').addEventListener('click', (e) => {
    e.preventDefault();
    const url = prompt('Ingresa la URL del enlace (ej: https://ejemplo.com):');
    if (url) {
      document.execCommand('createLink', false, url);
      visualEditor.focus();
    }
  });

  // Insert Image URL
  document.getElementById('btnInsertImage').addEventListener('click', (e) => {
    e.preventDefault();
    const imgUrl = prompt('Ingresa la URL de la imagen:');
    if (imgUrl) {
      document.execCommand('insertImage', false, imgUrl);
      visualEditor.focus();
    }
  });

  // Insert YouTube Video Embed
  document.getElementById('btnInsertVideo').addEventListener('click', (e) => {
    e.preventDefault();
    const ytUrl = prompt('Ingresa la URL del video de YouTube (ej: https://www.youtube.com/watch?v=...):');
    if (ytUrl) {
      let videoId = '';
      const match = ytUrl.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
      if (match && match[1]) {
        videoId = match[1];
        const embedHtml = `<div class="blog-video-wrap"><iframe src="https://www.youtube-nocookie.com/embed/${videoId}" allowfullscreen></iframe></div><p><br></p>`;
        document.execCommand('insertHTML', false, embedHtml);
        visualEditor.focus();
      } else {
        alert('URL de YouTube no válida.');
      }
    }
  });

  // Insert WhatsApp CTA
  document.getElementById('btnInsertCta').addEventListener('click', (e) => {
    e.preventDefault();
    const ctaHtml = `
      <div class="blog-cta-box" style="background: rgba(255, 197, 1, 0.08); border: 1px solid rgba(255, 197, 1, 0.25); border-radius: 10px; padding: 18px 24px; margin: 28px 0;">
        <div>
          <h4 style="color: #FFC501; margin: 0 0 6px 0; font-size: 1.15rem;">¿Necesitas una producción fotográfica o audiovisual?</h4>
          <p style="margin: 0; font-size: 0.9rem; color: #ddd;">Conversa directamente con Manuel y cotiza tu evento o sesión en Santiago.</p>
        </div>
        <div style="margin-top: 10px;">
          <a href="https://wa.me/56947573794?text=Hola%20Manuel,%20leí%20tu%20artículo%20en%20el%20blog%20y%20me%20gustaría%20cotizar" target="_blank" style="background: #25d366; color: #000; font-weight: 700; padding: 10px 18px; border-radius: 30px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fab fa-whatsapp"></i> Conversar por WhatsApp
          </a>
        </div>
      </div>
      <p><br></p>
    `;
    document.execCommand('insertHTML', false, ctaHtml);
    visualEditor.focus();
  });

  // Toggle HTML View
  let isHtmlMode = false;
  toggleHtmlBtn.addEventListener('click', () => {
    if (!isHtmlMode) {
      // Switch to HTML mode
      visualEditor.innerText = visualEditor.innerHTML;
      toggleHtmlBtn.innerHTML = '<i class="fas fa-eye"></i> Visual';
      toggleHtmlBtn.style.color = 'var(--adm-primary)';
      isHtmlMode = true;
    } else {
      // Switch back to Visual mode
      visualEditor.innerHTML = visualEditor.innerText;
      toggleHtmlBtn.innerHTML = '<i class="fas fa-code"></i> HTML';
      toggleHtmlBtn.style.color = '';
      isHtmlMode = false;
    }
  });

  // Form Submit: Sync Content
  postForm.addEventListener('submit', () => {
    if (isHtmlMode) {
      hiddenContent.value = visualEditor.innerText;
    } else {
      hiddenContent.value = visualEditor.innerHTML;
    }
  });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
