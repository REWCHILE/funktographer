<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * Get setting value from database
 */
function get_setting($key, $default = '') {
    global $pdo;
    static $cache = [];
    if (isset($cache[$key])) return $cache[$key];
    
    if (!$pdo) return $default;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        $cache[$key] = $row ? $row['setting_value'] : $default;
        return $cache[$key];
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Get optimized WebP image URL if it exists on disk, otherwise return original
 */
function img_url($path) {
    if (empty($path)) return $path;
    $cleanPath = ltrim((string)$path, '/');
    $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $cleanPath);
    if (file_exists(__DIR__ . '/../' . $webpPath)) {
        return $webpPath;
    }
    return $cleanPath;
}

/**
 * Clean and sanitize string input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function csrf_verify($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if admin is logged in
 */
function is_admin_logged_in() {
    return !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_id']);
}

/**
 * Require admin access or redirect to login
 */
function require_admin() {
    if (!is_admin_logged_in()) {
        $loginUrl = BASE_URL . '/admin/login';
        header("Location: $loginUrl");
        exit;
    }
}

/**
 * Flash message helpers
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Slugify text
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a-' . time() : $text;
}

/**
 * Handle image file upload with security validation
 */
/**
 * Handle image file upload with security validation and broad format support
 */
function upload_image($file, $subfolder = 'home') {
    if (!isset($file) || !is_array($file)) {
        return ['success' => false, 'error' => 'No se recibió ningún archivo de imagen.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'El archivo supera el tamaño máximo permitido por el servidor.',
            UPLOAD_ERR_FORM_SIZE  => 'El archivo supera el tamaño máximo del formulario.',
            UPLOAD_ERR_PARTIAL    => 'El archivo solo se subió parcialmente. Por favor, intenta nuevamente.',
            UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo de imagen.',
            UPLOAD_ERR_NO_TMP_DIR => 'Error de servidor: falta carpeta temporal.',
            UPLOAD_ERR_CANT_WRITE => 'Error de servidor: fallo al escribir en el disco.',
            UPLOAD_ERR_EXTENSION  => 'Subida detenida por una extensión de PHP.',
        ];
        return ['success' => false, 'error' => $errorMessages[$file['error']] ?? ('Error al subir archivo (código ' . $file['error'] . ')')];
    }

    // Check size limit: 30MB
    if ($file['size'] > 30 * 1024 * 1024) {
        return ['success' => false, 'error' => 'La imagen supera el límite permitido de 30MB.'];
    }

    $allowedMimes = [
        'image/jpeg', 'image/pjpeg', 'image/jpg',
        'image/png', 'image/x-png',
        'image/webp',
        'image/avif',
        'image/heic', 'image/heif',
        'image/gif'
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $origExt = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'heic', 'heif', 'gif'];

    if (!in_array($mime, $allowedMimes) && !in_array($origExt, $allowedExts)) {
        return ['success' => false, 'error' => 'Formato no permitido (' . htmlspecialchars($mime) . '). Aceptados: JPG, PNG, WEBP, AVIF, HEIC.'];
    }

    $ext = match($mime) {
        'image/jpeg', 'image/pjpeg', 'image/jpg' => 'jpg',
        'image/png', 'image/x-png'               => 'png',
        'image/webp'                             => 'webp',
        'image/avif'                             => 'avif',
        'image/gif'                              => 'gif',
        default                                  => ($origExt ?: 'jpg')
    };

    $targetDir = UPLOADS_PATH . '/' . trim($subfolder, '/');
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0777, true);
    }

    $fileName = uniqid('funk_', true) . '.' . $ext;
    $targetPath = $targetDir . '/' . $fileName;

    $saved = is_uploaded_file($file['tmp_name'])
        ? move_uploaded_file($file['tmp_name'], $targetPath)
        : copy($file['tmp_name'], $targetPath);

    if ($saved) {
        return [
            'success'  => true,
            'url'      => 'uploads/' . trim($subfolder, '/') . '/' . $fileName,
            'path'     => 'uploads/' . trim($subfolder, '/') . '/' . $fileName,
            'filename' => $fileName
        ];
    }

    return ['success' => false, 'error' => 'No se pudo guardar el archivo en el servidor. Revisa permisos de carpeta.'];
}

