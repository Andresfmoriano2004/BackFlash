<?php
/**
 * Cabecera compartida: <head>, barra superior, hero y franja de datos.
 *
 * Variables que puede definir la página antes de incluirlo:
 *   $pageTitle  (string) título de la pestaña
 *   $metaDesc   (string) meta description
 *   $nav        (string) clave del enlace activo: inicio | menu | reservas | contacto
 *   $h1         (string) título del hero
 *   $h1sub      (string) línea de apoyo del hero
 */

use BackFlash\Validation\Reglas;

$pageTitle = $pageTitle ?? SITE_NAME;
$metaDesc  = $metaDesc ?? 'Restaurante de comida colombiana en Pereira.';
$nav       = $nav ?? 'inicio';
$h1        = $h1 ?? 'Comida colombiana en Pereira';
$h1sub     = $h1sub ?? 'Bandeja paisa, sancochos y jugos naturales, hechos al momento.';

$enlaces = [
    'inicio'   => ['index.php',    'Inicio'],
    'menu'     => ['menu.php',     'Menú'],
    'reservas' => ['reservar.php', 'Reservas'],
    'contacto' => ['contacto.php', 'Contacto'],
];

/* --------------------------------------------------------------
   Cifras de la casa: los números encendidos de la banca de tubos.
   Se calculan aquí para que las cuatro páginas públicas enseñen
   siempre lo mismo. Los límites salen de Reglas (misma fuente que
   valida el formulario) y el precio mínimo de la propia base.
   -------------------------------------------------------------- */
$abre   = (int) substr(Reglas::HORA_APERTURA, 0, 2);
$cierra = (int) substr(Reglas::HORA_CIERRE, 0, 2);
$esLunes = ((int) date('N') === 1);
$horaAhora = (int) date('G');

$tuboHoy = match (true) {
    $esLunes            => 'Cerrado',
    $horaAhora >= $abre && $horaAhora < $cierra => 'Hasta ' . sprintf('%02d:00', $cierra),
    default             => 'Desde ' . sprintf('%02d:00', $abre),
};

$tuboMesa = Reglas::PERSONAS_MIN . '–' . Reglas::PERSONAS_MAX;

$precioMin = (int) (q('SELECT MIN(precio) FROM platos WHERE activo = 1')->fetchColumn() ?: 0);
$tuboDesde = $precioMin > 0 ? '$' . number_format($precioMin, 0, ',', '.') : '—';
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
    <meta property="og:image" content="<?= e(url('public/assets/img/hero.webp')) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="<?= e(url('public/assets/img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Satisfy&display=swap">
    <link rel="stylesheet" href="<?= e(url('public/css/style.css')) ?>">
    <title><?= e($pageTitle) ?></title>
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido principal</a>
<div class="topbar">
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
        <a href="<?= e(url('reservar.php')) ?>" class="nav-cta">Reservar mesa</a>
    </div>
</div>

<header class="header">
    <div class="hero container">
        <div class="hero-main">
            <div class="hero-txt">
                <h1><?= e($h1) ?></h1>
                <p class="hero-sub"><?= e($h1sub) ?></p>

                <dl class="tube-bank">
                    <div class="tube">
                        <dt class="tube-label">Hoy</dt>
                        <dd><?= e($tuboHoy) ?></dd>
                    </div>
                    <div class="tube">
                        <dt class="tube-label">Mesa</dt>
                        <dd><?= e($tuboMesa) ?></dd>
                    </div>
                    <div class="tube">
                        <dt class="tube-label">Desde</dt>
                        <dd><?= e($tuboDesde) ?></dd>
                    </div>
                </dl>

                <div class="header-cta">
                    <a href="<?= e(url('reservar.php')) ?>" class="btn-1 btn-solid">Reservar mesa</a>
                    <a href="<?= e(url('menu.php')) ?>" class="btn-1">Ver el menú</a>
                </div>
            </div>

            <figure class="hero-plate">
                <img src="<?= e(url('public/assets/img/hero.webp')) ?>"
                     alt="Salón de BackFlash iluminado al anochecer, con las mesas servidas"
                     width="1600" height="1067" fetchpriority="high" decoding="async">
            </figure>
        </div>

        <p class="hero-strip">
            <span><b>Ubicación</b><?= e(SITE_ADDRESS) ?></span>
            <span><b>Horario</b>mar–dom · 11:00–22:00</span>
            <span><b>Teléfono</b><?= e(SITE_PHONE) ?></span>
        </p>
    </div>
</header>

<main id="contenido" tabindex="-1">
<?php foreach (flash_get() as $alerta): ?>
    <div class="alert alert-<?= e($alerta['tipo']) ?>" role="status">
        <div class="container"><?= e($alerta['texto']) ?></div>
    </div>
<?php endforeach; ?>
