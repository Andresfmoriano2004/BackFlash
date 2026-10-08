<?php
/**
 * Pie de página compartido (cierra <main> y añade los scripts).
 */
?>
</main>

<footer>
    <div class="footer-content">
        <div class="footer-section about">
            <h2>Sobre Nosotros</h2>
            <p><?= e(SITE_NAME) ?> es un restaurante de comida colombiana ubicado en Pereira. Cocinamos con ingredientes frescos y recetas tradicionales para ofrecerte siempre la mejor mesa.</p>
        </div>

        <div class="footer-section links">
            <h2>Enlaces Útiles</h2>
            <ul>
                <li><a href="<?= e(url('index.php')) ?>">Inicio</a></li>
                <li><a href="<?= e(url('menu.php')) ?>">Menú</a></li>
                <li><a href="<?= e(url('reservar.php')) ?>">Reservas</a></li>
                <li><a href="<?= e(url('contacto.php')) ?>">Contacto</a></li>
            </ul>
        </div>

        <div class="footer-section contact">
            <h2>Contacto</h2>
            <p>Email: <?= e(SITE_EMAIL) ?></p>
            <p>Teléfono: <?= e(SITE_PHONE) ?></p>
            <p>Dirección: <?= e(SITE_ADDRESS) ?></p>
        </div>

        <div class="footer-section social-media">
            <h2>Redes Sociales</h2>
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">Facebook</a>
            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer">Twitter</a>
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">Instagram</a>
            <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer">LinkedIn</a>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= e(SITE_YEAR) ?> <?= e(SITE_NAME) ?>. Todos los derechos reservados.
            <a href="<?= e(url('admin/login.php')) ?>" class="footer-admin">Administración</a>
        </p>
    </div>
</footer>

<script src="<?= e(url('js/main.js')) ?>" defer></script>
</body>
</html>
