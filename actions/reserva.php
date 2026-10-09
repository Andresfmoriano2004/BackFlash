<?php
/**
 * Procesa la reserva de mesa:
 *  1. valida fecha, hora y datos de contacto,
 *  2. guarda la reserva en MySQL (estado "pendiente"),
 *  3. envía los correos de notificación y
 *  4. redirige con mensaje de confirmación.
 */

use BackFlash\Validation\Reglas;
use BackFlash\Validation\ReservaValidator;

require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('reservar.php');
}

csrf_check();

$datos = [
    'nombre'   => post_str('nombre', Reglas::NOMBRE_MAX),
    'email'    => post_str('email', Reglas::EMAIL_MAX),
    'telefono' => post_str('telefono', Reglas::TELEFONO_MAX),
    'fecha'    => post_str('fecha', 10),
    'hora'     => post_str('hora', 5),
    'personas' => post_str('personas', 3),
    'mensaje'  => post_str('mensaje', Reglas::COMENTARIO_MAX),
];

/** Vuelve al formulario conservando lo escrito (y los errores, si los hay). */
$volverAlFormulario = static function (array $datos, array $errores = []): never {
    if ($errores !== []) {
        $datos['__errores'] = $errores;
    }
    old_set($datos);
    redirect('reservar.php');
};

// Las reglas viven en src/Validation/ReservaValidator.php (fuente única).
$validador = new ReservaValidator();

if (!$validador->validar($datos)) {
    flash('error', 'No pudimos confirmar la reserva: revisa los campos marcados.');
    $volverAlFormulario($datos, $validador->errores());
}

$nombre   = $datos['nombre'];
$email    = $datos['email'];
$telefono = $datos['telefono'];
$fecha    = $datos['fecha'];
$hora     = $datos['hora'];
$personas = $datos['personas'];
$mensaje  = $datos['mensaje'];

// Aforo: máximo 8 reservas por franja de 30 minutos
$ocupadas = q(
    'SELECT COUNT(*) AS n FROM reservas
     WHERE estado <> "cancelada" AND fecha = ? AND hora = ?',
    [$fecha, $hora . ':00']
)->fetch();

if ((int) $ocupadas['n'] >= 8) {
    flash('error', 'Esa franja horaria ya está llena. Elige otra hora, por ejemplo las ' . date('H:i', strtotime($hora . ':00 +45 minutes')) . '.');
    $volverAlFormulario($datos);
}

q(
    'INSERT INTO reservas (nombre, email, telefono, fecha, hora, personas, mensaje) VALUES (?, ?, ?, ?, ?, ?, ?)',
    [$nombre, $email, $telefono, $fecha, $hora . ':00', (int) $personas, $mensaje]
);

$fechaLarga = date('d/m/Y', strtotime($fecha));

$cuerpoRestaurante = "Nueva reserva desde la página web de BackFlash\n"
    . str_repeat('-', 50) . "\n"
    . "Cliente: {$nombre}\n"
    . "Email:   {$email}\n"
    . "Tel:     {$telefono}\n"
    . "Fecha:   {$fechaLarga} a las {$hora}\n"
    . "Personas: {$personas}\n"
    . ($mensaje !== '' ? "Comentarios: {$mensaje}\n" : '')
    . str_repeat('-', 50) . "\n"
    . "Gestión de reservas: panel de administración.\n";

enviar_correo(SITE_EMAIL, "Reserva: {$nombre} - {$fechaLarga} {$hora}", $cuerpoRestaurante);

$cuerpoCliente = "Hola {$nombre}:\n\n"
    . "Hemos recibido tu reserva en BackFlash:\n"
    . "  Fecha:  {$fechaLarga}\n"
    . "  Hora:   {$hora}\n"
    . "  Personas: {$personas}\n"
    . "  Dirección: " . SITE_ADDRESS . "\n\n"
    . "Te confirmaremos por correo en cuanto el equipo la revise.\n\n"
    . "¡Te esperamos!\n" . SITE_NAME . "\n";

enviar_correo($email, 'Reserva recibida - BackFlash', $cuerpoCliente);

flash('ok', "¡Listo {$nombre}! Registramos tu reserva para {$fechaLarga} a las {$hora} para {$personas} " . ((int) $personas === 1 ? 'persona' : 'personas') . ". Te confirmaremos por correo.");
redirect('reservar.php');
