<?php
/**
 * Acceso al panel de administración.
 * Usuario: admin   Contraseña: BackFlash2026!
 */

require_once __DIR__ . '/../includes/bootstrap.php';

if (admin_logueado()) {
    redirect('admin/index.php');
}

$error  = '';
$usuario = old('usuario');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $usuario = post_str('usuario', 60);
    $clave   = (string) ($_POST['clave'] ?? '');

    $fila = q('SELECT * FROM usuarios WHERE usuario = ?', [$usuario])->fetch();

    if ($fila && password_verify($clave, $fila['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = $fila['usuario'];
        unset($_SESSION['csrf']);
        flash('ok', 'Bienvenido, ' . $fila['usuario'] . '.');
        redirect('admin/index.php');
    }

    // Contraseña incorrecta: mensaje genérico (no revelamos qué dato falló)
    usleep(300000);
    $error = 'Usuario o contraseña incorrectos.';
    old_set(['usuario' => $usuario]);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Acceso al panel de administración de BackFlash: gestión de mensajes, reservas y menú.">
    <link rel="icon" href="<?= e(url('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(url('style.css')) ?>">
    <link rel="stylesheet" href="<?= e(url('admin/admin.css')) ?>">
    <title>Acceso · BackFlash</title>
</head>
<body class="admin admin-login-body">

<div class="admin-login">
    <img class="admin-login-logo" src="<?= e(url('img/favicon.svg')) ?>" alt="" width="56" height="56">
    <h1>BackFlash <span>Admin</span></h1>
    <p class="admin-login-sub">Gestión de mensajes, reservas y menú</p>

    <?php foreach (flash_get() as $alerta): ?>
        <div class="alert alert-<?= e($alerta['tipo']) ?>" role="status"><?= e($alerta['texto']) ?></div>
    <?php endforeach; ?>

    <?php if ($error): ?>
        <div class="alert alert-error" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('admin/login.php')) ?>">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" autocomplete="username" required
                   value="<?= e($usuario) ?>" autofocus>
        </div>

        <div class="form-group">
            <label for="clave">Contraseña</label>
            <input type="password" id="clave" name="clave" autocomplete="current-password" required>
        </div>

        <div class="form-group">
            <button type="submit">Entrar</button>
        </div>
    </form>

    <p class="admin-login-volver"><a href="<?= e(url('index.php')) ?>">← Volver al sitio</a></p>
</div>

</body>
</html>