/**
 * Extract YouTube Video ID from any standard URL
 */
function get_youtube_id($url) {
    if (empty($url)) return null;
    $pattern = '%^(?:https?://)?(?:www\.|m\.)?(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:embed/|v/|watch\?v=|watch\?.+&v=|shorts/))([\w-]{11})(?:\S+)?$%x';
    if (preg_match($pattern, trim($url), $matches)) {
        return $matches[1];
    }
    return null;
}

/**
 * Get Clean Embed URL for YouTube
 */
function get_youtube_embed_url($url) {
    $id = get_youtube_id($url);
    if ($id) {
        return "https://www.youtube-nocookie.com/embed/{$id}?rel=0&modestbranding=1&autoplay=0";
    }
    return null;
}

/**
 * Handle video upload and optimize with FFmpeg for web streaming (faststart, h264, web-friendly)
 */
function upload_and_optimize_video($file, $subfolder = 'projects/videos') {
    if (!isset($file) || !is_array($file)) {
        return ['success' => false, 'error' => 'No se recibió ningún archivo de video.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Error al subir el video (código ' . $file['error'] . ')'];
    }

    // Limit video size: 100MB
    if ($file['size'] > 100 * 1024 * 1024) {
        return ['success' => false, 'error' => 'El video supera el límite de 100MB. Considera usar un enlace de YouTube o comprimirlo previamente.'];
    }

    $origExt = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    $allowedVideoExts = ['mp4', 'mov', 'webm', 'mkv', 'avi', 'm4v'];
    if (!in_array($origExt, $allowedVideoExts)) {
        return ['success' => false, 'error' => 'Formato de video no permitido. Aceptados: MP4, MOV, WEBM, MKV.'];
    }

    $targetDir = UPLOADS_PATH . '/' . trim($subfolder, '/');
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0777, true);
    }

    $tempRawName = uniqid('temp_raw_', true) . '.' . $origExt;
    $tempRawPath = $targetDir . '/' . $tempRawName;

    $savedRaw = is_uploaded_file($file['tmp_name'])
        ? move_uploaded_file($file['tmp_name'], $tempRawPath)
        : copy($file['tmp_name'], $tempRawPath);

    if (!$savedRaw) {
        return ['success' => false, 'error' => 'Fallo al guardar el video temporal en el servidor.'];
    }

    $finalName = uniqid('vid_', true) . '.mp4';
    $finalPath = $targetDir . '/' . $finalName;

    // Check if ffmpeg is available
    $ffmpegCmd = "ffmpeg -y -i " . escapeshellarg($tempRawPath) . " -c:v libx264 -crf 24 -preset fast -vf \"scale='min(1920,iw)':-2\" -c:a aac -b:a 128k -movflags +faststart " . escapeshellarg($finalPath) . " 2>&1";
    @exec($ffmpegCmd, $output, $returnCode);

    if ($returnCode === 0 && file_exists($finalPath) && filesize($finalPath) > 0) {
        @unlink($tempRawPath);
        return [
            'success' => true,
            'url'     => 'uploads/' . trim($subfolder, '/') . '/' . $finalName
        ];
    }

    // Fallback if ffmpeg failed: keep the raw upload as mp4
    if (file_exists($tempRawPath)) {
        rename($tempRawPath, $finalPath);
        return [
            'success' => true,
            'url'     => 'uploads/' . trim($subfolder, '/') . '/' . $finalName
        ];
    }

    return ['success' => false, 'error' => 'Error al procesar y optimizar el video.'];
}
