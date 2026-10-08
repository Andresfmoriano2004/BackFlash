<?php
/**
 * Carta del restaurante: los platos se leen de la base de datos
 * y se agrupan en "platos" y "bebidas".
 */

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Menú - BackFlash';
$metaDesc  = 'Menú de BackFlash: bandeja paisa, estofado, sancochos, espaguetis y jugos naturales. Comida colombiana en Pereira.';
$nav       = 'menu';
$h1        = 'El mejor menú';
$h1sub     = 'Mejor comida colombiana';

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
        <h2>Menú</h2>
        <span>Comida colombiana</span>

        <?php if (!$platos): ?>
            <p class="vacio">El menú está vacío. Entra al <a href="<?= e(url('admin/login.php')) ?>">panel de administración</a> para agregar platos.</p>
        <?php else: ?>
            <div class="food-content">
                <?php foreach (['plato' => 'Platos fuertes', 'bebida' => 'Bebidas'] as $clave => $titulo): ?>
                    <div class="left">
                        <h3 class="food-titulo"><?= e($titulo) ?></h3>
                        <?php foreach ($grupos[$clave] as $plato): ?>
                            <article class="food-1">
                                <h3><?= e($plato['nombre']) ?></h3>
                                <div class="food-txt">
                                    <?php if ($plato['imagen']): ?>
                                        <img src="<?= e(url($plato['imagen'])) ?>" alt="Ilustración de <?= e($plato['nombre']) ?>" loading="lazy">
                                    <?php endif; ?>
                                    <p class="precio"><?= e(precio($plato['precio'])) ?></p>
                                    <?php if (!empty($plato['descripcion'])): ?>
                                        <p class="food-desc"><?= e($plato['descripcion']) ?></p>
                                    <?php endif; ?>
                                </div>
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
