<?php
/**
 * Reserva de mesa en línea.
 * Los datos llegan por POST a acciones/reserva.php.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Reservas - BackFlash';
$metaDesc  = 'Reserva tu mesa en BackFlash: elige fecha, hora y número de personas en el mejor restaurante de comida colombiana de Pereira.';
$nav       = 'reservas';
$h1        = 'Reserva tu mesa';
$h1sub     = 'Fácil y rápido';

$hoy      = new DateTimeImmutable('today');
$minFecha = $hoy->format('Y-m-d');
$maxFecha = $hoy->modify('+90 days')->format('Y-m-d');

$proxBusy = q(
    'SELECT fecha, hora, personas FROM reservas
     WHERE estado <> "cancelada" AND fecha >= CURDATE()
     ORDER BY fecha, hora LIMIT 4'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

    <section class="contact-form container">
        <div class="container1">
            <h2>Reserva de mesa</h2>
            <p class="form-intro">Completa el formulario y recibirás la confirmación por correo. Para grupos de más de 12 personas, escríbenos directamente.</p>

            <form action="<?= e(url('acciones/reserva.php')) ?>" method="post" id="reserveForm" class="js-form" novalidate>
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="r-nombre">Nombre completo</label>
                        <input type="text" id="r-nombre" name="nombre" autocomplete="name" required minlength="3"
                               value="<?= e(old('nombre')) ?>"<?= aria_invalido('nombre') ?> aria-describedby="error-nombre">
                        <?php mostrar_error('nombre'); ?>
                    </div>

                    <div class="form-group">
                        <label for="r-email">Correo Electrónico</label>
                        <input type="email" id="r-email" name="email" autocomplete="email" required
                               value="<?= e(old('email')) ?>"<?= aria_invalido('email') ?> aria-describedby="error-email">
                        <?php mostrar_error('email'); ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="r-telefono">Teléfono</label>
                        <input type="tel" id="r-telefono" name="telefono" autocomplete="tel" required
                               placeholder="300 123 4567" value="<?= e(old('telefono')) ?>"<?= aria_invalido('telefono') ?>
                               aria-describedby="error-telefono">
                        <?php mostrar_error('telefono'); ?>
                    </div>

                    <div class="form-group">
                        <label for="r-personas">Personas</label>
                        <select id="r-personas" name="personas" required<?= aria_invalido('personas') ?> aria-describedby="error-personas">
                            <option value="">Selecciona…</option>
                            <?php for ($i = 1; $i <= 20; $i++): ?>
                                <option value="<?= $i ?>"<?= old('personas') == (string) $i ? ' selected' : '' ?>>
                                    <?= $i ?> <?= $i === 1 ? 'persona' : 'personas' ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <?php mostrar_error('personas'); ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="r-fecha">Fecha</label>
                        <input type="date" id="r-fecha" name="fecha" required
                               min="<?= $minFecha ?>" max="<?= $maxFecha ?>"
                               value="<?= e(old('fecha')) ?>"<?= aria_invalido('fecha') ?> aria-describedby="error-fecha">
                        <?php mostrar_error('fecha'); ?>
                    </div>

                    <div class="form-group">
                        <label for="r-hora">Hora</label>
                        <input type="time" id="r-hora" name="hora" required step="900"
                               min="11:00" max="22:00" value="<?= e(old('hora') ?: '19:00') ?>"
                               <?= aria_invalido('hora') ?> aria-describedby="error-hora">
                        <?php mostrar_error('hora'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="r-mensaje">Comentarios (opcional)</label>
                    <textarea id="r-mensaje" name="mensaje" maxlength="500"><?= e(old('mensaje')) ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit">Confirmar reserva</button>
                </div>
            </form>

            <?php if ($proxBusy): ?>
                <div class="reservas-proximas">
                    <h3>Próximas reservas confirmadas</h3>
                    <ul>
                        <?php foreach ($proxBusy as $r): ?>
                            <li>
                                <?= e(date('d/m/Y', strtotime($r['fecha']))) ?> ·
                                <?= e(substr($r['hora'], 0, 5)) ?> ·
                                <?= (int) $r['personas'] ?> personas
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php
old_clear();
require __DIR__ . '/includes/footer.php';
