<?php
// Prevent multiple inclusions
if (defined('FUNK_INIT')) return;
define('FUNK_INIT', true);

// Safe session directory fallback
$sessPath = dirname(__DIR__) . '/storage/sessions';
if (!is_dir($sessPath)) {
    @mkdir($sessPath, 0775, true);
}
if (is_dir($sessPath) && is_writable($sessPath)) {
    @session_save_path($sessPath);
} elseif (is_writable(sys_get_temp_dir())) {
    @session_save_path(sys_get_temp_dir());
}

// Start session securely
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @ini_set('session.cookie_httponly', 1);
    @ini_set('session.use_only_cookies', 1);
    @session_start();
}

// Enable error reporting and extension check if ?debug=1 is present
if (isset($_GET['debug']) && $_GET['debug'] === '1') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    echo "<div style='background:#111;color:#fff;padding:15px;font-family:monospace;'>";
    echo "<b>PHP Version:</b> " . PHP_VERSION . "<br>";
    echo "<b>PDO Loaded:</b> " . (extension_loaded('pdo') ? 'YES' : 'NO') . "<br>";
    echo "<b>PDO MySQL Loaded:</b> " . (extension_loaded('pdo_mysql') ? 'YES' : 'NO') . "<br>";
    echo "<b>MySQLi Loaded:</b> " . (extension_loaded('mysqli') ? 'YES' : 'NO') . "<br>";
    echo "<b>Session Path:</b> " . session_save_path() . "<br>";
    echo "</div>";
}

// Database configuration (Auto-detects Local vs Production)
$isLocalEnv = (isset($_SERVER['HTTP_HOST']) && (str_contains($_SERVER['HTTP_HOST'], '127.0.0.1') || str_contains($_SERVER['HTTP_HOST'], 'localhost'))) || (PHP_OS_FAMILY === 'Windows');

if ($isLocalEnv) {
    define('DB_HOST', '127.0.0.1');
    define('DB_PORT', '3306');
    define('DB_NAME', 'funktographer_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_CHARSET', 'utf8mb4');
} else {
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'cfu58607_funktophotographer');
    define('DB_USER', 'cfu58607_funktophotographer');
    define('DB_PASS', 'EPq0kP)pdH;$P]8b');
    define('DB_CHARSET', 'utf8mb4');
}

// Rutas y URLs
define('ROOT_PATH', dirname(__DIR__));
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Auto-detect base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseDir = trim($scriptName, '/');
if (str_contains($baseDir, 'admin')) {
    $baseDir = dirname($baseDir);
}
$baseDir = trim(str_replace('\\', '/', $baseDir), '/.');
if ($baseDir === '.') $baseDir = '';
define('BASE_URL', $protocol . $host . ($baseDir ? '/' . $baseDir : ''));

// Site defaults
define('SITE_NAME', 'Funktographer');
define('SITE_PHONE', '+56 9 4757 3794');
define('SITE_WHATSAPP', '56947573794');
define('SITE_EMAIL', 'contacto@funktographer.cl');
define('SITE_INSTAGRAM', 'https://www.instagram.com/funktographer/');
