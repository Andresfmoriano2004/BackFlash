<?php
/**
 * Procesa el formulario de contacto:
 *  1. valida en el servidor,  2. guarda en MySQL,
 *  3. envía el correo (o lo registra en logs/mail.log) y
 *  4. redirige de vuelta con un mensaje.
 */

use BackFlash\Validation\ContactoValidator;
use BackFlash\Validation\Reglas;

require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('contacto.php');
}

csrf_check();

$datos = [
    'nombre'  => post_str('nombre', Reglas::NOMBRE_MAX),
    'email'   => post_str('email', Reglas::EMAIL_MAX),
    'asunto'  => post_str('asunto', Reglas::ASUNTO_MAX),
    'mensaje' => post_str('mensaje', Reglas::MENSAJE_MAX),
];

// Las reglas viven en src/Validation/ContactoValidator.php (fuente única).
$validador = new ContactoValidator();

if (!$validador->validar($datos)) {
    old_set($datos + ['__errores' => $validador->errores()]);
    flash('error', 'No pudimos enviar el mensaje: revisa los campos marcados.');
    redirect('contacto.php');
}

$nombre  = $datos['nombre'];
$email   = $datos['email'];
$asunto  = $datos['asunto'];
$mensaje = $datos['mensaje'];

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
