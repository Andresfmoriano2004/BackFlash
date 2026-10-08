# BackFlash · Restaurante colombiano (Pereira)

Sitio web completo en **PHP + MySQL** (HTML, CSS y JavaScript propios, sin frameworks
ni librerías externas): páginas públicas, formulario de contacto, reservas de mesa,
menú gestionable y panel de administración con inicio de sesión.

---

## 1. Requisitos

| Componente | Versión mínima |
|------------|----------------|
| Apache     | 2.4            |
| PHP        | 8.0 (probado en 8.2) con `pdo_mysql` y `fileinfo` |
| MySQL / MariaDB | 5.7 / 10.x (probado en MariaDB 10.4) |

XAMPP cumple todos los requisitos tal cual.

---

## 2. Instalación en XAMPP

1. **Copia la carpeta** `BackFlash` dentro de `C:\xampp\htdocs\`.
2. **Crea la base de datos** con datos de ejemplo:

   ```bash
   C:\xampp\mysql\bin\mysql.exe -u root < database/schema.sql
   ```

   (o importa `database/schema.sql` desde phpMyAdmin → pestaña *Importar*).
   Esto crea la base de datos `backflash`, las 4 tablas y 10 platos de ejemplo.
3. **Revisa la conexión** en `includes/config.php` si tu MySQL usa otro usuario o
   contraseña:

   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_NAME', 'backflash');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
4. **Abre el sitio**: <http://localhost/BackFlash/>

---

## 3. Panel de administración

| Dato         | Valor |
|--------------|-------|
| URL          | `http://localhost/BackFlash/admin/` (o `admin/login.php`) |
| Usuario      | `admin` |
| Contraseña   | `BackFlash2026!` |

> **Cámbiala en cuanto puedas.** Se almacena como hash (`password_hash` de PHP),
> no en texto plano. Para generar un hash nuevo:
>
> ```bash
> C:\xampp\php\php.exe -r "echo password_hash('TuNuevaClave', PASSWORD_DEFAULT);"
> ```
>
> y sustituye el valor de la columna `password_hash` de la tabla `usuarios`.

### Qué se puede gestionar

- **Resumen** (`admin/index.php`): mensajes sin leer, reservas pendientes y totales.
- **Mensajes**: leer/marcar como no leído y borrar los formularios de contacto.
- **Reservas**: cambiar el estado (*pendiente → confirmada → cancelada*), filtrar y borrar.
- **Menú**: crear, editar y eliminar platos y bebidas, subir su imagen
  (JPG/PNG/WEBP, máx. 3 MB), marcarlos como *destacados* (aparecen en la portada)
  u ocultarlos sin borrarlos.

---

## 4. Qué hace cada parte

| Ruta | Función |
|------|---------|
| `index.php` | Portada: reservaciones, quiénes somos y 4 platos destacados desde la BD. |
| `menu.php` | Carta completa (platos y bebidas) leída de la tabla `platos`. |
| `reservar.php` | Formulario de reserva (fecha, hora, personas…). |
| `contacto.php` | Formulario de contacto y datos del restaurante. |
| `acciones/contacto.php` | Valida, guarda en `mensajes`, envía el correo y redirige. |
| `acciones/reserva.php` | Valida, guarda en `reservas`, controla aforo y envía los correos. |
| `admin/` | Panel de administración (login + 4 secciones). |
| `includes/` | Configuración, conexión PDO, funciones (CSRF, flash, correo) y plantillas de cabecera/pie. |
| `database/schema.sql` | Esquema + datos iniciales. |
| `js/main.js` | Validación en cliente con mensajes accesibles. |
| `style.css` | Estilos completos (paleta dorada `#C9A538`, Poppins + Satisfy). |
| `img/`, `images/` | Ilustraciones SVG originales (pesan pocos KB y escalan sin pérdida). |

---

## 5. Correos

Por defecto el sitio **no envía correo real**: todo queda registrado en
`logs/mail.log` (protegido frente a la web) para poder revisarlo.

Para enviar correos de verdad en XAMPP configura `sendmail` y cambia en
`includes/config.php`:

```php
define('MAIL_MODE', 'mail');   // 'log' (por defecto) o 'mail'
define('SITE_EMAIL', 'reservas@backflash.com');
```

Correos que intenta enviar:

1. **Al restaurante**: mensaje de contacto nuevo y reserva recibida.
2. **Al cliente**: acuse de recibo de su reserva con fecha, hora y dirección.

---

## 6. Seguridad incluida

- Consultas **preparadas PDO** en todos los accesos a la base de datos.
- **Token CSRF** en todos los formularios (POST sin token → error 403).
- Contraseña del admin con **`password_hash()` / `password_verify()`** y
  `session_regenerate_id()` al entrar.
- Salida siempre escapada con `htmlspecialchars` (`e()`).
- Subida de imágenes validada por **tipo MIME real** (no por la extensión),
  con el motor PHP desactivado en `img/subidas/`.
- `.htaccess` que bloquea el acceso web a `includes/`, `database/` y `logs/`.
- Etiquetas `noindex` en el panel, mensajes `aria-live`, foco en el primer campo
  inválido y validación tanto en cliente como en servidor.

---

## 7. Accesibilidad y calidad

Las cinco páginas públicas/panel obtenidas con Lighthouse:

- Accesibilidad **100**
- Buenas prácticas **100**
- SEO **100**

Sin errores de consola, sin imágenes rotas, sin scroll horizontal y sin
solapamientos a 375 px, 785 px y 844 px de ancho.

---

## 8. Datos de ejemplo

`database/schema.sql` crea un mensaje y una reserva de ejemplo para que el panel
no esté vacío la primera vez. Bórralos desde el propio panel cuando quieras.
