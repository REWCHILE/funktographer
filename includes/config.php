<?php
// Prevent multiple inclusions
if (defined('FUNK_INIT')) return;
define('FUNK_INIT', true);

// Start session securely
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @ini_set('session.cookie_httponly', 1);
    @ini_set('session.use_only_cookies', 1);
    session_start();
}

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'funktographer_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Paths & URLs
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
