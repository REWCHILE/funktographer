<?php
require_once __DIR__ . '/config.php';

// Check if PDO extension is enabled in PHP
if (!class_exists('PDO') || (!extension_loaded('pdo_mysql') && !extension_loaded('pdo'))) {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Extensión PDO Requerida — Funktographer</title>
      <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #09090c; color: #f4f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 24px; box-sizing: border-box; }
        .card { background: #13131a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 36px; max-width: 620px; width: 100%; box-shadow: 0 15px 50px rgba(0,0,0,0.6); }
        h2 { color: #FFC501; margin-top: 0; font-size: 1.6rem; }
        .step-box { background: rgba(255,197,1,0.08); border-left: 4px solid #FFC501; padding: 14px 18px; margin: 18px 0; border-radius: 4px; font-size: 0.95rem; line-height: 1.6; }
        p { color: #a1a1aa; line-height: 1.6; }
        ol { color: #d4d4d8; line-height: 1.8; padding-left: 20px; }
        ul { margin-top: 6px; }
        code { background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px; color: #FFC501; font-family: Consolas, monospace; }
        .btn-retry { display: inline-block; margin-top: 15px; background: #FFC501; color: #000; padding: 10px 22px; border-radius: 8px; font-weight: 600; text-decoration: none; }
      </style>
    </head>
    <body>
      <div class="card">
        <h2>Falta activar la extensión PDO en cPanel</h2>
        <p>PHP está ejecutándose en tu servidor, pero la extensión de base de datos <code>pdo_mysql</code> no está marcada en tu versión actual de PHP (<strong>PHP <?= PHP_VERSION ?></strong>).</p>
        <div class="step-box">
          <strong>Cómo activarlo en 30 segundos desde tu cPanel:</strong>
          <ol>
            <li>En tu cPanel busca y abre <strong>"Seleccionar Versión de PHP"</strong> (o <em>"Select PHP Version"</em>).</li>
            <li>Haz clic en la pestaña <strong>"Extensiones"</strong> (<em>Extensions</em>).</li>
            <li>Busca y marca con un visto bueno:
              <ul>
                <li>✅ <code>pdo</code></li>
                <li>✅ <code>pdo_mysql</code> (o <code>nd_pdo_mysql</code>)</li>
              </ul>
            </li>
            <li>Guarda y recarga esta página.</li>
          </ol>
        </div>
        <a href="javascript:location.reload()" class="btn-retry">Recargar Página</a>
      </div>
    </body>
    </html>
    <?php
    exit;
}

$hostsToTry = array_unique([DB_HOST, '127.0.0.1', 'localhost']);
$pdo = null;
$lastError = null;

foreach ($hostsToTry as $host) {
    try {
        $dsn = "mysql:host=" . $host . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 3,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        break;
    } catch (PDOException $e) {
        $lastError = $e;
    }
}

if (!$pdo) {
    // If DB does not exist (code 1049) and we are on localhost, try to create it
    if ($lastError && $lastError->getCode() == 1049) {
        try {
            $rootDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
            $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS);
            $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, $options);
        } catch (Exception $ex) {
            $lastError = $ex;
        }
    }
}

if ($pdo) {
    // Check if tables exist, if not, auto-import database/dump.sql
    try {
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'site_settings'")->fetch();
        if (empty($tableCheck)) {
            $dumpFile = ROOT_PATH . '/database/dump.sql';
            if (file_exists($dumpFile)) {
                $sql = file_get_contents($dumpFile);
                $pdo->exec($sql);
            }
        }
    } catch (Exception $e) {
        // Continue gracefully even if table check fails
    }
} else {
    // Display friendly error diagnostic screen
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Conexión a Base de Datos — Funktographer</title>
      <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #09090c; color: #f4f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 24px; box-sizing: border-box; }
        .card { background: #13131a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 36px; max-width: 600px; width: 100%; box-shadow: 0 15px 50px rgba(0,0,0,0.6); }
        h2 { color: #FFC501; margin-top: 0; font-size: 1.6rem; letter-spacing: -0.5px; }
        .err-box { background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35); color: #fca5a5; padding: 14px 18px; border-radius: 8px; font-family: Consolas, Monaco, monospace; font-size: 0.88rem; margin: 18px 0; word-break: break-all; line-height: 1.5; }
        p { color: #a1a1aa; line-height: 1.6; font-size: 0.95rem; }
        ul { color: #d4d4d8; line-height: 1.8; font-size: 0.95rem; padding-left: 20px; }
        code { background: rgba(255,255,255,0.08); padding: 2px 6px; border-radius: 4px; color: #FFC501; font-family: Consolas, Monaco, monospace; }
        .btn-retry { display: inline-block; margin-top: 15px; background: #FFC501; color: #000; padding: 10px 22px; border-radius: 8px; font-weight: 600; text-decoration: none; }
      </style>
    </head>
    <body>
      <div class="card">
        <h2>Aviso de Configuración de Base de Datos</h2>
        <p>El servidor no pudo establecer conexión con MySQL utilizando las credenciales provistas:</p>
        <div class="err-box"><?= htmlspecialchars($lastError ? $lastError->getMessage() : 'Error desconocido de conexión') ?></div>
        <p><strong>Pasos para verificar en cPanel:</strong></p>
        <ul>
          <li>Ingresa a <strong>Bases de Datos MySQL</strong> en tu cPanel.</li>
          <li>Verifica que la base de datos <code><?= htmlspecialchars(DB_NAME) ?></code> esté creada.</li>
          <li>Verifica que el usuario <code><?= htmlspecialchars(DB_USER) ?></code> exista.</li>
          <li>En la sección <strong>"Añadir usuario a la base de datos"</strong>, añade al usuario con <strong>TODOS LOS PRIVILEGIOS</strong> marcados.</li>
        </ul>
        <a href="javascript:location.reload()" class="btn-retry">Reintentar Conexión</a>
      </div>
    </body>
    </html>
    <?php
    exit;
}

