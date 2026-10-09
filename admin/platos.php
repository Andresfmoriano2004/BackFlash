<?php
/**
 * Alta, edición y borrado de platos del menú (con subida de imagen).
 */

use BackFlash\Validation\PlatoValidator;
use BackFlash\Validation\Reglas;

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requerir_admin();

$tituloPagina = 'Menú';
$paginaAdmin  = 'platos';

/** Guarda una imagen subida y devuelve la ruta relativa (o null). */
function subir_imagen(): ?string
{
    if (empty($_FILES['imagen']['name'])) {
        return null;
    }

    if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No se pudo subir el archivo (error ' . $_FILES['imagen']['error'] . ').');
    }
    if ($_FILES['imagen']['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('La imagen supera el máximo de 3 MB.');
    }

    $permitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($_FILES['imagen']['tmp_name']);

    if (!isset($permitidos[$mime])) {
        throw new RuntimeException('Formato no permitido: usa JPG, PNG o WEBP.');
    }

    $destino = 'public/assets/img/subidas/' . uniqid('plato_', true) . '.' . $permitidos[$mime];
    $ruta    = dirname(__DIR__) . '/' . $destino;

    if (!is_dir(dirname($ruta))) {
        mkdir(dirname($ruta), 0775, true);
    }
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
        throw new RuntimeException('No se pudo guardar la imagen en el servidor.');
    }

    return $destino;
}

/* ---------------------------------------------------------------
 * Acciones POST
 * ------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $id     = (int) ($_POST['id'] ?? 0);
    $accion = (string) ($_POST['accion'] ?? '');

    if ($accion === 'borrar') {
        $plato = q('SELECT imagen FROM platos WHERE id = ?', [$id])->fetch();
        if ($plato) {
            q('DELETE FROM platos WHERE id = ?', [$id]);
            if ($plato['imagen'] && (str_starts_with($plato['imagen'], 'img/subidas/') || str_starts_with($plato['imagen'], 'public/assets/img/subidas/'))) {
                $fichero = dirname(__DIR__) . '/' . $plato['imagen'];
                if (is_file($fichero)) {
                    @unlink($fichero);
                }
            }
            flash('ok', 'Plato eliminado del menú.');
        }
        redirect('admin/platos.php');
    }

    if ($accion === 'guardar') {
        $nombre      = post_str('nombre', Reglas::NOMBRE_MAX);
        $descripcion = post_str('descripcion', Reglas::PLATO_DESC_MAX);
        $precio      = post_str('precio', 12);
        $categoria   = post_str('categoria', 10);
        $orden       = (int) ($_POST['orden'] ?? 0);
        $destacado   = isset($_POST['destacado']) ? 1 : 0;
        $activo      = isset($_POST['activo']) ? 1 : 0;

        // Las reglas viven en src/Validation/PlatoValidator.php (fuente única).
        $validador = new PlatoValidator();
        $validador->validar([
            'nombre'    => $nombre,
            'precio'    => $precio,
            'categoria' => $categoria,
        ]);

        $imagenActual = $id > 0 ? (string) (q('SELECT imagen FROM platos WHERE id = ?', [$id])->fetch()['imagen'] ?? '') : '';
        $imagenNueva  = null;

        try {
            if (!empty($_FILES['imagen']['name'])) {
                $imagenNueva = subir_imagen();
            }
        } catch (RuntimeException $e) {
            $validador->agregarError('imagen', $e->getMessage());
        }

        if (isset($_POST['quitar_imagen'])) {
            $imagenNueva = '';
            $imagenActual = '';
        }

        $errores = $validador->errores();

        if ($errores) {
            old_set([
                'id'         => (string) $id,
                'nombre'     => $nombre,
                'descripcion'=> $descripcion,
                'precio'     => $precio,
                'categoria'  => $categoria,
                'orden'      => (string) $orden,
                'destacado'  => $destacado ? '1' : '',
                'activo'     => $activo ? '1' : '',
                '__errores'  => $errores,
            ]);
            flash('error', 'Revisa los campos marcados.');
            redirect($id > 0 ? 'admin/platos.php?editar=' . $id : 'admin/platos.php');
        }

        $rutaImagen = $imagenNueva ?? $imagenActual;
        $precioNum  = (float) str_replace(',', '.', $precio);

        if ($id > 0) {
            q(
                'UPDATE platos SET nombre = ?, descripcion = ?, precio = ?, categoria = ?,
                 imagen = ?, destacado = ?, activo = ?, orden = ? WHERE id = ?',
                [$nombre, $descripcion, $precioNum, $categoria, $rutaImagen, $destacado, $activo, $orden, $id]
            );
            flash('ok', 'Plato actualizado correctamente.');
        } else {
            q(
                'INSERT INTO platos (nombre, descripcion, precio, categoria, imagen, destacado, activo, orden)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [$nombre, $descripcion, $precioNum, $categoria, $rutaImagen, $destacado, $activo, $orden]
            );
            flash('ok', 'Plato agregado al menú.');
        }

        redirect('admin/platos.php');
    }
}

/* ---------------------------------------------------------------
 * Datos para la vista
 * ------------------------------------------------------------- */
require __DIR__ . '/_header.php';

