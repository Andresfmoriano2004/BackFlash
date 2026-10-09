<?php
/**
 * Cabecera compartida: <head>, menú de navegación y hero.
 *
 * Variables que puede definir la página antes de incluirlo:
 *   $pageTitle  (string) título de la pestaña
 *   $metaDesc   (string) meta description
 *   $nav        (string) clave del enlace activo: inicio | menu | reservas | contacto
 *   $h1         (string) título del hero
 *   $h1sub      (string) subtítulo del hero
 */

$pageTitle = $pageTitle ?? SITE_NAME;
$metaDesc  = $metaDesc ?? 'Restaurante de comida colombiana en Pereira.';
$nav       = $nav ?? 'inicio';
$h1        = $h1 ?? 'El mejor menú';
$h1sub     = $h1sub ?? 'Mejor comida colombiana';

$enlaces = [
    'inicio'   => ['index.php',    'Inicio'],
    'menu'     => ['menu.php',     'Menú'],
    'reservas' => ['reservar.php', 'Reservas'],
    'contacto' => ['contacto.php', 'Contacto'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDesc) ?>">
    <meta name="author" content="<?= e(SITE_NAME) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($metaDesc) ?>">
    <meta property="og:image" content="<?= e(url('public/assets/img/favicon.svg')) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="<?= e(url('public/assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(url('public/css/style.css')) ?>">
    <title><?= e($pageTitle) ?></title>
</head>
<body>
<header class="header">
    <div class="menu container">
        <a href="<?= e(url('index.php')) ?>" class="logo"><?= e(SITE_NAME) ?></a>
        <input type="checkbox" id="menu" aria-label="Mostrar u ocultar el menú de navegación">
        <label for="menu">
            <img src="<?= e(url('public/assets/img/logo.svg')) ?>" class="menu-icono" alt="Menú de navegación">
        </label>
        <nav class="navbar" id="nav-principal" aria-label="Navegación principal">
            <ul>
                <?php foreach ($enlaces as $clave => [$href, $texto]): ?>
                    <li>
                        <a href="<?= e(url($href)) ?>"<?= $nav === $clave ? ' aria-current="page"' : '' ?>>
                            <?= e($texto) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
    <div class="header-container">
        <div class="header-txt">
            <h1><?= e($h1) ?></h1>
            <p><?= e($h1sub) ?></p>
            <div class="header-cta">
                <a href="<?= e(url('reservar.php')) ?>" class="btn-1 btn-solid">Reservar mesa</a>
                <a href="<?= e(url('menu.php')) ?>" class="btn-1">Ver el menú</a>
            </div>
        </div>
    </div>
</header>

<main>
<?php foreach (flash_get() as $alerta): ?>
    <div class="alert alert-<?= e($alerta['tipo']) ?>" role="status">
        <div class="container"><?= e($alerta['texto']) ?></div>
    </div>
<?php endforeach; ?>
