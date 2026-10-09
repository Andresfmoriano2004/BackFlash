# BackFlash — Decisiones de diseño

Documento breve sobre el **segundo rediseño** del sitio. El sistema de diseño vive en
`public/css/style.css` (tokens, base, componentes, páginas) y el panel lo hereda en
`public/css/admin.css`. Sin frameworks, sin librerías, CSS vanilla.

---

## 1 · La idea central

**Las cifras de la casa están encendidas.** Hora de apertura, aforo y precio más bajo
son un banco de dígitos de ancho fijo, alineados en columna, en dorado sobre chasis
oscuro. Es lo primero que se ve en la portada y es lo único que se mueve con intención:
las cifras no saltan al cambiar porque son tabulares y de ancho fijo.

Toda decisión posterior responde a esa idea: un solo acento cálido, una sola tinta,
superficies planas en capas, y la fotografía como único lugar donde entra color.

## 2 · Dirección: fondo oscuro profundo con superficies en capas

Se eligió «chasis de acero pavonado» sobre «papel/crema» porque:

- El oro de marca `#C9A538` gana peso y lectura sobre fondo oscuro sin saturar.
- Las fotos de platos (el activo real del restaurante) se convierten en el elemento
  más luminoso de la página en lugar de competir con un fondo claro.
- El panel de administración hereda el mismo chasis: una sola piel para todo el
  producto, sin un «modo oscuro» añadido después.

Se descartó el segundo acento cromático deliberadamente. **Un solo acento** implica que
el oro nunca decorativo: solo marca, CTA activo y cifra encendida.

## 3 · Tokens

Los nombres son la interfaz pública del diseño; el admin no define color propio,
solo reutiliza (`--adm-*` apunta a los tokens de arriba).

| Grupo | Tokens | Valores |
|---|---|---|
| Chasis | `--bg` · `--bg-deep` · `--surface` · `--surface-2` | `#0B0C0E` · `#141410` · `#141410` · `#1F1B16` |
| Filete | `--line` · `--line-strong` · `--line-input` | `#2C2C2C` · `#767064` · `#6E6A62` |
| Tinta | `--ink` · `--ink-soft` · `--muted` | `#F2EEE6` · `#CFC9BD` · `#A49C8E` |
| Marca | `--gold` · `--gold-bright` · `--gold-press` · `--gold-text` · `--on-gold` | `#C9A538` · `#E7C55D` · `#B8942F` · `#E7C55D` · `#0B0C0E` |
| Estado | `--success` / `--aviso` / `--danger` + `-bg` y `-line` | oro · oro medio · tinta |

**Contraste:** todos los pares de texto verificados ≥ 4,5:1 sobre su fondo real
(el guion usa el color compuesto de sus ancestros, no el color plano). El peor caso
medido en vivo es **6,3:1**; los pares no textuales (borde de control, filete de
badge, anillo de foco) ≥ 3:1. `--danger-line` está en `#807A6E` precisamente para
que el borde del botón peligroso cruce 3:1.

**Estado sin depender del color.** `--success`, `--aviso` y `--danger` comparten
familia porque el sistema es de una sola tinta; por eso *cada estado va siempre
acompañado de texto y de un ícono* (✕ / ✓ / aviso), y los badges de reserva además
usan relleno, filete y etiqueta escrita.

## 4 · Tipografía — dos familias descargadas

- **Poppins** (400/600/700/800): prosa, títulos y versalitas grabadas.
- **Satisfy** (400): **solo** el logotipo y un único detalle decorativo
  (`¿Qué ofrecemos?` de la banda `nuestro`). Nunca en bloques de texto.
- **Cifras:** monoespaciada del sistema (`ui-monospace → Cascadia Mono → Consolas → …`).
  Ancho fijo real **sin cargar una tercera webfont** — el brief pedía dos familias y
  el concepto pedía dígitos fijos; se resolvió con la pila del sistema.

La petición va en el `<head>` (`preconnect` + `<link>`), **no** en un `@import` dentro
de la hoja: el `@import` obliga a descargar `style.css` entero antes de empezar a traer
las tipografías y retrasa el primer pintado.

Escala: `12 · 14 · 15 · 16 · 18 · 20 · 24 · 32` más dos escalones fluidos
(`--fs-section` 28→44, `--fs-hero` 32→56). Los siete valores del brief (12/14/16/20/
28/40/56) están todos presentes; los pasos intermedios (15/18/24/32) afinan componentes
pequeños. `--lh-body: 1.65`, `--lh-tight: 1.12`, `--track-caps: .14em` para versalitas.

## 5 · Espaciado y radios

- Escala 4/8: `--s-1…--s-24` (4, 8, 12, 16, 20, 24, 32, 40, 48, 64, 96).
- **Un solo lenguaje de radios:** `--r-sm/md/lg = 8px` para superficies y controles,
  `--r-pill` (999px) solo para chips. (Los tres nombres se mantienen porque `admin.css`
  los referencia; apuntan al mismo radio.)

## 6 · Componentes

| Componente | Tratamiento |
|---|---|
| Botón primario | placa dorada `--gold` sobre `--on-gold` (8,4:1); hover `--gold-bright`, activo `--gold-press` |
| Botón secundario | contorno dorado sobre el chasis |
| Botón fantasma / peligro | `--adm-danger-soft` + borde ≥3:1 + texto |
| Inputs | etiqueta **visible** siempre, campo ≥48px, foco con anillo dorado de 2px |
| Tarjeta de plato | fila editorial (miniatura 96px + nombre + precio a la derecha + descripción debajo), separada por filete |
| Badge de reserva | relleno + filete + etiqueta escrita (pendiente / confirmada / cancelada) |
| Alertas | `role="status"`, `aria-live`, ícono + texto |
| Enlace de salto | `.skip-link`, oculto hasta que recibe foco, mueve el foco a `#contenido` |

