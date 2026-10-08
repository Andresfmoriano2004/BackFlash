<?php
/**
 * Mensajes del formulario de contacto: lectura, marcado y borrado.
 */

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requerir_admin();

$tituloPagina = 'Mensajes';
$paginaAdmin  = 'mensajes';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $id     = (int) ($_POST['id'] ?? 0);
    $accion = (string) ($_POST['accion'] ?? '');

    if ($id > 0 && $accion === 'leer') {
        q('UPDATE mensajes SET leido = 1 WHERE id = ?', [$id]);
        flash('ok', 'Mensaje marcado como leído.');
    } elseif ($id > 0 && $accion === 'no-leer') {
        q('UPDATE mensajes SET leido = 0 WHERE id = ?', [$id]);
        flash('ok', 'Mensaje marcado como no leído.');
    } elseif ($id > 0 && $accion === 'borrar') {
        q('DELETE FROM mensajes WHERE id = ?', [$id]);
        flash('ok', 'Mensaje eliminado.');
    }

    redirect('admin/mensajes.php');
}

require __DIR__ . '/_header.php';

$mensajes = q('SELECT * FROM mensajes ORDER BY leido ASC, creado DESC')->fetchAll();
?>

<section class="panel">
    <div class="panel-cabecera">
        <h2>Mensajes recibidos (<?= count($mensajes) ?>)</h2>
    </div>

    <?php if (!$mensajes): ?>
        <p class="vacio">No hay mensajes todavía. Cuando alguien use el formulario de <a href="<?= e(url('contacto.php')) ?>">contacto</a>, aparecerá aquí.</p>
    <?php else: ?>
        <div class="tabla-scroll">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Nombre / Email</th>
                        <th>Asunto</th>
                        <th>Mensaje</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mensajes as $m): ?>
                        <tr<?= $m['leido'] ? '' : ' class="sin-leer"' ?>>
                            <td data-label="Fecha"><?= e(date('d/m/Y H:i', strtotime($m['creado']))) ?></td>
                            <td data-label="Nombre">
                                <strong><?= e($m['nombre']) ?></strong><br>
                                <a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a>
                            </td>
                            <td data-label="Asunto"><?= e($m['asunto']) ?></td>
                            <td data-label="Mensaje" class="celda-msg"><?= nl2br(e($m['mensaje'])) ?></td>
                            <td data-label="Estado">
                                <span class="badge badge-<?= $m['leido'] ? 'confirmada' : 'pendiente' ?>">
                                    <?= $m['leido'] ? 'Leído' : 'Nuevo' ?>
                                </span>
                            </td>
                            <td data-label="Acciones">
                                <form method="post" class="form-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                                    <input type="hidden" name="accion" value="<?= $m['leido'] ? 'no-leer' : 'leer' ?>">
                                    <button type="submit" class="btn-mini"><?= $m['leido'] ? 'No leído' : 'Marcar leído' ?></button>
                                </form>
                                <form method="post" class="form-inline" data-confirm="¿Borrar este mensaje?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
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
