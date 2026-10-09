<?php
/**
 * Carta del restaurante: los platos se leen de la base de datos
 * y se agrupan en "platos" y "bebidas".
 */

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Menú - BackFlash';
$metaDesc  = 'Menú de BackFlash: bandeja paisa, estofado, sancochos, espaguetis y jugos naturales. Comida colombiana en Pereira.';
$nav       = 'menu';
$h1        = 'Menú de la casa';
$h1sub     = 'Platos fuertes y jugos naturales, preparados al momento con ingredientes de la región.';

$platos = q(
    'SELECT * FROM platos WHERE activo = 1 ORDER BY orden, nombre'
)->fetchAll();

$grupos = ['plato' => [], 'bebida' => []];
foreach ($platos as $plato) {
    $grupos[$plato['categoria']][] = $plato;
}

require __DIR__ . '/includes/header.php';
?>

    <section class="food container">
        <div class="food-cab">
            <h2>Menú</h2>
            <span>Comida colombiana · Pereira</span>
        </div>

        <?php if (!$platos): ?>
            <p class="vacio">El menú está vacío. Entra al <a href="<?= e(url('admin/login.php')) ?>">panel de administración</a> para agregar platos.</p>
        <?php else: ?>
            <nav class="indice" aria-label="Índice de categorías del menú">
                <a href="#cat-platos">Platos fuertes</a>
                <a href="#cat-bebidas">Bebidas</a>
            </nav>

            <div class="food-content">
                <?php foreach (['plato' => 'Platos fuertes', 'bebida' => 'Bebidas'] as $clave => $titulo): ?>
                    <div class="left">
                        <h2 class="food-titulo" id="<?= $clave === 'plato' ? 'cat-platos' : 'cat-bebidas' ?>"><?= e($titulo) ?></h2>
                        <?php foreach ($grupos[$clave] as $plato): ?>
                            <article class="food-1">
                                <div class="food-media">
                                    <?php if ($plato['imagen']): ?>
                                        <img src="<?= e(url($plato['imagen'])) ?>"
                                             alt="Foto de <?= e($plato['nombre']) ?>"
                                             width="900" height="619" loading="lazy" decoding="async">
                                    <?php endif; ?>
                                    <p class="precio"><?= e(precio($plato['precio'])) ?></p>
                                </div>
                                <h3><?= e($plato['nombre']) ?></h3>
                                <?php if (!empty($plato['descripcion'])): ?>
                                    <p class="food-desc"><?= e($plato['descripcion']) ?></p>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>

                        <?php if (!$grupos[$clave]): ?>
                            <p class="vacio">No hay <?= e(strtolower($titulo)) ?> disponibles por ahora.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="food-cta">
            <a href="<?= e(url('reservar.php')) ?>" class="btn-1">Reservar una mesa</a>
        </div>
    </section>

<?php require __DIR__ . '/includes/footer.php';