Estados completos en todos los interactivos: hover, focus-visible, activo, deshabilitado,
cargando (spinner `data-cargando`), vacío (`.vacio`) y error (`.field-error` con
`aria-describedby`).

## 7 · Composición por página

- **Portada.** Barra fija con CTA *Reservar mesa* siempre visible (≥640) → hero con
  foto y el banco de cifras → franja de ubicación/horario/teléfono → dos bloques
  informativos asimétricos (7/5 y 5/7 en ≥1024) → banda `nuestro` con la única
  aparición de Satisfy → destacados con un plato *lead* + filas alternadas → visita.
- **Menú.** Chips de categoría con `scroll-margin-top` y un `h2` por sección
  (`#platos`, `#bebidas`); cartas en dos columnas desde ≥1024, no una rejilla de
  tarjetas idénticas.
- **Reservar.** Una sola columna (~560px), validación en línea con la Constraint
  Validation API, foco en el primer campo inválido y confirmación explícita.
- **Contacto.** Formulario + placa de datos del local; la dirección enlaza a Google
  Maps y se subraya para no depender solo del color.
- **Admin.** Login sobrio, tablas con filtros por estado que se convierten en tarjetas
  apiladas por debajo de 640px, formulario de plato con **vista previa de imagen** y
  confirmación en los borrados.

## 8 · Movimiento

Una sola animación autorizada: la entrada de las secciones al entrar en cuadro
(`animation-timeline: view()`), protegida por `@supports` y por
`prefers-reduced-motion: no-preference`. El resto son transiciones de 150–250 ms con
propósito (color, borde, desplazamiento del enlace de salto). Con
`prefers-reduced-motion: reduce` todo queda en 1 ms y sin desplazamiento.

## 9 · Responsive y accesibilidad

- **Mobile first**, quiebres en **640 / 1024 / 1280** (el admin alinea sus quiebres a
  la misma rejilla; la única excepción documentada es la tabla → tarjetas por debajo
  de 640px).
- **Objetivos táctiles ≥44px** en botones, nav, chips, campos y enlaces de navegación.
  Los enlaces en línea dentro de una frase (pie, dirección) quedan cubiertos por la
  excepción en línea de WCAG; en móvil la dirección ya envuelve y supera los 44px.
- **Un solo `h1` por página**, `alt` en todas las imágenes, landmarks (`banner`,
  `main`, `contentinfo`), `aria-current="page"` en el enlace activo, orden de foco
  lógico y foco visible dorado en todo lo interactivo.
- El menú móvil usa el patrón *checkbox-hack*: **funciona sin JavaScript**, y `main.js`
  solo sincroniza `aria-expanded`. Si JavaScript no carga, nada se rompe.

## 10 · JavaScript

- `public/js/main.js` — validación de formularios y estados de envío. No toca seguridad.
- `public/js/admin.js` — **solo presentación**: pregunta antes de enviar los
  formularios con `data-confirm` y enseña la vista previa de la imagen del plato.
  El servidor valida, comprueba CSRF y autoriza siempre; si este script no carga, los
  formularios siguen enviándose y solo se pierde la vista previa.

No se ha tocado la lógica de seguridad (CSRF, PDO preparado, `password_hash`,
`htmlspecialchars`): el rediseño es 100 % capa visual.

## 11 · Imágenes

- Platos, bebidas y fotos de sala en **WEBP** con `loading="lazy"` y `width`/`height`
  declarados (sin saltos de layout). `hero.webp` va con `fetchpriority="high"` porque
  está visible al cargar.
- Los SVG de platos anteriores ya no se referencian desde ninguna vista (quedan en
  disco como material, sin uso).
- Iconos y logo siguen en SVG, que es lo correcto para formas planas.
- **Procedencia:** cada foto lleva su crédito en `.credito` (`Fotografías: © Jorge
  Lascar (CC BY-SA 2.0)…`), accesible desde el pie.

## 12 · Verificación

| Comprobación | Resultado |
|---|---|
| `php -l` en todos los `.php` | 0 errores |
| Matriz 4 páginas × 360 / 768 / 1280 | sin scroll horizontal · 1 `h1` · `alt` completo · nada fuera de viewport |
| Contraste en vivo (DOM real, color compuesto) | 0 fallos AA; peor ratio 6,3:1 |
| Objetivos táctiles | ≥44px en controles (excepción en línea WCAG en prosa) |
| Consola | 0 errores, 0 avisos |
| **Lighthouse** (9 páginas públicas + admin) | **Accesibilidad 100 · Buenas prácticas 100 · SEO 100** |
| `admin.js` | vista previa y `data-confirm` verificados en el navegador |

## 13 · Observaciones abiertas (no son de diseño)

1. `reservar.php` muestra públicamente «Próximas reservas confirmadas» con fecha,
   hora y número de personas. Puede filtrar datos de clientes: convendría mostrarlo
   solo como «huecos disponibles» sin identificadores, u ocultarlo. **No se ha
   cambiado** para no alterar datos ni comportamiento sin decisión explícita.
2. `.impeccable/` y los `_detect_*.html` (artefactos de las herramientas de revisión)
   están sin seguir; limpiarlos antes de un commit.