$editar = null;
if (!empty($_GET['editar'])) {
    $editar = q('SELECT * FROM platos WHERE id = ?', [(int) $_GET['editar']])->fetch() ?: null;
    if (!$editar) {
        flash('error', 'No se encontró el plato solicitado.');
        redirect('admin/platos.php');
    }
}

$platos = q('SELECT * FROM platos ORDER BY categoria, orden, nombre')->fetchAll();
?>

<section class="admin-grid admin-grid-form">
    <div class="panel">
        <h2><?= $editar ? 'Editar plato' : 'Agregar plato o bebida' ?></h2>

        <form method="post" enctype="multipart/form-data" class="form-admin">
            <?= csrf_field() ?>
            <input type="hidden" name="accion" value="guardar">
            <input type="hidden" name="id" value="<?= (int) ($editar['id'] ?? old('id')) ?>">

            <div class="form-group">
                <label for="p-nombre">Nombre *</label>
                <input type="text" id="p-nombre" name="nombre" required maxlength="<?= Reglas::NOMBRE_MAX ?>"
                       value="<?= e($editar['nombre'] ?? old('nombre')) ?>"<?= aria_invalido('nombre') ?>>
                <?php mostrar_error('nombre'); ?>
            </div>

            <div class="form-group">
                <label for="p-desc">Descripción</label>
                <textarea id="p-desc" name="descripcion" rows="2" maxlength="<?= Reglas::PLATO_DESC_MAX ?>"><?= e($editar['descripcion'] ?? old('descripcion')) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="p-precio">Precio (COP) *</label>
                    <input type="text" id="p-precio" name="precio" required inputmode="decimal"
                           placeholder="20000" value="<?= e($editar['precio'] ?? old('precio')) ?>"<?= aria_invalido('precio') ?>>
                    <?php mostrar_error('precio'); ?>
                </div>

                <div class="form-group">
                    <label for="p-cat">Categoría *</label>
                    <select id="p-cat" name="categoria" required<?= aria_invalido('categoria') ?>>
                        <option value="plato"<?= ($editar['categoria'] ?? old('categoria', 'plato')) === 'plato' ? ' selected' : '' ?>>Plato fuerte</option>
                        <option value="bebida"<?= ($editar['categoria'] ?? '') === 'bebida' ? ' selected' : '' ?>>Bebida</option>
                    </select>
                    <?php mostrar_error('categoria'); ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="p-orden">Orden</label>
                    <input type="number" id="p-orden" name="orden" min="0" max="<?= Reglas::PLATO_ORDEN_MAX ?>"
                           value="<?= (int) ($editar['orden'] ?? old('orden', 0)) ?>">
                </div>

                <div class="form-group">
                    <label for="p-img">Imagen (JPG, PNG o WEBP · máx. 3 MB)</label>
                    <input type="file" id="p-img" name="imagen" accept="image/jpeg,image/png,image/webp">
                    <?php mostrar_error('imagen'); ?>
                    <?php if (!empty($editar['imagen'])): ?>
                        <label class="check"><input type="checkbox" name="quitar_imagen"> Quitar la imagen actual</label>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <label class="check"><input type="checkbox" name="destacado" value="1"
                    <?= !empty($editar['destacado']) || old('destacado') === '1' ? 'checked' : '' ?>> Destacar en la portada</label>
                <label class="check"><input type="checkbox" name="activo" value="1"
                    <?= $editar ? (!empty($editar['activo']) ? 'checked' : '') : 'checked' ?>> Visible en el menú</label>
            </div>

            <div class="form-actions">
                <button type="submit"><?= $editar ? 'Guardar cambios' : 'Agregar al menú' ?></button>
                <?php if ($editar): ?>
                    <a class="btn-secundario" href="<?= e(url('admin/platos.php')) ?>">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="panel">
        <h2>Menú actual (<?= count($platos) ?>)</h2>

        <?php if (!$platos): ?>
            <p class="vacio">Aún no hay platos. Agrega el primero con el formulario.</p>
        <?php else: ?>
            <div class="tabla-scroll">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Plato</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($platos as $p): ?>
                            <tr<?= $p['activo'] ? '' : ' class="inactivo"' ?>>
                                <td data-label="Plato">
                                    <?php if ($p['imagen']): ?>
                                        <img class="mini" src="<?= e(url($p['imagen'])) ?>" alt="" width="44" height="34" loading="lazy">
                                    <?php endif; ?>
                                    <?= e($p['nombre']) ?>
                                    <?php if ($p['destacado']): ?><span class="badge badge-gold">Destacado</span><?php endif; ?>
                                </td>
                                <td data-label="Categoría"><?= $p['categoria'] === 'bebida' ? 'Bebida' : 'Plato' ?></td>
                                <td data-label="Precio"><?= e(precio($p['precio'])) ?></td>
                                <td data-label="Estado">
                                    <span class="badge badge-<?= $p['activo'] ? 'confirmada' : 'cancelada' ?>">
                                        <?= $p['activo'] ? 'Visible' : 'Oculto' ?>
                                    </span>
                                </td>
                                <td data-label="Acciones">
                                    <a class="btn-mini" href="<?= e(url('admin/platos.php?editar=' . (int) $p['id'])) ?>">Editar</a>
                                    <form method="post" class="form-inline" data-confirm="¿Borrar <?= e($p['nombre']) ?> del menú?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="accion" value="borrar">
                                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                        <button type="submit" class="btn-mini btn-peligro">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/_footer.php';
