<?php
/**
 * Procesa el formulario de contacto:
 *  1. valida en el servidor,  2. guarda en MySQL,
 *  3. envía el correo (o lo registra en logs/mail.log) y
 *  4. redirige de vuelta con un mensaje.
 */

require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('contacto.php');
}

csrf_check();

$nombre  = post_str('nombre', 120);
$email   = post_str('email', 160);
$asunto  = post_str('asunto', 160);
$mensaje = post_str('mensaje', 4000);

$errores = [];

if (mb_strlen($nombre) < 3) {
    $errores['nombre'] = 'Escribe tu nombre (mínimo 3 caracteres).';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores['email'] = 'Ingresa un correo electrónico válido.';
}
if (mb_strlen($asunto) < 3) {
    $errores['asunto'] = 'El asunto debe tener al menos 3 caracteres.';
}
if (mb_strlen($mensaje) < 10) {
    $errores['mensaje'] = 'El mensaje debe tener al menos 10 caracteres.';
}

if ($errores) {
    old_set([
        'nombre'     => $nombre,
        'email'      => $email,
        'asunto'     => $asunto,
        'mensaje'    => $mensaje,
        '__errores'  => $errores,
    ]);
    flash('error', 'No pudimos enviar el mensaje: revisa los campos marcados.');
    redirect('contacto.php');
}

q(
    'INSERT INTO mensajes (nombre, email, asunto, mensaje) VALUES (?, ?, ?, ?)',
    [$nombre, $email, $asunto, $mensaje]
);

$cuerpo = "Nuevo mensaje desde la página web de BackFlash\n"
    . str_repeat('-', 50) . "\n"
    . "Nombre:  {$nombre}\n"
    . "Email:   {$email}\n"
    . "Asunto:  {$asunto}\n"
    . str_repeat('-', 50) . "\n"
    . $mensaje . "\n";

enviar_correo(SITE_EMAIL, 'Contacto web: ' . $asunto, $cuerpo);

flash('ok', "¡Gracias, {$nombre}! Tu mensaje ha sido enviado y quedó registrado. Te responderemos pronto.");
redirect('contacto.php');
