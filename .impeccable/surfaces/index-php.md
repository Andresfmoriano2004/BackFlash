---
version: 1
slug: "index-php"
primary_target: "index.php"
related_targets: ["menu.php","reservar.php","contacto.php","admin/index.php","admin/login.php","public/css/style.css"]
---

# Portada pública (index.php) — superficie primaria de BackFlash

Alcance: portada, menú, reservar, contacto y el panel admin hereda los tokens.

- **Modo del visitante:** Persuade en las cuatro páginas públicas (el visitante decide y actúa); Operate en `admin/`.
- **Audiencia:** comensales locales en Pereira, mayoría desde el móvil, con intención clara: ver la carta y asegurar mesa en una sola sesión.
- **Trabajo / acción:** abrir la carta y enviar una reserva; secundario, escribir un mensaje.
- **Prueba / contenido:** fotografía de los platos reales de la carta, horario y dirección reales, precios legibles, formulario de reserva corto con confirmación.
- **Restricciones no negociables:** PHP 8.x + HTML/CSS/JS vanilla; sin librerías; tokens en `style.css` heredados por el admin; dorado `#C9A538`; Poppins (cuerpo) + Satisfy solo marca; Lighthouse 100 en accesibilidad/buenas prácticas/SEO; no tocar CSRF, PDO, htmlspecialchars.
- **Momento memorable:** los números de la casa encendidos —hora, comensales, precio— como tubos de cátodo que brillan en oro sobre acero pavonado y cambian en el sitio sin saltar.

## Direction contract

**THESIS.** La reserva es una lectura, no un formulario: la casa se conoce por sus cifras. La portada no abre con un eslogan centrado sobre una foto de archivo (el default de la categoría que rechazo), sino con los números vivos del restaurante —horario de hoy, mesa disponible, precio desde— encendidos como tubos de cátodo sobre el chasis.

**OWN-WORLD.** Chasis de acero pavonado `#0B0C0E`, superficies en capas `#141410` / `#1F1B16`, filete de malla `#2C2C2C`; la única luz cálida del sistema es el oro de marca `#C9A538`, reservado a la cifra encendida, el filete activo y el CTA. Texto `#F2EEE6`, atenuado `#A49C8E`. Poppins para prosa y versalitas grabadas con tracking; Satisfy solo en el logotipo y un detalle. Radios 8px / 999px en chips. Los numerales siempre tabulares, de ancho fijo, dentro de una cápsula de vidrio con brillo interior; sin un segundo acento cromático en toda la interfaz.

**STORY.** El visitante entiende en tres segundos: aquí se sirve comida colombiana de verdad, está abierto hoy hasta las X, y el botón dorado reserva la mesa. Cree que la casa es cuidadosa porque sus datos están encendidos con precisión. Reserva sin salir de la página.

**FIRST VIEWPORT.** Barra de navegación fina sobre el chasis con el logotipo a la izquierda y «Reservar mesa» como placa dorada siempre visible. Debajo, a la izquierda (7/12), la fotografía de la sala recortada en una placa con filete; a la derecha (5/12), el `h1` en Poppins 700 colgado de un filete dorado continuo, una línea de apoyo, y debajo una **banca de tres tubos** (HOY ·ABIERTO | MESA ·2–12 | DESDE ·$4.000) con las cifras encendidas. El CTA primario dorado y el secundario de contorno quedan bajo la banca, sin scroll. Al pie del viewport arranca la franja de dirección y horario.

**FORM.** Dirección asignada por el roll: *Contador de tubos de nixie* (retador repartido, `signals-instruments-nixie-laboratory-counter`), semilla `dc223cbd`, posición 3 de la mano repartida; mi candidato propio era «El salón al anochecer» (nº1 de mi lista). Reglas del retador traducidas a marca: la banca de cifras es la topología (cada métrica de la casa tiene su tubo de ancho fijo), el resplandor naranja se traduce al oro `#C9A538`, y el acero pavonado es el fondo. Subidas nombradas: del léxico, color confinado a las planchas fotográficas e índice de categorías; del nixie, cifras tabulares de ancho fijo; de la sección de luz, la espina dorada continua; del disco de acreción, el centro oscuro sin rellenar; de ASCII, una sola tinta; del riso, un único solapamiento sancionado (el chip de precio cruza la esquina de la foto).

**FINISH.** unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance
