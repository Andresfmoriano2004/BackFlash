<?php
/**
 * Autoloader mínimo (PSR-4) para el espacio de nombres BackFlash\ -> src/
 *
 * El proyecto no usa Composer a propósito, así que no hay vendor/ ni
 * dependencias externas: esto es todo lo que hace falta para que las clases
 * de src/ se carguen solas al nombrarlas.
 */

declare(strict_types=1);

spl_autoload_register(static function (string $clase): void {
    $prefijo = 'BackFlash\\';

    if (!str_starts_with($clase, $prefijo)) {
        return;
    }

    $relativa = substr($clase, strlen($prefijo));
    $ruta     = __DIR__ . '/' . str_replace('\\', '/', $relativa) . '.php';

    if (is_file($ruta)) {
        require $ruta;
    }
});
