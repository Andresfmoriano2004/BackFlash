<?php
/**
 * BackFlash - configuración general
 * Edita estos valores para adaptar el sitio a tu entorno.
 */

// --- Base de datos (XAMPP / MariaDB) ---
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'backflash');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// --- Datos del restaurante ---
define('SITE_NAME', 'BackFlash');
define('SITE_EMAIL', 'contacto@backflash.com');
define('SITE_PHONE', '+57 300 123 4567');
define('SITE_ADDRESS', 'calle 28 # 6-27, Pereira, Colombia');
define('SITE_YEAR', '2026');

// --- Correo ---
// 'mail' -> usa mail() de PHP (requiere sendmail/SMTP configurado en XAMPP)
// 'log'  -> no envía: guarda todo en logs/mail.log (opción segura por defecto)
define('MAIL_MODE', 'log');
define('MAIL_FROM', 'no-reply@backflash.com');
define('MAIL_LOG', dirname(__DIR__) . '/logs/mail.log');

// --- Ruta base del proyecto (funciona en /BackFlash o desplegado en la raíz) ---
$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$dir    = dirname($script);
foreach (['/admin', '/actions', '/acciones'] as $sub) {
    if (str_ends_with($dir, $sub)) {
        $dir = substr($dir, 0, -strlen($sub));
        break;
    }
}
define('BASE_URL', rtrim($dir, '/'));
