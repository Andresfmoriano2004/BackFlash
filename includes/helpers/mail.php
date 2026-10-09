<?php
/**
 * Correo: envía y registra en logs/mail.log.
 */

declare(strict_types=1);

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

    $directorio = dirname(MAIL_LOG);
    if (!is_dir($directorio)) {
        @mkdir($directorio, 0775, true);
    }
    @file_put_contents(MAIL_LOG, $registro, FILE_APPEND);

    return $enviado;
}
