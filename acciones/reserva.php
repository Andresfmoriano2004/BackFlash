<?php
/**
 * Procesa la reserva de mesa:
 *  1. valida fecha, hora y datos de contacto,
 *  2. guarda la reserva en MySQL (estado "pendiente"),
 *  3. envía los correos de notificación y
 *  4. redirige con mensaje de confirmación.
 */

require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('reservar.php');
}

csrf_check();

$nombre    = post_str('nombre', 120);
$email     = post_str('email', 160);
$telefono  = post_str('telefono', 40);
$fecha     = post_str('fecha', 10);
$hora      = post_str('hora', 5);
$personas  = post_str('personas', 3);
$mensaje   = post_str('mensaje', 500);

$errores = [];

if (mb_strlen($nombre) < 3) {
    $errores['nombre'] = 'Escribe tu nombre completo (mínimo 3 caracteres).';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores['email'] = 'Ingresa un correo electrónico válido.';
}
if (preg_replace('/\D/', '', $telefono) < 7) {
    $errores['telefono'] = 'Ingresa un teléfono de contacto (mínimo 7 dígitos).';
}
if (!in_array($personas, array_map('strval', range(1, 20)), true)) {
    $errores['personas'] = 'Elige entre 1 y 20 personas.';
}

$fechaObj = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
$hoje     = new DateTimeImmutable('today');

if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
    $errores['fecha'] = 'Selecciona una fecha válida.';
} elseif ($fechaObj < $hoje) {
    $errores['fecha'] = 'La fecha no puede ser anterior a hoy.';
} elseif ($fechaObj > $hoje->modify('+90 days')) {
    $errores['fecha'] = 'Solo aceptamos reservas con hasta 90 días de anticipación.';
}

if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora)) {
    $errores['hora'] = 'Selecciona una hora válida.';
} else {
    $minutos = (int) substr($hora, 0, 2) * 60 + (int) substr($hora, 3, 2);
    if ($minutos < 11 * 60 || $minutos > 22 * 60) {
        $errores['hora'] = 'Atendemos de 11:00 a. m. a 10:00 p. m.';
    } elseif ($minutos % 15 !== 0) {
        $errores['hora'] = 'Las reservas se agendan cada 15 minutos.';
    }
}

if ($errores) {
    old_set([
        'nombre'    => $nombre,
        'email'     => $email,
        'telefono'  => $telefono,
        'fecha'     => $fecha,
        'hora'      => $hora,
        'personas'  => $personas,
        'mensaje'   => $mensaje,
        '__errores' => $errores,
    ]);
    flash('error', 'No pudimos confirmar la reserva: revisa los campos marcados.');
    redirect('reservar.php');
}

// Aforo: máximo 8 reservas por franja de 30 minutos
$ocupadas = q(
    'SELECT COUNT(*) AS n FROM reservas
     WHERE estado <> "cancelada" AND fecha = ? AND hora = ?',
    [$fecha, $hora . ':00']
)->fetch();

if ((int) $ocupadas['n'] >= 8) {
    old_set([
        'nombre'   => $nombre,
        'email'    => $email,
        'telefono' => $telefono,
        'fecha'    => $fecha,
        'hora'     => $hora,
        'personas' => $personas,
        'mensaje'  => $mensaje,
    ]);
    flash('error', 'Esa franja horaria ya está llena. Elige otra hora, por ejemplo las ' . date('H:i', strtotime($hora . ':00 +45 minutes')) . '.');
    redirect('reservar.php');
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
