<?php
/**
 * Punto de entrada común: configuración, base de datos y funciones.
 * Todas las páginas (públicas y de administración) empiezan con este archivo.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';
