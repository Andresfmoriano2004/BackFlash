<?php
/**
 * Formulario de contacto.
 * Los datos llegan por POST a acciones/contacto.php y aquí se muestran
 * los resultados (tanto los mensajes flash como los errores por campo).
 */

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Contacto - BackFlash';
$metaDesc  = 'Contacta a BackFlash: envíanos tu mensaje o haz reservaciones. Calle 28 # 6-27, Pereira, Colombia.';
$nav       = 'contacto';
$h1        = 'Contáctanos';
$h1sub     = 'Te responderemos pronto';

require __DIR__ . '/includes/header.php';
?>

    <section class="contact-form container">
        <div class="container1">
            <h2>Formulario de Contacto</h2>

            <form action="<?= e(url('acciones/contacto.php')) ?>" method="post" id="contactForm" class="js-form" novalidate>
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" id="name" name="nombre" autocomplete="name" required minlength="3"
                           value="<?= e(old('nombre')) ?>"<?= aria_invalido('nombre') ?>
                           aria-describedby="error-nombre">
                    <?php mostrar_error('nombre'); ?>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" autocomplete="email" required
                           value="<?= e(old('email')) ?>"<?= aria_invalido('email') ?>
                           aria-describedby="error-email">
                    <?php mostrar_error('email'); ?>
                </div>

                <div class="form-group">
                    <label for="asunto">Asunto</label>
                    <input type="text" id="asunto" name="asunto" required minlength="3"
                           value="<?= e(old('asunto')) ?>"<?= aria_invalido('asunto') ?>
                           aria-describedby="error-asunto">
                    <?php mostrar_error('asunto'); ?>
                </div>

                <div class="form-group">
                    <label for="message">Mensaje</label>
                    <textarea id="message" name="mensaje" required minlength="10"<?= aria_invalido('mensaje') ?>
                              aria-describedby="error-mensaje"><?= e(old('mensaje')) ?></textarea>
                    <?php mostrar_error('mensaje'); ?>
                </div>

                <div class="form-group">
                    <button type="submit">Enviar</button>
                </div>
            </form>

            <div class="contacto-datos">
                <h3>También puedes encontrarnos en</h3>
                <ul>
                    <li><strong>Dirección:</strong> <?= e(SITE_ADDRESS) ?></li>
                    <li><strong>Teléfono:</strong> <?= e(SITE_PHONE) ?></li>
                    <li><strong>Email:</strong> <?= e(SITE_EMAIL) ?></li>
                    <li><strong>Horario:</strong> martes a domingo, 11:00 a. m. – 10:00 p. m.</li>
                </ul>
            </div>
        </div>
    </section>

<?php
old_clear();
require __DIR__ . '/includes/footer.php';
