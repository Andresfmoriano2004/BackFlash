<?php
/**
 * Reservas: cambio de estado, filtrado y borrado.
 */

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requerir_admin();

$tituloPagina = 'Reservas';
$paginaAdmin  = 'reservas';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $id     = (int) ($_POST['id'] ?? 0);
    $accion = (string) ($_POST['accion'] ?? '');

    if ($id > 0 && in_array($accion, ['confirmada', 'cancelada', 'pendiente'], true)) {
        q('UPDATE reservas SET estado = ? WHERE id = ?', [$accion, $id]);
        flash('ok', 'Reserva actualizada a "' . $accion . '".');
    } elseif ($id > 0 && $accion === 'borrar') {
        q('DELETE FROM reservas WHERE id = ?', [$id]);
        flash('ok', 'Reserva eliminada.');
    }

    redirect('admin/reservas.php');
}

require __DIR__ . '/_header.php';

$filtro = (string) ($_GET['estado'] ?? '');
$donde  = '';
$params = [];

if (in_array($filtro, ['pendiente', 'confirmada', 'cancelada'], true)) {
    $donde  = ' WHERE estado = ?';
    $params = [$filtro];
}

$reservas = q(
    'SELECT * FROM reservas' . $donde . ' ORDER BY fecha DESC, hora DESC',
    $params
)->fetchAll();

$conteos = q(
    'SELECT estado, COUNT(*) AS n FROM reservas GROUP BY estado'
)->fetchAll();
$porEstado = array_column($conteos, 'n', 'estado');
?>

<section class="panel">
    <div class="panel-cabecera">
        <h2>Reservas (<?= count($reservas) ?>)</h2>
        <nav class="filtros" aria-label="Filtrar por estado">
            <a href="<?= e(url('admin/reservas.php')) ?>" class="<?= $filtro === '' ? 'activo' : '' ?>">Todas</a>
            <a href="<?= e(url('admin/reservas.php?estado=pendiente')) ?>" class="<?= $filtro === 'pendiente' ? 'activo' : '' ?>">
                Pendientes (<?= (int) ($porEstado['pendiente'] ?? 0) ?>)
            </a>
            <a href="<?= e(url('admin/reservas.php?estado=confirmada')) ?>" class="<?= $filtro === 'confirmada' ? 'activo' : '' ?>">
                Confirmadas (<?= (int) ($porEstado['confirmada'] ?? 0) ?>)
            </a>
            <a href="<?= e(url('admin/reservas.php?estado=cancelada')) ?>" class="<?= $filtro === 'cancelada' ? 'activo' : '' ?>">
                Canceladas (<?= (int) ($porEstado['cancelada'] ?? 0) ?>)
            </a>
        </nav>
    </div>

    <?php if (!$reservas): ?>
        <p class="vacio">No hay reservas<?= $filtro ? ' con el filtro "' . e($filtro) . '"' : '' ?>.</p>
    <?php else: ?>
        <div class="tabla-scroll">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Fecha y hora</th>
                        <th>Cliente</th>
                        <th>Personas</th>
                        <th>Comentarios</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $r): ?>
                        <tr>
                            <td data-label="Fecha">
                                <strong><?= e(date('d/m/Y', strtotime($r['fecha']))) ?></strong><br>
                                <?= e(substr($r['hora'], 0, 5)) ?>
                            </td>
                            <td data-label="Cliente">
                                <strong><?= e($r['nombre']) ?></strong><br>
                                <a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a><br>
                                <?= e($r['telefono']) ?>
                            </td>
                            <td data-label="Personas"><?= (int) $r['personas'] ?></td>
                            <td data-label="Comentarios" class="celda-msg"><?= e($r['mensaje'] ?: '—') ?></td>
                            <td data-label="Estado">
                                <span class="badge badge-<?= e($r['estado']) ?>"><?= e(ucfirst($r['estado'])) ?></span>
                            </td>
                            <td data-label="Acciones">
                                <form method="post" class="form-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <select name="accion" aria-label="Nuevo estado de la reserva de <?= e($r['nombre']) ?>">
                                        <option value="pendiente"<?= $r['estado'] === 'pendiente' ? ' selected' : '' ?>>Pendiente</option>
                                        <option value="confirmada"<?= $r['estado'] === 'confirmada' ? ' selected' : '' ?>>Confirmada</option>
                                        <option value="cancelada"<?= $r['estado'] === 'cancelada' ? ' selected' : '' ?>>Cancelada</option>
                                    </select>
                                    <button type="submit" class="btn-mini">Guardar</button>
                                </form>
                                <form method="post" class="form-inline" data-confirm="¿Borrar esta reserva?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                    <input type="hidden" name="accion" value="borrar">
                                    <button type="submit" class="btn-mini btn-peligro">Borrar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/_footer.php';
