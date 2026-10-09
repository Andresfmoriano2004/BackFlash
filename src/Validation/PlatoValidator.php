<?php
/**
 * Reglas del alta/edición de platos y bebidas (panel de administración).
 * Antes vivían sueltas dentro de admin/platos.php, en medio del HTML.
 *
 * La subida de imagen NO se valida aquí: depende de $_FILES y del disco. El
 * controlador la resuelve y, si falla, añade el error con agregarError().
 */

declare(strict_types=1);

namespace BackFlash\Validation;

final class PlatoValidator extends Validador
{
    private const CATEGORIAS = ['plato', 'bebida'];

    /**
     * @param array<string, mixed> $datos
     */
    public function validar(array $datos): bool
    {
        $this->errores = [];

        $nombre    = (string) ($datos['nombre'] ?? '');
        $precio    = (string) ($datos['precio'] ?? '');
        $categoria = (string) ($datos['categoria'] ?? '');

        if (mb_strlen($nombre) < Reglas::PLATO_NOMBRE_MIN) {
            $this->falla('nombre', 'Escribe el nombre del plato.');
        }

        if (!preg_match('/^\d{1,9}([.,]\d{1,2})?$/', $precio)) {
            $this->falla('precio', 'Escribe un precio válido, por ejemplo 20000.');
        }

        if (!in_array($categoria, self::CATEGORIAS, true)) {
            $this->falla('categoria', 'Elige la categoría.');
        }

        return $this->valido();
    }

    /** @return list<string> */
    public static function categorias(): array
    {
        return self::CATEGORIAS;
    }
}
