<?php
/**
 * Funciones compartidas: escapado, CSRF, mensajes flash, correo y control de acceso.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Escapa texto para imprimirlo en HTML. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** URL absoluta dentro del proyecto: url('menu.php') -> /BackFlash/menu.php */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/** Redirige dentro del proyecto y termina la ejecución. */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/* ------------------------------------------------------------------
 * Token CSRF
 * ---------------------------------------------------------------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Aborta la petición si el token CSRF no coincide. */
function csrf_check(): void
{
    $token = $_POST['csrf'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Petición no válida (token CSRF). Vuelve atrás y reintenta.');
    }
}

/* ------------------------------------------------------------------
 * Mensajes flash (se muestran una sola vez tras redirigir)
 * ---------------------------------------------------------------- */

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

/* ------------------------------------------------------------------
 * Control de acceso del panel de administración
 * ---------------------------------------------------------------- */

function admin_logueado(): bool
{
    return !empty($_SESSION['admin']);
}

function requerir_admin(): void
{
    if (!admin_logueado()) {
        redirect('admin/login.php');
    }
}

/* ------------------------------------------------------------------
 * Correo
 * ---------------------------------------------------------------- */

/**
 * Envía un correo y, sea cual sea el modo, deja constancia en logs/mail.log.
 * Devuelve true solo si mail() lo aceptó realmente.
 */
function enviar_correo(string $para, string $asunto, string $cuerpo): bool
{
    $cabeceras  = 'From: ' . MAIL_FROM . "\r\n";
    $cabeceras .= "Reply-To: " . MAIL_FROM . "\r\n";
    $cabeceras .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $enviado = false;
    if (MAIL_MODE === 'mail') {
        $asuntoB64 = '=?UTF-8?B?' . base64_encode($asunto) . '?=';
        $enviado = @mail($para, $asuntoB64, $cuerpo, $cabeceras);
    }

    $registro = sprintf(
        "[%s] modo=%s para=%s asunto=%s enviado=%s\n%s\n%s\n",
        date('Y-m-d H:i:s'),
        MAIL_MODE,
        $para,
        str_replace(["\r", "\n"], ' ', $asunto),
        $enviado ? 'si' : 'no',
        $cuerpo,
        str_repeat('-', 60)
    );

    // El directorio de registros debe existir (y si no, lo creamos)
    $directorio = dirname(MAIL_LOG);
    if (!is_dir($directorio)) {
        @mkdir($directorio, 0775, true);
    }
    @file_put_contents(MAIL_LOG, $registro, FILE_APPEND);

    return $enviado;
}

/* ------------------------------------------------------------------
 * Utilidades de formulario
 * ---------------------------------------------------------------- */

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
