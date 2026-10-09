<?php
/**
 * Cabecera del panel de administración (requiere sesión iniciada).
 *
 * Variables: $tituloPagina, $paginaAdmin (clave del menú activo)
 */

require_once dirname(__DIR__) . '/includes/bootstrap.php';
requerir_admin();

$tituloPagina = $tituloPagina ?? 'Administración';
$paginaAdmin  = $paginaAdmin ?? 'index';

$enlacesAdmin = [
    'index'     => ['index.php',     'Resumen'],
    'mensajes'  => ['mensajes.php',  'Mensajes'],
    'reservas'  => ['reservas.php',  'Reservas'],
    'platos'    => ['platos.php',    'Menú'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Panel de administración de BackFlash: mensajes, reservas y menú.">
    <link rel="icon" href="<?= e(url('public/assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Satisfy&display=swap">
    <link rel="stylesheet" href="<?= e(url('public/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(url('public/css/admin.css')) ?>">
    <title><?= e($tituloPagina) ?> · BackFlash</title>
</head>
<body class="admin">

<a class="skip-link" href="#contenido">Saltar al contenido principal</a>

<header class="admin-top">
    <a class="admin-logo" href="<?= e(url('admin/index.php')) ?>">
        <img src="<?= e(url('public/assets/img/favicon.svg')) ?>" alt="" width="28" height="28">
        BackFlash <span>Admin</span>
    </a>

    <nav class="admin-nav" aria-label="Menú de administración">
        <?php foreach ($enlacesAdmin as $clave => [$href, $texto]): ?>
            <a href="<?= e(url('admin/' . $href)) ?>"<?= $paginaAdmin === $clave ? ' aria-current="page"' : '' ?>>
                <?= e($texto) ?>
            </a>
        <?php endforeach; ?>
        <a href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener">Ver sitio ↗</a>
        <a href="<?= e(url('admin/logout.php')) ?>" class="admin-salir">Salir</a>
    </nav>
</header>

<main id="contenido" class="admin-main" tabindex="-1">
<?php foreach (flash_get() as $alerta): ?>
    <div class="alert alert-<?= e($alerta['tipo']) ?>" role="status">
        <div class="container"><?= e($alerta['texto']) ?></div>
    </div>
<?php endforeach; ?>
