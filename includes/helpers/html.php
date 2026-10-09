<?php
/**
 * HTML helpers: escapado, URLs y redirección.
 */

declare(strict_types=1);

/** Escapa texto para imprimirlo en HTML. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** URL absoluta dentro del proyecto: url('menu.php') -> /BackFlash/menu.php */
function url(string $path = ''): string
{
    $clean = ltrim($path, '/');

    // Mapeo automático de assets para compatibilidad transparente con la nueva estructura
    if ($clean === 'style.css') {
        $clean = 'public/css/style.css';
    } elseif ($clean === 'admin/admin.css') {
        $clean = 'public/css/admin.css';
    } elseif ($clean === 'js/main.js') {
        $clean = 'public/js/main.js';
    } elseif (str_starts_with($clean, 'img/')) {
        $clean = 'public/assets/' . $clean;
    } elseif (str_starts_with($clean, 'images/')) {
        $clean = 'public/assets/' . $clean;
    }

    return BASE_URL . '/' . $clean;
}

/** Helper semántico para assets estáticos dentro de public/ */
function asset(string $path = ''): string
{
    $clean = ltrim($path, '/');
    if (!str_starts_with($clean, 'public/')) {
        $clean = 'public/' . $clean;
    }
    return BASE_URL . '/' . $clean;
}

/** Redirige dentro del proyecto y termina la ejecución. */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}
