<?php
/**
 * Utilidades de formulario: lectura de POST, valores anteriores,
 * errores de campo y formateo de precios.
 */

declare(strict_types=1);

/** Lee un campo del POST, recortado y sin saltos de línea peligrosos. */
function post_str(string $campo, int $max = 500): string
{
    $valor = trim((string) ($_POST[$campo] ?? ''));
    $valor = str_replace(["\0", "\r"], '', $valor);
    return mb_substr($valor, 0, $max);
}

/* ------------------------------------------------------------------
 * Valores anteriores del formulario (para no perder lo escrito
 * cuando la validación del servidor redirige de vuelta)
 * ---------------------------------------------------------------- */

function old_set(array $valores): void
{
    $_SESSION['old'] = $valores;
}

function old(string $campo, string $defecto = ''): string
{
    return (string) ($_SESSION['old'][$campo] ?? $defecto);
}

function old_clear(): void
{
    unset($_SESSION['old']);
}

/**
 * Devuelve el mensaje de validación guardado para un campo
 * (se almacena en old_set() con la clave reservada __errores).
 */
function error_de(string $campo): string
{
    return (string) ($_SESSION['old']['__errores'][$campo] ?? '');
}

/** Atributo aria-invalid para un campo con error. */
function aria_invalido(string $campo): string
{
    return error_de($campo) !== '' ? ' aria-invalid="true"' : '';
}

/** Imprime el mensaje de error de un campo si existe. */
function mostrar_error(string $campo): void
{
    $mensaje = error_de($campo);
    if ($mensaje !== '') {
        echo '<span class="field-error" id="error-' . e($campo) . '">' . e($mensaje) . '</span>';
    }
}

/** Formatea un precio en pesos colombianos: 20000 -> "$20.000 COP" */
function precio($valor): string
{
    return '$' . number_format((float) $valor, 0, ',', '.') . ' COP';
}
