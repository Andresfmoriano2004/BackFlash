/**
 * BackFlash · Panel de administración — solo presentación.
 *
 * Aquí NO hay reglas de negocio ni autorización: el servidor valida,
 * comprueba CSRF y vuelve a comprobar cada acción. Este archivo hace
 * tres únicamente cosas de interfaz:
 *
 *   1. Pregunta antes de enviar los formularios con data-confirm.
 *   2. Centra en el menú horizontal la pestaña de la página activa.
 *   3. Enseña una vista previa de la imagen del plato antes de subirla.
 *
 * Si el script no carga, los formularios siguen enviándose con normalidad
 * (la confirmación es un refuerzo, no una barrera), el menú se sigue
 * deslizando a mano y el campo de archivo sigue funcionando: solo se
 * pierde la vista previa.
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  /* ---------------------------------------------------------------
   * 1 · Confirmación de acciones destructivas
   * ------------------------------------------------------------- */
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    var yaConfirmado = false;

    form.addEventListener('submit', function (evento) {
      if (yaConfirmado) {
        return; // ya se respondió «sí»: no volver a preguntar
      }

      if (!window.confirm(form.getAttribute('data-confirm'))) {
        evento.preventDefault();
        return;
      }

      yaConfirmado = true;
    });
  });

  /* ---------------------------------------------------------------
   * 2 · Menú horizontal: dejar visible la página activa
   *
   * En móvil el menú se desliza en horizontal (overflow-x) y la pestaña
   * activa puede quedar fuera de pantalla. Con JS se centra al cargar;
   * sin él, el menú sigue siendo deslizable a mano.
   * ------------------------------------------------------------- */
  var navAdmin = document.querySelector('.admin-nav');
  var activo   = navAdmin && navAdmin.querySelector('[aria-current="page"]');

  if (activo) {
    var centro = activo.offsetLeft - (navAdmin.clientWidth - activo.offsetWidth) / 2;
    navAdmin.scrollLeft = Math.max(0, centro);
  }

  /* ---------------------------------------------------------------
   * 3 · Vista previa de la imagen del plato
   * ------------------------------------------------------------- */
  var campo   = document.getElementById('p-img');
  var caja    = document.getElementById('p-img-preview');
  var miniImg = caja ? caja.querySelector('img') : null;
  var nombre  = caja ? caja.querySelector('[data-nombre]') : null;

  if (!campo || !caja) {
    return;
  }

  // Imagen actual del plato: se restaura si el usuario vacía el campo.
  var imagenInicial = miniImg ? (miniImg.getAttribute('src') || '') : '';
  var urlActual     = '';

  function pintar(url, texto) {
    if (!miniImg) {
      miniImg = document.createElement('img');
      miniImg.alt = 'Vista previa de la imagen seleccionada';
      miniImg.width = 96;
      miniImg.height = 72;
      caja.insertBefore(miniImg, caja.firstChild);
    }

    if (urlActual) {
      URL.revokeObjectURL(urlActual); // no acumular blob: URLs
    }
    urlActual = url.indexOf('blob:') === 0 ? url : '';

    miniImg.src = url;
    if (nombre) {
      nombre.textContent = texto || '';
    }
    caja.hidden = false;
  }

  function restaurar() {
    if (urlActual) {
      URL.revokeObjectURL(urlActual);
      urlActual = '';
    }

    if (imagenInicial) {
      pintar(imagenInicial, '');
    } else {
      caja.hidden = true;
    }
  }

  campo.addEventListener('change', function () {
    var archivo = campo.files && campo.files[0];

    if (!archivo) {
      restaurar();
      return;
    }

    if (archivo.type.indexOf('image/') !== 0) {
      // El navegador ya filtra con accept=, pero no fiarse de eso.
      restaurar();
      return;
    }

    pintar(URL.createObjectURL(archivo), archivo.name);
  });
});
