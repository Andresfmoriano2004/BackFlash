<?php
/**
 * Reglas del formulario de contacto.
 * Antes vivían sueltas dentro de acciones/contacto.php.
 */

declare(strict_types=1);

namespace BackFlash\Validation;

final class ContactoValidator extends Validador
{
    /**
     * @param array<string, mixed> $datos
     */
    public function validar(array $datos): bool
    {
        $this->errores = [];

        $nombre  = (string) ($datos['nombre'] ?? '');
        $email   = (string) ($datos['email'] ?? '');
        $asunto  = (string) ($datos['asunto'] ?? '');
        $mensaje = (string) ($datos['mensaje'] ?? '');

        if (mb_strlen($nombre) < Reglas::NOMBRE_MIN) {
            $this->falla('nombre', 'Escribe tu nombre (mínimo ' . Reglas::NOMBRE_MIN . ' caracteres).');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->falla('email', 'Ingresa un correo electrónico válido.');
        }

        if (mb_strlen($asunto) < Reglas::ASUNTO_MIN) {
            $this->falla('asunto', 'El asunto debe tener al menos ' . Reglas::ASUNTO_MIN . ' caracteres.');
        }

        if (mb_strlen($mensaje) < Reglas::MENSAJE_MIN) {
            $this->falla('mensaje', 'El mensaje debe tener al menos ' . Reglas::MENSAJE_MIN . ' caracteres.');
        }

        return $this->valido();
    }
}
