<?php
require_once __DIR__ . '/auth.php';

$id = intval($_GET['id'] ?? 0);
$isEdit = ($id > 0);
$adminTitle = $isEdit ? "Editar Proyecto" : "Nuevo Proyecto";

$project = [
    'title'         => '',
    'slug'          => '',
    'category'      => 'Eventos',
    'client'        => '',
    'event_date'    => '',
    'description'   => '',
    'extra_title'   => '',
    'extra_content' => '',
    'video_type'    => 'none',
    'video_url'     => '',
    'video_file'    => '',
    'cover_image'   => '',
    'is_featured'   => 0,
    'display_order' => 1
];

$galleryImages = [];

// Fetch all active categories
$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order ASC, id ASC")->fetchAll();

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if (!$project) {
        set_flash('danger', 'El proyecto solicitado no existe.');
        header("Location: " . BASE_URL . "/admin/projects");
        exit;
    }

    // Fetch existing gallery photos
    $stmtGallery = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY display_order ASC");
    $stmtGallery->execute([$id]);
    $galleryImages = $stmtGallery->fetchAll();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Token CSRF inválido.');
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Sub-action: delete single gallery photo
    if (($_POST['sub_action'] ?? '') === 'delete_photo') {
        $photoId = intval($_POST['photo_id'] ?? 0);
        $stmtP = $pdo->prepare("SELECT image_url FROM project_images WHERE id = ? AND project_id = ?");
        $stmtP->execute([$photoId, $id]);
        $photoRow = $stmtP->fetch();
        if ($photoRow) {
            if (str_starts_with($photoRow['image_url'], 'uploads/')) {
                @unlink(ROOT_PATH . '/' . $photoRow['image_url']);
            }
            $pdo->prepare("DELETE FROM project_images WHERE id = ?")->execute([$photoId]);
            set_flash('success', 'Foto de galería eliminada.');
        }
        header("Location: " . BASE_URL . "/admin/project-form?id=" . $id);
        exit;
    }

    // Save project data
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) $slug = slugify($title);
    $category = trim($_POST['category'] ?? 'Eventos');
    $client = trim($_POST['client'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $extraTitle = trim($_POST['extra_title'] ?? '');
    $extraContent = trim($_POST['extra_content'] ?? '');
    $videoType = in_array($_POST['video_type'] ?? '', ['none', 'youtube', 'upload']) ? $_POST['video_type'] : 'none';
    $videoUrl = trim($_POST['video_url'] ?? '');
    $videoFile = $project['video_file'] ?? null;
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $displayOrder = intval($_POST['display_order'] ?? 1);

    if (empty($title)) {
        set_flash('danger', 'El título del proyecto es obligatorio.');
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Handle Video Upload if chosen
    if ($videoType === 'upload' && isset($_FILES['video_file_input']) && $_FILES['video_file_input']['error'] !== UPLOAD_ERR_NO_FILE) {
        $vidUpload = upload_and_optimize_video($_FILES['video_file_input']);
        if ($vidUpload['success']) {
            $videoFile = $vidUpload['url'];
        } else {
            set_flash('danger', 'Error al procesar el video: ' . $vidUpload['error']);
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    // Clean up video values based on type
    if ($videoType === 'none') {
        $videoUrl = null;
        $videoFile = null;
    } elseif ($videoType === 'youtube') {
        $videoFile = null;
    } elseif ($videoType === 'upload') {
        $videoUrl = null;
    }

    // Handle Cover Image Upload
    $coverImage = $project['cover_image'];
    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = upload_image($_FILES['cover_photo'], 'projects');
        if ($upload['success']) {
            $coverImage = $upload['url'];
        } else {
            set_flash('danger', 'Error en la portada: ' . $upload['error']);
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    if (!$isEdit && empty($coverImage)) {
        set_flash('danger', 'Debes subir una imagen de portada para el proyecto.');
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    try {
        if ($isEdit) {
            $stmtUpdate = $pdo->prepare("UPDATE projects SET title = ?, slug = ?, category = ?, client = ?, event_date = ?, description = ?, extra_title = ?, extra_content = ?, video_type = ?, video_url = ?, video_file = ?, cover_image = ?, is_featured = ?, display_order = ? WHERE id = ?");
            $stmtUpdate->execute([$title, $slug, $category, $client, $eventDate, $description, $extraTitle, $extraContent, $videoType, $videoUrl, $videoFile, $coverImage, $isFeatured, $displayOrder, $id]);
            $projectId = $id;
            set_flash('success', 'Proyecto actualizado correctamente.');
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO projects (title, slug, category, client, event_date, description, extra_title, extra_content, video_type, video_url, video_file, cover_image, is_featured, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtInsert->execute([$title, $slug, $category, $client, $eventDate, $description, $extraTitle, $extraContent, $videoType, $videoUrl, $videoFile, $coverImage, $isFeatured, $displayOrder]);
            $projectId = $pdo->lastInsertId();
            set_flash('success', 'Proyecto creado correctamente.');
        }

        // Handle Multiple Gallery Photos Upload
        if (!empty($_FILES['gallery_photos']['name'][0])) {
            $totalFiles = count($_FILES['gallery_photos']['name']);
            $stmtAddImg = $pdo->prepare("INSERT INTO project_images (project_id, image_url, caption, display_order) VALUES (?, ?, ?, ?)");
            
            $stmtMaxOrder = $pdo->prepare("SELECT MAX(display_order) FROM project_images WHERE project_id = ?");
            $stmtMaxOrder->execute([$projectId]);
            $currentMax = intval($stmtMaxOrder->fetchColumn()) ?: 0;

            for ($i = 0; $i < $totalFiles; $i++) {
                if ($_FILES['gallery_photos']['error'][$i] === UPLOAD_ERR_OK) {
                    $singleFile = [
                        'name'     => $_FILES['gallery_photos']['name'][$i],
                        'type'     => $_FILES['gallery_photos']['type'][$i],
                        'tmp_name' => $_FILES['gallery_photos']['tmp_name'][$i],
                        'error'    => $_FILES['gallery_photos']['error'][$i],
                        'size'     => $_FILES['gallery_photos']['size'][$i]
                    ];
                    $uploadG = upload_image($singleFile, 'projects');
                    if ($uploadG['success']) {
                        $currentMax++;
                        $stmtAddImg->execute([$projectId, $uploadG['url'], $title, $currentMax]);
                    }
                }
            }
        }

        header("Location: " . BASE_URL . "/admin/project-form?id=" . $projectId);
        exit;
    } catch (PDOException $ex) {
        if ($ex->getCode() == 23000) {
            set_flash('danger', 'Ya existe un proyecto con ese enlace/slug. Usa un slug diferente.');
        } else {
            set_flash('danger', 'Error al guardar: ' . $ex->getMessage());
        }
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="adm-header">
  <div>
    <h1 class="adm-title"><?= $isEdit ? 'Editar Proyecto' : 'Crear Nuevo Proyecto' ?></h1>
    <p class="adm-subtitle">Configura los datos del proyecto, portada, galería de tomas y video opcional</p>
  </div>
  <div style="display: flex; gap: 12px;">
    <?php if ($isEdit && !empty($project['slug'])): ?>
      <a href="<?= BASE_URL ?>/proyecto/<?= htmlspecialchars($project['slug']) ?>" target="_blank" class="adm-btn adm-btn-outline">
        <i class="fas fa-external-link-alt"></i> Ver en la web
      </a>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/admin/projects" class="adm-btn adm-btn-outline">
      <i class="fas fa-arrow-left"></i> Volver a Proyectos
    </a>
  </div>
</div>

<form id="projectForm" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

  <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 30px; align-items: flex-start;">
    <!-- Main Info Column -->
    <div>
      <div class="adm-card">
        <h3 class="adm-card-title" style="margin-bottom: 24px;">Información Principal</h3>

        <div class="adm-form-group">
          <label class="adm-form-label" for="projectTitle">Título del Proyecto *</label>
          <input type="text" class="adm-input" id="projectTitle" name="title" value="<?= htmlspecialchars($project['title']) ?>" placeholder="Ej: Convención Anual Orsan 2024" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div class="adm-form-group">
            <label class="adm-form-label" for="projectSlug">Slug / Enlace URL</label>
            <input type="text" class="adm-input" id="projectSlug" name="slug" value="<?= htmlspecialchars($project['slug']) ?>" placeholder="ej: convencion-anual-orsan-2024">
          </div>

          <div class="adm-form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
              <label class="adm-form-label" for="category" style="margin-bottom: 0;">Categoría *</label>
              <button type="button" class="btn-open-cat-modal" id="btnOpenCatModal" title="Agregar Nueva Categoría">
                <i class="fas fa-plus"></i>
              </button>
            </div>
            <select class="adm-select" id="category" name="category" required>
              <?php 
              $foundCurrent = false;
              foreach ($categories as $cat): 
                $isSelected = ($project['category'] === $cat['slug'] || $project['category'] === $cat['name']);
                if ($isSelected) $foundCurrent = true;
              ?>
                <option value="<?= htmlspecialchars($cat['slug']) ?>" <?= $isSelected ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['name']) ?>
                </option>
              <?php endforeach; ?>
              <?php if (!empty($project['category']) && !$foundCurrent): ?>
                <option value="<?= htmlspecialchars($project['category']) ?>" selected>
                  <?= htmlspecialchars($project['category']) ?>
                </option>
              <?php endif; ?>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div class="adm-form-group">
            <label class="adm-form-label" for="client">Cliente / Empresa</label>
            <input type="text" class="adm-input" id="client" name="client" value="<?= htmlspecialchars($project['client'] ?? '') ?>" placeholder="Ej: WEG Chile">
          </div>

          <div class="adm-form-group">
            <label class="adm-form-label" for="event_date">Fecha o Período</label>
            <input type="text" class="adm-input" id="event_date" name="event_date" value="<?= htmlspecialchars($project['event_date'] ?? '') ?>" placeholder="Ej: Marzo 2024">
          </div>
        </div>

        <div class="adm-form-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label class="adm-form-label" style="margin-bottom: 0;">Descripción / Historia del Proyecto</label>
            <button type="button" class="adm-btn adm-btn-secondary" id="toggleHtmlDesc" style="padding: 4px 10px; font-size: 0.75rem;">
              <i class="fas fa-code"></i> HTML
            </button>
          </div>

          <!-- Rich Toolbar for Description -->
          <div class="rich-editor-toolbar" id="toolbarDesc">
            <button type="button" class="tool-btn" data-editor="editorDesc" data-cmd="formatBlock" data-val="h3" title="Encabezado 3"><strong>H3</strong></button>
            <button type="button" class="tool-btn" data-editor="editorDesc" data-cmd="formatBlock" data-val="h4" title="Encabezado 4"><strong>H4</strong></button>
            <button type="button" class="tool-btn" data-editor="editorDesc" data-cmd="formatBlock" data-val="p" title="Párrafo normal">P</button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn" data-editor="editorDesc" data-cmd="bold" title="Negrita"><i class="fas fa-bold"></i></button>
            <button type="button" class="tool-btn" data-editor="editorDesc" data-cmd="italic" title="Cursiva"><i class="fas fa-italic"></i></button>
            <button type="button" class="tool-btn" data-editor="editorDesc" data-cmd="underline" title="Subrayado"><i class="fas fa-underline"></i></button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn" data-editor="editorDesc" data-cmd="insertUnorderedList" title="Lista con viñetas"><i class="fas fa-list-ul"></i></button>
            <button type="button" class="tool-btn" data-editor="editorDesc" data-cmd="insertOrderedList" title="Lista numerada"><i class="fas fa-list-ol"></i></button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn btn-action-link" data-editor="editorDesc" title="Insertar Enlace"><i class="fas fa-link"></i> Enlace</button>
            <button type="button" class="tool-btn" data-editor="editorDesc" data-cmd="unlink" title="Quitar Enlace"><i class="fas fa-unlink"></i></button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn" data-editor="editorDesc" data-cmd="removeFormat" title="Limpiar Formato"><i class="fas fa-eraser"></i></button>
          </div>

          <!-- Visual Canvas for Description -->
          <div id="editorDesc" class="rich-visual-editor" contenteditable="true" spellcheck="false"><?= !empty($project['description']) ? $project['description'] : '' ?></div>
          <textarea id="description" name="description" style="display: none;"><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 24px; align-items: center; margin-top: 10px;">
          <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <input type="checkbox" name="is_featured" value="1" <?= $project['is_featured'] ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--adm-primary);">
            <span style="font-weight: 600; color: #fff;">Destacar en la portada (Home)</span>
          </label>

          <div style="display: flex; align-items: center; gap: 10px;">
            <label class="adm-form-label" style="margin: 0;" for="display_order">Orden:</label>
            <input type="number" class="adm-input" id="display_order" name="display_order" value="<?= $project['display_order'] ?>" style="width: 80px;" min="0">
          </div>
        </div>
      </div>

      <!-- Seccion Secundaria y Video Opcional -->
      <div class="adm-card" style="margin-top: 24px;">
        <h3 class="adm-card-title" style="margin-bottom: 20px;">
          <i class="fas fa-video" style="color: var(--adm-primary); margin-right: 8px;"></i>
          Contenido Secundario y Video Opcional
        </h3>

        <div class="adm-form-group">
          <label class="adm-form-label" for="extra_title">Título Secundario</label>
          <input type="text" class="adm-input" id="extra_title" name="extra_title" value="<?= htmlspecialchars($project['extra_title'] ?? '') ?>" placeholder="Ej: Video de Cobertura y Resumen Audiovisual">
          <div style="font-size: 0.8rem; color: var(--adm-muted); margin-top: 4px;">Aparecerá como encabezado en la sección inferior de la página del proyecto.</div>
        </div>

        <div class="adm-form-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label class="adm-form-label" style="margin-bottom: 0;">Texto o Notas de Producción</label>
            <button type="button" class="adm-btn adm-btn-secondary" id="toggleHtmlExtra" style="padding: 4px 10px; font-size: 0.75rem;">
              <i class="fas fa-code"></i> HTML
            </button>
          </div>

          <!-- Rich Toolbar for Extra Content -->
          <div class="rich-editor-toolbar" id="toolbarExtra">
            <button type="button" class="tool-btn" data-editor="editorExtra" data-cmd="formatBlock" data-val="h3" title="Encabezado 3"><strong>H3</strong></button>
            <button type="button" class="tool-btn" data-editor="editorExtra" data-cmd="formatBlock" data-val="h4" title="Encabezado 4"><strong>H4</strong></button>
            <button type="button" class="tool-btn" data-editor="editorExtra" data-cmd="formatBlock" data-val="p" title="Párrafo normal">P</button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn" data-editor="editorExtra" data-cmd="bold" title="Negrita"><i class="fas fa-bold"></i></button>
            <button type="button" class="tool-btn" data-editor="editorExtra" data-cmd="italic" title="Cursiva"><i class="fas fa-italic"></i></button>
            <button type="button" class="tool-btn" data-editor="editorExtra" data-cmd="underline" title="Subrayado"><i class="fas fa-underline"></i></button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn" data-editor="editorExtra" data-cmd="insertUnorderedList" title="Lista con viñetas"><i class="fas fa-list-ul"></i></button>
            <button type="button" class="tool-btn" data-editor="editorExtra" data-cmd="insertOrderedList" title="Lista numerada"><i class="fas fa-list-ol"></i></button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn btn-action-link" data-editor="editorExtra" title="Insertar Enlace"><i class="fas fa-link"></i> Enlace</button>
            <button type="button" class="tool-btn" data-editor="editorExtra" data-cmd="unlink" title="Quitar Enlace"><i class="fas fa-unlink"></i></button>
            <span class="tool-sep"></span>
            <button type="button" class="tool-btn" data-editor="editorExtra" data-cmd="removeFormat" title="Limpiar Formato"><i class="fas fa-eraser"></i></button>
          </div>

          <!-- Visual Canvas for Extra Content -->
          <div id="editorExtra" class="rich-visual-editor" contenteditable="true" spellcheck="false" style="min-height: 180px;"><?= !empty($project['extra_content']) ? $project['extra_content'] : '' ?></div>
          <textarea id="extra_content" name="extra_content" style="display: none;"><?= htmlspecialchars($project['extra_content'] ?? '') ?></textarea>
        </div>

        <div class="adm-form-group">
          <label class="adm-form-label" for="videoTypeSelect">Tipo de Video (Opcional)</label>
          <select class="adm-select" id="videoTypeSelect" name="video_type">
            <option value="none" <?= (($project['video_type'] ?? 'none') === 'none') ? 'selected' : '' ?>>Sin video</option>
            <option value="youtube" <?= (($project['video_type'] ?? '') === 'youtube') ? 'selected' : '' ?>>Enlace de YouTube</option>
            <option value="upload" <?= (($project['video_type'] ?? '') === 'upload') ? 'selected' : '' ?>>Subir archivo de video al servidor</option>
          </select>
        </div>

        <!-- YouTube URL group -->
        <div class="adm-form-group" id="youtubeInputGroup" style="display: none;">
          <label class="adm-form-label" for="youtubeUrlInput">Enlace del Video en YouTube</label>
          <input type="url" class="adm-input" id="youtubeUrlInput" name="video_url" value="<?= htmlspecialchars($project['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=...">
          <div style="font-size: 0.8rem; color: var(--adm-muted); margin-top: 4px;">Pega la URL de YouTube (watch, youtu.be o shorts). Se cargará de forma responsiva.</div>
          <div id="youtubePreviewBox"></div>
        </div>

        <!-- Video File upload group -->
        <div class="adm-form-group" id="uploadVideoGroup" style="display: none;">
          <label class="adm-form-label">Subir Archivo de Video (MP4, MOV, WEBM — Máx. 100MB)</label>
          <?php if (!empty($project['video_file'])): ?>
            <div style="margin-bottom: 12px; padding: 12px 16px; background: rgba(255,255,255,0.04); border-radius: 8px; border: 1px solid var(--adm-border); display: flex; align-items: center; justify-content: space-between;">
              <span style="font-size: 0.88rem;"><i class="fas fa-file-video" style="color: var(--adm-primary); margin-right: 8px;"></i> Video actual: <strong><?= htmlspecialchars(basename($project['video_file'])) ?></strong></span>
              <a href="<?= BASE_URL ?>/<?= htmlspecialchars($project['video_file']) ?>" target="_blank" class="adm-btn adm-btn-outline adm-btn-sm">Reproducir</a>
            </div>
          <?php endif; ?>

          <div class="upload-dropzone" style="position: relative; overflow: hidden; padding: 26px 16px; text-align: center; border: 2px dashed var(--adm-border); border-radius: 10px; background: rgba(255,255,255,0.02);">
            <input type="file" name="video_file_input" accept="video/mp4,video/quicktime,video/webm,video/*" style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10;" data-info="videoFileInfo">
            <i class="fas fa-file-video" style="font-size: 2.2rem; color: var(--adm-primary); margin-bottom: 8px; display: block;"></i>
            <div style="font-weight: 600; color: #fff; font-size: 0.95rem;">Haz clic o arrastra un archivo de video aquí</div>
            <div style="font-size: 0.8rem; color: var(--adm-muted); margin-top: 4px;" id="videoFileInfo">
              El sistema lo procesará automáticamente con FFmpeg (+faststart) para una carga ultrarrápida y óptima en SEO.
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Media / Cover Column -->
    <div>
      <!-- Cover Image Box -->
      <div class="adm-card">
        <h3 class="adm-card-title" style="margin-bottom: 20px;">Imagen de Portada</h3>
        
        <?php if (!empty($project['cover_image'])): ?>
          <div style="margin-bottom: 16px;">
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($project['cover_image']) ?>" alt="Portada actual" style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 8px; border: 1px solid var(--adm-border);">
            <div style="font-size: 0.8rem; color: var(--adm-muted); margin-top: 4px;">Portada actual</div>
          </div>
        <?php endif; ?>

        <div class="upload-dropzone" style="position: relative; overflow: hidden; padding: 28px 16px; text-align: center; border: 2px dashed var(--adm-border); border-radius: 10px; background: rgba(255,255,255,0.02);">
          <input type="file" id="coverInput" name="cover_photo" accept="image/*" style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10;" data-preview="coverPreviewImg" data-info="coverFileInfo" <?= $isEdit ? '' : 'required' ?>>
          <i class="fas fa-camera" style="font-size: 2rem; color: var(--adm-primary); margin-bottom: 8px; display: block;"></i>
          <div style="font-weight: 600; color: #fff; font-size: 0.92rem;">
            <?= $isEdit ? 'Cambiar imagen de portada' : 'Seleccionar imagen de portada' ?>
          </div>
          <div style="font-size: 0.78rem; color: var(--adm-muted); margin-top: 4px;" id="coverFileInfo">JPG, PNG, WEBP hasta 30MB</div>
          <img id="coverPreviewImg" class="upload-preview" alt="Nueva portada" style="display: none; max-height: 180px; max-width: 100%; margin: 12px auto 0 auto; border-radius: 6px; border: 1px solid var(--adm-border);">
        </div>
      </div>

      <!-- Multiple Gallery Upload Box -->
      <div class="adm-card">
        <h3 class="adm-card-title" style="margin-bottom: 14px;">Galería Adicional del Proyecto</h3>
        <p style="color: var(--adm-muted); font-size: 0.85rem; margin-bottom: 16px;">
          Puedes seleccionar o arrastrar varias fotografías para añadirlas a la galería de este proyecto.
        </p>

        <div class="upload-dropzone" style="position: relative; overflow: hidden; padding: 28px 16px; text-align: center; border: 2px dashed var(--adm-border); border-radius: 10px; background: rgba(255,255,255,0.02);">
          <input type="file" id="galleryInput" name="gallery_photos[]" accept="image/*" multiple style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10;" data-info="galleryFileInfo">
          <i class="fas fa-images" style="font-size: 2rem; color: #10b981; margin-bottom: 8px; display: block;"></i>
          <div style="font-weight: 600; color: #fff; font-size: 0.92rem;">Subir fotos a la galería</div>
          <div style="font-size: 0.78rem; color: var(--adm-muted); margin-top: 4px;" id="galleryFileInfo">Selecciona uno o múltiples archivos</div>
        </div>

        <!-- Existing Gallery Items List -->
        <?php if (!empty($galleryImages)): ?>
          <div style="margin-top: 24px;">
            <div style="font-weight: 700; font-size: 0.88rem; margin-bottom: 12px; color: #fff;">
              Fotos actuales (<?= count($galleryImages) ?>):
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
              <?php foreach ($galleryImages as $gi): ?>
                <div style="position: relative; aspect-ratio: 1; border-radius: 8px; overflow: hidden; background: #000;">
                  <img src="<?= BASE_URL ?>/<?= htmlspecialchars($gi['image_url']) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                  <button type="submit" name="sub_action" value="delete_photo" onclick="document.getElementById('photoIdInput').value = '<?= $gi['id'] ?>';" style="position: absolute; top: 4px; right: 4px; width: 26px; height: 26px; border-radius: 50%; background: rgba(239,68,68,0.85); color: #fff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;" title="Eliminar foto" data-confirm="¿Eliminar esta foto de la galería?">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              <?php endforeach; ?>
            </div>
            <input type="hidden" name="photo_id" id="photoIdInput" value="">
          </div>
        <?php endif; ?>
      </div>

      <button type="submit" class="adm-btn adm-btn-primary" style="width: 100%; justify-content: center; padding: 16px; font-size: 1rem;">
        <i class="fas fa-save"></i> <?= $isEdit ? 'Guardar Cambios del Proyecto' : 'Publicar Proyecto' ?>
      </button>
    </div>
  </div>
</form>

<!-- Modal Unificado de Gestión de Categorías (Crear, Editar, Eliminar) -->
<?php 
$categorySelectId = 'category'; 
require_once __DIR__ . '/modal-category.php'; 
?>

<style>
.rich-editor-toolbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 4px;
  background: #18112e;
  border: 1px solid var(--adm-border);
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
  padding: 8px 10px;
}
.rich-editor-toolbar .tool-btn {
  background: transparent;
  border: 1px solid transparent;
  color: #ddd;
  border-radius: 6px;
  padding: 5px 10px;
  font-size: 0.82rem;
  cursor: pointer;
  transition: all 0.15s;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  justify-content: center;
}
.rich-editor-toolbar .tool-btn:hover {
  background: rgba(255, 197, 1, 0.15);
  color: #FFC501;
  border-color: rgba(255, 197, 1, 0.3);
}
.rich-editor-toolbar .tool-sep {
  width: 1px;
  height: 18px;
  background: var(--adm-border);
  margin: 0 4px;
}
.rich-visual-editor {
  min-height: 220px;
  background: #110c22;
  border: 1px solid var(--adm-border);
  border-top: none;
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;
  padding: 16px;
  color: #eee;
  font-family: inherit;
  font-size: 0.95rem;
  line-height: 1.7;
  outline: none;
  overflow-y: auto;
}
.rich-visual-editor:focus {
  border-color: var(--adm-primary);
}
.rich-visual-editor h3 {
  font-size: 1.25rem;
  color: #fff;
  margin: 16px 0 8px 0;
  border-left: 3px solid var(--adm-primary);
  padding-left: 10px;
}
.rich-visual-editor h4 {
  font-size: 1.1rem;
  color: #FFC501;
  margin: 14px 0 6px 0;
}
.rich-visual-editor p {
  margin-bottom: 12px;
}
.rich-visual-editor a {
  color: var(--adm-primary);
  text-decoration: underline;
}
.rich-visual-editor ul, .rich-visual-editor ol {
  padding-left: 24px;
  margin-bottom: 12px;
}
.rich-visual-editor li {
  margin-bottom: 4px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const projectForm = document.getElementById('projectForm');
  const editorDesc = document.getElementById('editorDesc');
  const textareaDesc = document.getElementById('description');
  const toggleHtmlDesc = document.getElementById('toggleHtmlDesc');

  const editorExtra = document.getElementById('editorExtra');
  const textareaExtra = document.getElementById('extra_content');
  const toggleHtmlExtra = document.getElementById('toggleHtmlExtra');

  let isHtmlDesc = false;
  let isHtmlExtra = false;

  // Generic Command Handlers
  document.querySelectorAll('.rich-editor-toolbar .tool-btn[data-cmd]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const editorId = btn.getAttribute('data-editor');
      const targetEditor = document.getElementById(editorId);
      if (!targetEditor) return;

      const cmd = btn.getAttribute('data-cmd');
      const val = btn.getAttribute('data-val') || null;
      targetEditor.focus();
      document.execCommand(cmd, false, val);
    });
  });

  // Link Buttons
  document.querySelectorAll('.btn-action-link').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const editorId = btn.getAttribute('data-editor');
      const targetEditor = document.getElementById(editorId);
      if (!targetEditor) return;

      const url = prompt('Ingresa la URL del enlace (ej: https://ejemplo.com):');
      if (url) {
        targetEditor.focus();
        document.execCommand('createLink', false, url);
      }
    });
  });

  // Toggle HTML for Description
  if (toggleHtmlDesc && editorDesc) {
    toggleHtmlDesc.addEventListener('click', () => {
      if (!isHtmlDesc) {
        editorDesc.innerText = editorDesc.innerHTML;
        toggleHtmlDesc.innerHTML = '<i class="fas fa-eye"></i> Visual';
        toggleHtmlDesc.style.color = 'var(--adm-primary)';
        isHtmlDesc = true;
      } else {
        editorDesc.innerHTML = editorDesc.innerText;
        toggleHtmlDesc.innerHTML = '<i class="fas fa-code"></i> HTML';
        toggleHtmlDesc.style.color = '';
        isHtmlDesc = false;
      }
    });
  }

  // Toggle HTML for Extra Content
  if (toggleHtmlExtra && editorExtra) {
    toggleHtmlExtra.addEventListener('click', () => {
      if (!isHtmlExtra) {
        editorExtra.innerText = editorExtra.innerHTML;
        toggleHtmlExtra.innerHTML = '<i class="fas fa-eye"></i> Visual';
        toggleHtmlExtra.style.color = 'var(--adm-primary)';
        isHtmlExtra = true;
      } else {
        editorExtra.innerHTML = editorExtra.innerText;
        toggleHtmlExtra.innerHTML = '<i class="fas fa-code"></i> HTML';
        toggleHtmlExtra.style.color = '';
        isHtmlExtra = false;
      }
    });
  }

  // Form Submit: Synchronize Visual Editors with Hidden Textareas
  if (projectForm) {
    projectForm.addEventListener('submit', () => {
      if (editorDesc && textareaDesc) {
        textareaDesc.value = isHtmlDesc ? editorDesc.innerText : editorDesc.innerHTML;
      }
      if (editorExtra && textareaExtra) {
        textareaExtra.value = isHtmlExtra ? editorExtra.innerText : editorExtra.innerHTML;
      }
    });
  }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
