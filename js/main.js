/**
 * BackFlash - validación de formularios en el cliente.
 *
 * Los formularios también se validan en el servidor (acciones/*.php);
 * aquí solo mejoramos la experiencia mostrando los errores en línea
 * y sin perder lo que la persona ya escribió.
 */
document.addEventListener('DOMContentLoaded', function () {
  var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function mensajePorDefecto(campo) {
    var nombre = campo.name || campo.id || 'este campo';
    switch (campo.type) {
      case 'email':
        return 'Ingresa un correo electrónico válido.';
      case 'date':
        return 'Selecciona una fecha válida.';
      case 'time':
        return 'Selecciona una hora válida.';
      case 'tel':
        return 'Ingresa un teléfono de contacto.';
      default:
        if (campo.tagName === 'SELECT') return 'Selecciona una opción.';
        return 'Completa el campo "' + nombre + '".';
    }
  }

  function validar(campo) {
    var valor = (campo.value || '').trim();

    if (campo.required && valor === '') return mensajePorDefecto(campo);
    if (valor === '') return null;

    if (campo.type === 'email' && !EMAIL.test(valor)) {
      return 'Ingresa un correo electrónico válido.';
    }

    var min = parseInt(campo.getAttribute('minlength') || '0', 10);
    if (min && valor.length < min) {
      return 'Escribe al menos ' + min + ' caracteres.';
    }

    if ((campo.type === 'date' || campo.type === 'time') && campo.min && valor < campo.min) {
      return campo.type === 'date'
        ? 'La fecha no puede ser anterior a hoy.'
        : 'Atendemos a partir de las ' + campo.min + '.';
    }
    if ((campo.type === 'date' || campo.type === 'time') && campo.max && valor > campo.max) {
      return 'La fecha máxima permitida es ' + campo.max + '.';
    }

    if (campo.type === 'tel' && valor.replace(/\D/g, '').length < 7) {
      return 'Ingresa un teléfono de contacto (mínimo 7 dígitos).';
    }

    return null;
  }

  function contenedor(campo) {
    return campo.closest('.form-group') || campo.parentNode;
  }

  function limpiar(campo) {
    campo.removeAttribute('aria-invalid');
    var errorId = 'error-' + (campo.name || campo.id);
    var viejo = document.getElementById(errorId);
    if (viejo && viejo.classList.contains('field-error-cliente')) {
      viejo.remove();
    }
    var desc = (campo.getAttribute('aria-describedby') || '')
      .split(/\s+/)
      .filter(function (id) { return id && id !== errorId; });
    if (desc.length) campo.setAttribute('aria-describedby', desc.join(' '));
    else campo.removeAttribute('aria-describedby');
  }

  function marcar(campo, mensaje) {
    var errorId = 'error-' + (campo.name || campo.id);
    var span = document.getElementById(errorId);

    if (!span) {
      span = document.createElement('span');
      span.className = 'field-error field-error-cliente';
      span.id = errorId;
      contenedor(campo).appendChild(span);
    }
    span.textContent = mensaje;
    span.classList.add('field-error-cliente');

    campo.setAttribute('aria-invalid', 'true');
    var desc = (campo.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
    if (desc.indexOf(errorId) === -1) desc.push(errorId);
    campo.setAttribute('aria-describedby', desc.join(' '));
  }

  document.querySelectorAll('form.js-form').forEach(function (form) {
    form.addEventListener('submit', function (evento) {
      var primeroInvalido = null;

      form.querySelectorAll('input, select, textarea').forEach(function (campo) {
        if (campo.type === 'hidden' || campo.disabled) return;
        limpiar(campo);
        var mensaje = validar(campo);
        if (mensaje) {
          marcar(campo, mensaje);
          if (!primeroInvalido) primeroInvalido = campo;
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
      }
    });

    form.querySelectorAll('input, select, textarea').forEach(function (campo) {
      campo.addEventListener('input', function () { limpiar(campo); });
      campo.addEventListener('change', function () { limpiar(campo); });
    });
  });
});
