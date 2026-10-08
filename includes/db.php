<?php
/**
 * Conexión a la base de datos (PDO + consultas preparadas).
 */

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            exit('No se pudo conectar con la base de datos "' . htmlspecialchars(DB_NAME)
                . '". Revisa includes/config.php o importa database/schema.sql. Detalle: '
                . htmlspecialchars($e->getMessage()));
        }
    }

    return $pdo;
}

/**
 * Ejecuta una consulta preparada y devuelve el statement.
 */
function q(string $sql, array $params = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}
