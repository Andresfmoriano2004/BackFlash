/**
 * BackFlash - presentación de la validación en el cliente.
 *
 * AQUÍ NO HAY REGLAS DE VALIDACIÓN. Las reglas viven en el servidor
 * (src/Validation/) y llegan al formulario como atributos HTML
 * (required, minlength, maxlength, type, min, max, step, pattern) generados
 * desde src/Validation/Reglas.php.
 *
 * Este archivo solo pregunta al navegador si el campo es válido mediante la
 * Constraint Validation API y muestra el resultado con mensajes accesibles.
 * El servidor vuelve a validar siempre: el cliente nunca es la única barrera.
 *
 * Antes había aquí una segunda copia de las reglas (email, teléfono, fecha y
 * hora) que ya había divergido de la del servidor.
 */
document.addEventListener('DOMContentLoaded', function () {
  /**
   * Mensaje según el TIPO de restricción incumplida.
   * No depende de ningún campo concreto, así que no duplica ninguna regla.
   * Los dos avisos que sí son específicos de una regla (patrón del teléfono y
   * franja de la hora) llegan como texto desde la vista, no como lógica.
   */
  function mensajeDe(campo) {
    var v = campo.validity;

    if (v.valueMissing) {
      return campo.tagName === 'SELECT' ? 'Selecciona una opción.' : 'Completa este campo.';
    }
    if (v.patternMismatch) {
      return campo.dataset.mensajePatron || 'Revisa el formato de este campo.';
    }
    if (v.typeMismatch) {
      return campo.type === 'email'
        ? 'Ingresa un correo electrónico válido.'
        : 'El formato no es válido.';
    }
    if (v.tooShort) {
      return 'Escribe al menos ' + campo.minLength + ' caracteres.';
    }
    if (v.tooLong) {
      return 'No superes los ' + campo.maxLength + ' caracteres.';
    }
    if (v.rangeUnderflow) {
      return campo.type === 'time'
        ? 'Atendemos a partir de las ' + campo.min + '.'
        : 'La fecha no puede ser anterior a hoy.';
    }
    if (v.rangeOverflow) {
      return campo.type === 'time'
        ? 'Atendemos hasta las ' + campo.max + '.'
        : 'La fecha máxima permitida es ' + campo.max + '.';
    }
    if (v.stepMismatch) {
      return campo.dataset.mensajePaso || 'Elige un valor de la lista.';
    }
    if (v.badInput) {
      return 'El valor no es válido.';
    }
    return 'Revisa este campo.';
  }

  /* El servidor usa exactamente este mismo id en mostrar_error(). */
  function idError(campo) {
    return 'error-' + (campo.name || campo.id);
  }

  function contenedor(campo) {
    return campo.closest('.form-group') || campo.parentNode;
  }

  function ariaAnadir(campo, id) {
    var ids = (campo.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
    if (ids.indexOf(id) === -1) {
      ids.push(id);
    }
    campo.setAttribute('aria-describedby', ids.join(' '));
  }

  function ariaQuitar(campo, id) {
    var ids = (campo.getAttribute('aria-describedby') || '')
      .split(/\s+/)
      .filter(function (actual) { return actual && actual !== id; });

    if (ids.length) {
      campo.setAttribute('aria-describedby', ids.join(' '));
    } else {
      campo.removeAttribute('aria-describedby');
    }
  }

  /** Quita el mensaje y el estado de error de un campo que ya es válido. */
  function limpiar(campo) {
    var id = idError(campo);
    var span = document.getElementById(id);
    if (span && span.classList.contains('field-error')) {
      span.remove();
    }
    campo.removeAttribute('aria-invalid');
    ariaQuitar(campo, id);
  }

  /** Muestra el mensaje y marca el campo como inválido. */
  function marcar(campo, mensaje) {
    var id = idError(campo);
    var span = document.getElementById(id);

    if (!span) {
      span = document.createElement('span');
      span.id = id;
      span.className = 'field-error';
      contenedor(campo).appendChild(span);
    }

    span.textContent = mensaje;
    campo.setAttribute('aria-invalid', 'true');
    ariaAnadir(campo, id);
  }

  /**
   * Revisa un campo solo si ya estaba marcado: así no aparece un error
   * mientras alguien escribe un campo por primera vez.
   */
  function revalidar(campo) {
    if (!campo.hasAttribute('aria-invalid')) {
      return;
    }
    if (campo.checkValidity()) {
      limpiar(campo);
    } else {
      marcar(campo, mensajeDe(campo));
    }
  }

  function campos(form) {
    return form.querySelectorAll('input, select, textarea');
  }

  document.querySelectorAll('form.js-form').forEach(function (form) {
    form.addEventListener('submit', function (evento) {
      var primeroInvalido = null;

      campos(form).forEach(function (campo) {
        if (campo.type === 'hidden' || campo.disabled) {
          return;
        }
        /* checkValidity() aplica las reglas declaradas en el HTML. */
        if (campo.checkValidity()) {
          limpiar(campo);
          return;
        }
        marcar(campo, mensajeDe(campo));
        if (!primeroInvalido) {
          primeroInvalido = campo;
        }
      });

      if (primeroInvalido) {
        evento.preventDefault();
        primeroInvalido.focus();
        return;
      }

      var boton = form.querySelector('button[type="submit"]');
      if (boton) {
        boton.disabled = true;
        boton.dataset.texto = boton.textContent;
        boton.textContent = 'Enviando…';
        /* Estado de carga: el CSS muestra un spinner dentro del botón */
        boton.dataset.cargando = 'true';
        boton.setAttribute('aria-busy', 'true');
      }
    });

    campos(form).forEach(function (campo) {
      if (campo.type === 'hidden') {
        return;
      }
      campo.addEventListener('input', function () { revalidar(campo); });
      campo.addEventListener('change', function () { revalidar(campo); });
    });
  });
});
