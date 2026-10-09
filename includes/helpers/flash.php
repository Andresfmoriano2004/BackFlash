<?php
/**
 * Mensajes flash: se muestran una sola vez tras redirigir.
 */

declare(strict_types=1);

function flash(string $tipo, string $texto): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'texto' => $texto];
}

function flash_get(): array
{
    $mensajes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensajes;
}
