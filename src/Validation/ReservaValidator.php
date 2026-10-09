<?php
/**
 * Reglas de la reserva de mesa.
 * Antes vivían sueltas dentro de acciones/reserva.php, repetidas a mano en
 * js/main.js.
 *
 * Recibe la fecha "de hoy" por constructor para poder comprobar los límites
 * de fecha sin depender del reloj del sistema.
 */

declare(strict_types=1);

namespace BackFlash\Validation;

use DateTimeImmutable;

final class ReservaValidator extends Validador
{
    private DateTimeImmutable $hoy;

    public function __construct(?DateTimeImmutable $hoy = null)
    {
        $this->hoy = $hoy ?? new DateTimeImmutable('today');
    }

    /**
     * @param array<string, mixed> $datos
     */
    public function validar(array $datos): bool
    {
        $this->errores = [];

        $nombre   = (string) ($datos['nombre'] ?? '');
        $email    = (string) ($datos['email'] ?? '');
        $telefono = (string) ($datos['telefono'] ?? '');
        $fecha    = (string) ($datos['fecha'] ?? '');
        $hora     = (string) ($datos['hora'] ?? '');
        $personas = (string) ($datos['personas'] ?? '');

        $this->validarNombre($nombre);
        $this->validarEmail($email);
        $this->validarTelefono($telefono);
        $this->validarPersonas($personas);
        $this->validarFecha($fecha);
        $this->validarHora($hora);

        return $this->valido();
    }

    // --- Límites que la vista usa para los atributos min/max del formulario ---

    public function fechaMinima(): string
    {
        return $this->hoy->format('Y-m-d');
    }

    public function fechaMaxima(): string
    {
        return $this->hoy->modify('+' . Reglas::DIAS_ANTICIPACION . ' days')->format('Y-m-d');
    }

    // --- Reglas ---

    private function validarNombre(string $nombre): void
    {
        if (mb_strlen($nombre) < Reglas::NOMBRE_MIN) {
            $this->falla('nombre', 'Escribe tu nombre completo (mínimo ' . Reglas::NOMBRE_MIN . ' caracteres).');
        }
    }

    private function validarEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->falla('email', 'Ingresa un correo electrónico válido.');
        }
    }

    /**
     * Se CUENTAN los dígitos. La versión anterior comparaba el número con 7
     * (`preg_replace(...) < 7`), de modo que "12" pasaba la validación porque
     * 12 < 7 es falso, y solo se rechazaban valores menores que 7.
     */
    private function validarTelefono(string $telefono): void
    {
        if ($this->digitos($telefono) < Reglas::TELEFONO_DIGITOS_MIN) {
            $this->falla(
                'telefono',
                'Ingresa un teléfono de contacto (mínimo ' . Reglas::TELEFONO_DIGITOS_MIN . ' dígitos).'
            );
            return;
        }

        // Mismo patrón que el atributo `pattern` del input: si el navegador lo
        // acepta, el servidor también.
        if (!preg_match(Reglas::regexTelefono(), $telefono)) {
            $this->falla('telefono', 'El teléfono solo puede incluir números, espacios y los signos + ( ) - .');
        }
    }

    private function validarPersonas(string $personas): void
    {
        $validas = array_map('strval', range(Reglas::PERSONAS_MIN, Reglas::PERSONAS_MAX));

        if (!in_array($personas, $validas, true)) {
            $this->falla(
                'personas',
                'Elige entre ' . Reglas::PERSONAS_MIN . ' y ' . Reglas::PERSONAS_MAX . ' personas.'
            );
        }
    }

    private function validarFecha(string $fecha): void
    {
        $fechaObj = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);

        if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
            $this->falla('fecha', 'Selecciona una fecha válida.');
            return;
        }

        if ($fechaObj < $this->hoy) {
            $this->falla('fecha', 'La fecha no puede ser anterior a hoy.');
            return;
        }

        if ($fechaObj > $this->hoy->modify('+' . Reglas::DIAS_ANTICIPACION . ' days')) {
            $this->falla(
                'fecha',
                'Solo aceptamos reservas con hasta ' . Reglas::DIAS_ANTICIPACION . ' días de anticipación.'
            );
        }
    }

    private function validarHora(string $hora): void
    {
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora)) {
            $this->falla('hora', 'Selecciona una hora válida.');
            return;
        }

        $minutos = Reglas::minutos($hora);

        if ($minutos < Reglas::aperturaMinutos() || $minutos > Reglas::cierreMinutos()) {
            $this->falla('hora', 'Atendemos de 11:00 a. m. a 10:00 p. m.');
            return;
        }

        if ($minutos % Reglas::FRANJA_MINUTOS !== 0) {
            $this->falla('hora', 'Las reservas se agendan cada ' . Reglas::FRANJA_MINUTOS . ' minutos.');
        }
    }
}
