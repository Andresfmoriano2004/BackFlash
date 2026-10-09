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
        <span class="credito">
            Fotografías:
            <a href="https://commons.wikimedia.org/wiki/File:Bandeja_paisa_%285082434401%29.jpg" target="_blank" rel="noopener noreferrer">bandeja paisa</a> © Jorge Lascar (CC BY 2.0) ·
            <a href="https://commons.wikimedia.org/wiki/File:Estofado_de_carne.jpg" target="_blank" rel="noopener noreferrer">estofado</a> © PapiPijuan (CC BY-SA 4.0) ·
            <a href="https://commons.wikimedia.org/wiki/File:Sancocho_cruzado_de_gallina%2C_rabo_y_costilla_con_arepa.jpg" target="_blank" rel="noopener noreferrer">sancocho de pollo</a> © Rodolfo Pimentel (CC BY-SA 4.0) ·
            <a href="https://commons.wikimedia.org/wiki/File:Sancocho_de_pescado_%28gastronom%C3%ADa_Ecuatoriana%29.jpg" target="_blank" rel="noopener noreferrer">sancocho de pescado</a> © Kevinmero (CC BY-SA 4.0) ·
            <a href="https://commons.wikimedia.org/wiki/File:Jugo_de_lulo.jpg" target="_blank" rel="noopener noreferrer">jugo de lulo</a> © Caldobasico (CC BY-SA 4.0) ·
            <a href="https://www.flickr.com/photos/38102750@N06/8734808247" target="_blank" rel="noopener noreferrer">jugo de mora</a> © Breville USA (CC BY 2.0) ·
            resto de imágenes: Unsplash (Unsplash License).
        </span>
    </div>
</footer>

<script src="<?= e(url('public/js/main.js')) ?>" defer></script>
</body>
</html>
