<?php
/**
 * Página de inicio: presentación, tarjetas informativas y ofertas del mes
 * (los platos destacados salen de la base de datos).
 */

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'BackFlash | Restaurante de comida colombiana en Pereira';
$metaDesc  = 'BackFlash: restaurante de comida colombiana en Pereira. Bandeja paisa, sancochos, espaguetis, jugos naturales y reservas en línea. Calle 28 # 6-27.';
$nav       = 'inicio';
$h1        = 'Comida colombiana en Pereira';
$h1sub     = 'Bandeja paisa, sancochos y jugos naturales, preparados al momento.';

$destacados = q(
    'SELECT * FROM platos WHERE activo = 1 AND destacado = 1 ORDER BY orden, nombre LIMIT 4'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

    <section class="information container">
        <h2 class="sr-only">Información del restaurante</h2>
        <div class="information-content">

            <div class="information-1">
                <div class="information-c1">
                    <h3>Reservaciones</h3>
                    <p>Reserva tu mesa con anticipación y disfruta de la mejor comida colombiana en un ambiente familiar. Atendemos grupos, celebraciones y eventos especiales.</p>
                    <a href="<?= e(url('reservar.php')) ?>" class="btn-1">Reservar mesa</a>
                </div>
                <div class="information-a1">
                    <img src="<?= e(url('public/assets/img/reservas.webp')) ?>"
                         alt="Comensales compartiendo una mesa en BackFlash"
                         width="1200" height="900" loading="lazy" decoding="async">
                </div>
            </div>

            <div class="information-2">
                <div class="information-b1">
                    <img src="<?= e(url('public/assets/img/salon.webp')) ?>"
                         alt="Salón del restaurante BackFlash, en el corazón de Pereira"
                         width="1600" height="1067" loading="lazy" decoding="async">
                </div>
                <div class="information-c1">
                    <h3>Nosotros</h3>
                    <p>En BackFlash cocinamos como en casa: platos colombianos preparados con ingredientes frescos de la región, recetas tradicionales y el sazón de siempre. Te esperamos en la calle 28 # 6-27, en el corazón de Pereira.</p>
                    <a href="<?= e(url('menu.php')) ?>" class="btn-1">Ver el menú</a>
                </div>
            </div>
        </div>
    </section>

    <section class="our">
        <div class="container">
            <h2>¿Qué ofrecemos?</h2>
            <span class="our-guide">Platos destacados del mes</span>
        </div>
    </section>

    <section class="oferta container">
        <div class="oferta-content">
            <h2 class="sr-only">Ofertas y platos destacados</h2>

            <?php foreach ($destacados as $i => $plato): ?>
                <?php $imagenTexto = ($i % 2 === 0); ?>
                <article class="oferta-1">
                    <?php if ($imagenTexto): ?>
                        <div class="oferta-txt">
                            <h3><?= e($plato['nombre']) ?></h3>
                            <p><?= e($plato['descripcion'] ?? 'Plato preparado al momento con ingredientes frescos.') ?></p>
                            <a href="<?= e(url('menu.php')) ?>" class="btn-2">Ver en el menú</a>
                        </div>
                        <div class="oferta-media">
                            <img src="<?= e(url($plato['imagen'])) ?>"
                                 alt="Foto de <?= e($plato['nombre']) ?>"
                                 width="900" height="675" loading="lazy" decoding="async">
                            <p class="oferta-precio"><?= e(precio($plato['precio'])) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="oferta-media">
                            <img src="<?= e(url($plato['imagen'])) ?>"
                                 alt="Foto de <?= e($plato['nombre']) ?>"
                                 width="900" height="675" loading="lazy" decoding="async">
                            <p class="oferta-precio"><?= e(precio($plato['precio'])) ?></p>
                        </div>
                        <div class="oferta-txt">
                            <h3><?= e($plato['nombre']) ?></h3>
                            <p><?= e($plato['descripcion'] ?? 'Plato preparado al momento con ingredientes frescos.') ?></p>
                            <a href="<?= e(url('menu.php')) ?>" class="btn-2">Ver en el menú</a>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>

            <?php if (!$destacados): ?>
                <div class="oferta-1">
                    <div class="oferta-txt">
                        <h3>Pronto tendremos novedades</h3>
                        <p>Nuestras ofertas del mes se publican aquí. Escríbenos y te avisamos.</p>
                        <a href="<?= e(url('contacto.php')) ?>" class="btn-2">Contacto</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php require __DIR__ . '/includes/footer.php';
