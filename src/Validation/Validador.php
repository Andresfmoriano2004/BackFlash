<?php
/**
 * Base de los validadores.
 *
 * Un validador NO conoce $_POST, ni la sesión, ni la base de datos: recibe un
 * array de datos ya recortados y devuelve un mapa campo => mensaje. Eso es lo
 * que lo hace comprobable sin HTTP ni MySQL.
 *
 * El primer mensaje de cada campo es el que se muestra; los siguientes se
 * descartan para no encadenar avisos sobre el mismo input.
 */

declare(strict_types=1);

namespace BackFlash\Validation;

abstract class Validador
{
    /** @var array<string, string> campo => mensaje */
    protected array $errores = [];

    /**
     * Valida los datos y devuelve true si no hay errores.
     *
     * @param array<string, mixed> $datos
     */
    abstract public function validar(array $datos): bool;

    /** @return array<string, string> */
    public function errores(): array
    {
        return $this->errores;
    }

    public function valido(): bool
    {
        return $this->errores === [];
    }

    /** Registra un error solo si ese campo aún no tenía uno. */
    protected function falla(string $campo, string $mensaje): void
    {
        if (!isset($this->errores[$campo])) {
            $this->errores[$campo] = $mensaje;
        }
    }

    /**
     * Añade un error calculado fuera del validador (por ejemplo la subida de
     * imagen, que depende de $_FILES y del disco).
     */
    public function agregarError(string $campo, string $mensaje): void
    {
        $this->falla($campo, $mensaje);
    }

    /** Cuenta de dígitos de un teléfono. */
    protected function digitos(string $telefono): int
    {
        return Reglas::digitosTelefono($telefono);
    }
}
