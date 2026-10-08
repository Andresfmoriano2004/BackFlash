<?php
/**
 * Resumen del panel: estadísticas y accesos rápidos.
 */

$tituloPagina = 'Resumen';
$paginaAdmin  = 'index';
require __DIR__ . '/_header.php';

$stats = q(
    'SELECT
        (SELECT COUNT(*) FROM mensajes)                          AS mensajes,
        (SELECT COUNT(*) FROM mensajes WHERE leido = 0)          AS sin_leer,
        (SELECT COUNT(*) FROM reservas WHERE estado = "pendiente") AS reservas_pendientes,
        (SELECT COUNT(*) FROM reservas WHERE fecha = CURDATE())  AS reservas_hoy,
        (SELECT COUNT(*) FROM reservas)                          AS reservas_total,
        (SELECT COUNT(*) FROM platos WHERE activo = 1)           AS platos_activos'
)->fetch();

$ultimosMensajes = q('SELECT * FROM mensajes ORDER BY creado DESC LIMIT 5')->fetchAll();
$proximasReservas = q(
    'SELECT * FROM reservas WHERE estado <> "cancelada" AND fecha >= CURDATE()
     ORDER BY fecha, hora LIMIT 6'
)->fetchAll();
?>

<section class="admin-cards">
    <a class="card" href="<?= e(url('admin/mensajes.php')) ?>">
        <span class="card-num"><?= (int) $stats['mensajes'] ?></span>
        <span class="card-txt">Mensajes
            <?php if ((int) $stats['sin_leer'] > 0): ?>
                <em class="badge badge-gold"><?= (int) $stats['sin_leer'] ?> sin leer</em>
            <?php endif; ?>
        </span>
    </a>

    <a class="card" href="<?= e(url('admin/reservas.php')) ?>">
        <span class="card-num"><?= (int) $stats['reservas_pendientes'] ?></span>
        <span class="card-txt">Reservas pendientes
            <em class="badge"><?= (int) $stats['reservas_hoy'] ?> hoy</em>
        </span>
    </a>

    <a class="card" href="<?= e(url('admin/reservas.php')) ?>">
        <span class="card-num"><?= (int) $stats['reservas_total'] ?></span>
        <span class="card-txt">Reservas totales</span>
    </a>

    <a class="card" href="<?= e(url('admin/platos.php')) ?>">
        <span class="card-num"><?= (int) $stats['platos_activos'] ?></span>
        <span class="card-txt">Platos publicados</span>
    </a>
</section>

<div class="admin-grid">
    <section class="panel">
        <h2>Últimos mensajes</h2>
        <?php if (!$ultimosMensajes): ?>
            <p class="vacio">Todavía no hay mensajes.</p>
        <?php else: ?>
            <ul class="lista">
                <?php foreach ($ultimosMensajes as $m): ?>
                    <li<?= $m['leido'] ? '' : ' class="sin-leer"' ?>>
                        <strong><?= e($m['nombre']) ?></strong>
                        <span class="lista-meta"><?= e($m['asunto']) ?> · <?= e(date('d/m H:i', strtotime($m['creado']))) ?></span>
                        <p><?= e(mb_strimwidth($m['mensaje'], 0, 110, '…')) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a class="btn-secundario" href="<?= e(url('admin/mensajes.php')) ?>">Ver todos los mensajes</a>
        <?php endif; ?>
    </section>

    <section class="panel">
        <h2>Próximas reservas</h2>
        <?php if (!$proximasReservas): ?>
            <p class="vacio">No hay reservas programadas.</p>
        <?php else: ?>
            <ul class="lista">
                <?php foreach ($proximasReservas as $r): ?>
                    <li>
                        <strong><?= e($r['nombre']) ?></strong>
                        <span class="lista-meta">
                            <?= e(date('d/m/Y', strtotime($r['fecha']))) ?> ·
                            <?= e(substr($r['hora'], 0, 5)) ?> ·
                            <?= (int) $r['personas'] ?> personas
                        </span>
                        <span class="badge badge-<?= e($r['estado']) ?>"><?= e(ucfirst($r['estado'])) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a class="btn-secundario" href="<?= e(url('admin/reservas.php')) ?>">Gestionar reservas</a>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/_footer.php';
