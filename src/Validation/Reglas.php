<?php
/**
 * Límites y formatos compartidos por TODA la validación.
 *
 * Es la única fuente de verdad de estos valores: los usan los validadores del
 * servidor (autoritativos) y las vistas para generar los atributos HTML
 * (min, max, minlength, maxlength, pattern). Si un límite cambia, cambia aquí
 * y el formulario y el servidor siguen de acuerdo por construcción.
 *
 * Antes estos números estaban escritos por duplicado en acciones/*.php y en
 * js/main.js, y ya habían divergido (la regla del teléfono comparaba la
 * magnitud del número en vez de contar los dígitos).
 */

declare(strict_types=1);

namespace BackFlash\Validation;

final class Reglas
{
    // --- Formulario de contacto ---
    public const NOMBRE_MIN  = 3;
    public const NOMBRE_MAX  = 120;
    public const EMAIL_MAX   = 160;
    public const ASUNTO_MIN  = 3;
    public const ASUNTO_MAX  = 160;
    public const MENSAJE_MIN = 10;
    public const MENSAJE_MAX = 4000;

    // --- Reservas ---
    public const COMENTARIO_MAX    = 500;
    public const PERSONAS_MIN      = 1;
    public const PERSONAS_MAX      = 20;
    public const DIAS_ANTICIPACION = 90;
    public const HORA_APERTURA     = '11:00';
    public const HORA_CIERRE       = '22:00';
    public const FRANJA_MINUTOS    = 15;

    /**
     * Teléfono: se cuentan los dígitos, no se compara su valor.
     * (El bug original era `preg_replace('/\D/','',$tel) < 7`, que aceptaba
     * "12" porque 12 < 7 es falso.)
     */
    public const TELEFONO_DIGITOS_MIN = 7;
    public const TELEFONO_MAX         = 40;

    // --- Menú (panel) ---
    public const PLATO_NOMBRE_MIN = 2;
    public const PLATO_DESC_MAX   = 255;
    public const PLATO_ORDEN_MAX  = 999;

    /**
     * Patrón del teléfono, en un solo sitio.
     *
     * La misma cadena se usa como `pattern` del input (la aplica el navegador,
     * que la ancla a todo el valor) y dentro del validador del servidor. No es
     * una regla duplicada: es una regla generada y consumida por dos clientes.
     *
     * Exige dos cosas a la vez:
     *   1. al menos TELEFONO_DIGITOS_MIN dígitos en cualquier posición,
     *   2. que el valor solo tenga dígitos, espacios y los signos + ( ) - .
     *
     * `(`, `)` y `-` van escapados a propósito: los navegadores compilan el
     * atributo `pattern` con el flag `v` (unicodeSets), donde esos caracteres
     * son sintaxis reservada dentro de una clase y un patrón sin escapar NO
     * COMPILA: el navegador lo descarta y la regla desaparece en silencio.
     * Escapados compila igual con `v` y con `u`, y en PCRE.
     */
    public static function patronTelefono(): string
    {
        return '(?=(?:\D*\d){' . self::TELEFONO_DIGITOS_MIN . ',})[\d\s\(\)+.\-]+';
    }

    /** Expresión PCRE equivalente al patrón del input, para el servidor. */
    public static function regexTelefono(): string
    {
        return '/^' . self::patronTelefono() . '$/';
    }

    /** Minutos transcurridos desde medianoche para una hora "HH:MM". */
    public static function minutos(string $hora): int
    {
        return (int) substr($hora, 0, 2) * 60 + (int) substr($hora, 3, 2);
    }

    public static function aperturaMinutos(): int
    {
        return self::minutos(self::HORA_APERTURA);
    }

    public static function cierreMinutos(): int
    {
        return self::minutos(self::HORA_CIERRE);
    }

    /** Cuántos dígitos tiene un teléfono (cuenta, no compara). */
    public static function digitosTelefono(string $telefono): int
    {
        return strlen(preg_replace('/\D/', '', $telefono) ?? '');
    }
}
