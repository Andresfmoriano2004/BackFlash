<?php
/**
 * Cierra la sesión del panel y vuelve al inicio del sitio.
 */

require_once __DIR__ . '/../includes/bootstrap.php';

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

session_destroy();

// Regenera el arranque de sesión para el mensaje flash
session_start();
flash('ok', 'Has cerrado la sesión correctamente.');

redirect('index.php');
