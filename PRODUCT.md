# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Clientes locales de Pereira (Colombia) que visitan el sitio para consultar el menú y realizar una reserva de mesa. El contexto de uso es principalmente móvil o de escritorio desde casa o el trabajo, con intención clara: ver qué se sirve y asegurar un lugar antes de ir.

## Product Purpose

BackFlash es el sitio web oficial de un restaurante colombiano en Pereira. Permite a los clientes explorar la carta completa (platos y bebidas), hacer reservas de mesa con confirmación, enviar mensajes de contacto, y al equipo del restaurante gestionar todo desde un panel de administración privado.

## Positioning

Una experiencia digital moderna y premium que eleva la percepción del restaurante: el sitio no es solo informativo, es el primer contacto con el ambiente del lugar. BackFlash apuesta por la calidad de la experiencia digital como extensión directa de la calidad en sala.

## Operating Context

- Visitante típico llega desde búsqueda directa o referido; navega el menú y completa el formulario de reserva en una sola sesión.
- El restaurante gestiona reservas, mensajes y platos desde `admin/`.
- Correos de confirmación se envían al cliente y al restaurante (actualmente en modo log para XAMPP local).
- El sitio corre sobre XAMPP (Apache + PHP 8.x + MariaDB 10.4); sin frameworks ni librerías externas.

## Capabilities and Constraints

- Páginas públicas: portada, menú, reservar, contacto.
- Panel admin: login con hash, gestión de mensajes, reservas y carta (CRUD de platos con imagen).
- Stack: PHP 8.x, MySQL/MariaDB, HTML/CSS/JS vanilla — sin frameworks frontend.
- Sin librerías externas (sin Bootstrap, sin Tailwind, sin jQuery).
- Imágenes: JPG/PNG/WEBP, máx. 3 MB, validadas por MIME real.
- Seguridad: PDO preparado, CSRF, password_hash, htmlspecialchars, .htaccess de protección.
- Lighthouse: Accesibilidad 100, Buenas Prácticas 100, SEO 100.

## Brand Commitments

- **Nombre:** BackFlash (evocación premium y moderna).
- **Paleta existente:** dorado `#C9A538` como color de acento principal.
- **Tipografía existente:** Poppins (cuerpo) + Satisfy (decorativa/marca).
- El diseño visual debe sentirse premium, contemporáneo y sofisticado — no genérico ni de plantilla.

## Evidence on Hand

- Código fuente completo en `c:\xampp\htdocs\BackFlash\`.
- `style.css` (27 KB) como autoridad del sistema visual actual.
- Imágenes SVG originales en `img/` e `images/`.
- Esquema de base de datos en `database/schema.sql`.
- Lighthouse scores 100/100/100 documentados en `LEEME.md`.

## Product Principles

1. **Digital como extensión de la experiencia en sala** — el sitio debe transmitir la misma calidad y calidez que el restaurante promete presencialmente.
2. **Claridad para la conversión** — el visitante debe poder ver el menú y reservar sin fricción ni pasos innecesarios.
3. **Premium sin artificio** — sofisticado y moderno, nunca genérico; cada detalle visual refuerza la percepción de calidad.
4. **Funcionalidad sólida primero** — el admin y los formularios deben ser fiables y seguros; la belleza no puede romper lo que ya funciona.
5. **Accesibilidad real** — Lighthouse 100 no es el techo, es el suelo; el diseño debe ser legible y operable para todos.

## Accessibility & Inclusion

Accesibilidad 100 en Lighthouse mantenida como requisito no negociable. Mensajes aria-live, foco en primer campo inválido, validación en cliente y servidor.
