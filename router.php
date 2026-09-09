<?php
/**
 * Local Development Router for PHP Built-in Server
 * Enables extensionless URLs matching production Apache (.htaccess) behavior.
 */

$rawUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedUrl = parse_url($rawUri);
$path = urldecode($parsedUrl['path'] ?? '/');
$docRoot = __DIR__;

// Normalize path
$path = preg_replace('#/+#', '/', $path);
$path = preg_replace('#/\.(/|$)#', '/', $path);
if ($path === '') {
    $path = '/';
}

// 1. If someone accesses a .php file directly via GET (except router.php), 301 redirect to clean URL
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && preg_match('/\.php$/i', $path) && !str_ends_with($path, 'router.php')) {
    $cleanPath = preg_replace('/\.php$/i', '', $path);
    if ($cleanPath === '/index' || $cleanPath === '/admin/index') {
        $cleanPath = ($cleanPath === '/index') ? '/' : '/admin';
    }
    $qs = isset($parsedUrl['query']) && $parsedUrl['query'] !== '' ? '?' . $parsedUrl['query'] : '';
    header("Location: " . $cleanPath . $qs, true, 301);
    exit;
}

// 2. Static files (CSS, JS, images, videos, fonts)
$filePath = realpath($docRoot . $path);
if ($filePath && is_file($filePath) && str_starts_with($filePath, realpath($docRoot))) {
    return false; // let the built-in server serve static file
}

// 3. Aliases: /bio -> /links
if ($path === '/bio' || $path === '/bio/') {
    header("Location: " . BASE_URL . "/links", true, 301);
    exit;
}

// 4. Dynamic Route: /proyecto/{slug} -> proyecto.php?slug={slug}
if (preg_match('#^/proyecto/([a-zA-Z0-9_-]+)/?$#', $path, $matches)) {
    $_GET['slug'] = $matches[1];
    $projectScript = $docRoot . '/proyecto.php';
    if (file_exists($projectScript)) {
        $_SERVER['SCRIPT_NAME'] = '/proyecto.php';
        $_SERVER['PHP_SELF'] = '/proyecto.php';
        $_SERVER['SCRIPT_FILENAME'] = $projectScript;
        chdir($docRoot);
        require $projectScript;
        exit;
    }
}

// 5. Dynamic Route: /blog/{slug} -> blog-post.php?slug={slug}
if (preg_match('#^/blog/([a-zA-Z0-9_-]+)/?$#', $path, $matches)) {
    $_GET['slug'] = $matches[1];
    $blogPostScript = $docRoot . '/blog-post.php';
    if (file_exists($blogPostScript)) {
        $_SERVER['SCRIPT_NAME'] = '/blog-post.php';
        $_SERVER['PHP_SELF'] = '/blog-post.php';
        $_SERVER['SCRIPT_FILENAME'] = $blogPostScript;
        chdir($docRoot);
        require $blogPostScript;
        exit;
    }
}

// 4. Exact PHP file match without extension (e.g. /proyectos -> proyectos.php, /admin/login -> admin/login.php)
$phpFilePath = realpath($docRoot . $path . '.php');
if ($phpFilePath && is_file($phpFilePath) && str_starts_with($phpFilePath, realpath($docRoot))) {
    $_SERVER['SCRIPT_NAME'] = $path . '.php';
    $_SERVER['PHP_SELF'] = $path . '.php';
    $_SERVER['SCRIPT_FILENAME'] = $phpFilePath;
    chdir(dirname($phpFilePath));
    require $phpFilePath;
    exit;
}

// 5. Direct .php file execution (e.g. POST requests or internal scripts)
if ($filePath && is_file($filePath) && str_ends_with($filePath, '.php')) {
    $_SERVER['SCRIPT_NAME'] = $path;
    $_SERVER['PHP_SELF'] = $path;
    $_SERVER['SCRIPT_FILENAME'] = $filePath;
    chdir(dirname($filePath));
    require $filePath;
    exit;
}

// 6. Directory index (e.g. /admin -> admin/index.php)
$dirIndexPath = realpath($docRoot . rtrim($path, '/') . '/index.php');
if ($dirIndexPath && is_file($dirIndexPath) && str_starts_with($dirIndexPath, realpath($docRoot))) {
    $scriptName = rtrim($path, '/') . '/index.php';
    $_SERVER['SCRIPT_NAME'] = $scriptName;
    $_SERVER['PHP_SELF'] = $scriptName;
    $_SERVER['SCRIPT_FILENAME'] = $dirIndexPath;
    chdir(dirname($dirIndexPath));
    require $dirIndexPath;
    exit;
}

// 7. Root fallback
if ($path === '/' || $path === '') {
    $indexPath = realpath($docRoot . '/index.php');
    if ($indexPath) {
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['PHP_SELF'] = '/index.php';
        $_SERVER['SCRIPT_FILENAME'] = $indexPath;
        chdir($docRoot);
        require $indexPath;
        exit;
    }
}

// 404 Not Found
http_response_code(404);
echo "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body style='font-family: sans-serif; text-align: center; padding: 50px;'><h1>404 No Encontrado</h1><p>La página solicitada no existe.</p><a href='/'>Volver al Inicio</a></body></html>";
exit;
