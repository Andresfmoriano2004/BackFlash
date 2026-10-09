<?php
/**
 * Punto de entrada común: configuración, base de datos y funciones.
 * Todas las páginas (públicas y de administración) empiezan con este archivo.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Arrancar sesión antes de cargar los helpers que la usan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helpers modulares (antes: un único functions.php monolítico)
require_once __DIR__ . '/helpers/html.php';
require_once __DIR__ . '/helpers/csrf.php';
require_once __DIR__ . '/helpers/flash.php';
require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/helpers/mail.php';
require_once __DIR__ . '/helpers/form.php';
