-- ============================================================
--  BackFlash - esquema de base de datos (MySQL / MariaDB)
--  Importar con:  mysql -u root < database/schema.sql
--  (o desde phpMyAdmin en XAMPP)
-- ============================================================

CREATE DATABASE IF NOT EXISTS backflash
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE backflash;

-- ------------------------------------------------------------
--  Usuarios del panel de administración
--  Usuario: admin   Contraseña: BackFlash2026!
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    usuario       VARCHAR(60)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    creado        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Platos del menú (platos y bebidas)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS platos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(120)   NOT NULL,
    descripcion VARCHAR(255)   NULL,
    precio      DECIMAL(10,0)  NOT NULL,
    categoria   ENUM('plato','bebida') NOT NULL DEFAULT 'plato',
    imagen      VARCHAR(255)   NULL,
    destacado   TINYINT(1)     NOT NULL DEFAULT 0,
    activo      TINYINT(1)     NOT NULL DEFAULT 1,
    orden       INT            NOT NULL DEFAULT 0,
    creado      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Mensajes del formulario de contacto
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS mensajes (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nombre   VARCHAR(120) NOT NULL,
    email    VARCHAR(160) NOT NULL,
    asunto   VARCHAR(160) NOT NULL,
    mensaje  TEXT         NOT NULL,
    leido    TINYINT(1)   NOT NULL DEFAULT 0,
    creado   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Reservas de mesa
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reservas (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(120) NOT NULL,
    email     VARCHAR(160) NOT NULL,
    telefono  VARCHAR(40)  NOT NULL,
    fecha     DATE         NOT NULL,
    hora      TIME         NOT NULL,
    personas  TINYINT UNSIGNED NOT NULL,
    mensaje   VARCHAR(500) NULL,
    estado    ENUM('pendiente','confirmada','cancelada') NOT NULL DEFAULT 'pendiente',
    creado    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Datos iniciales
-- ============================================================

INSERT INTO usuarios (usuario, password_hash) VALUES
('admin', '$2y$10$aIoew9QTg/WHhWBKo5aPJOvLGBiSUZT80UTBaRuIDqStrD9D27mMO')
ON DUPLICATE KEY UPDATE usuario = usuario;

INSERT INTO platos (nombre, descripcion, precio, categoria, imagen, destacado, orden) VALUES
('Bandeja paisa',      'Frijoles, carne molida, chicharrón, huevo, arepa y aguacate', 20000, 'plato', 'img/platos/bandeja-paisa.svg', 1, 1),
('Estofado de carne',  'Carne en estofado con papas y vegetales',                     38000, 'plato', 'img/platos/estofado.svg',      1, 2),
('Sancocho de pollo',  'Sancocho con yuca, plátano y mazorca',                         25000, 'plato', 'img/platos/sancocho-pollo.svg', 1, 3),
('Sancocho de pescado','Sancocho de pescado con patacón y aguapanela',                 28000, 'plato', 'img/platos/sancocho-pescado.svg', 0, 4),
('Espaguetis a la carbonara', 'Espaguetis con tocino, huevo y parmesano',              22000, 'plato', 'img/platos/carbonara.svg',      1, 5),
('Jugo de mora',       'Jugo natural de mora servido con hielo',                       6000, 'bebida', 'img/bebidas/jugo-mora.svg',     0, 6),
('Jugo de maracuyá',   'Jugo natural de maracuyá',                                      6000, 'bebida', 'img/bebidas/jugo-maracuya.svg', 0, 7),
('Jugo de mango',      'Jugo natural de mango',                                         6000, 'bebida', 'img/bebidas/jugo-mango.svg',    0, 8),
('Jugo de lulo',       'Jugo natural de lulo',                                           6000, 'bebida', 'img/bebidas/jugo-lulo.svg',     0, 9),
('Café tinto',         'Café colombiano pasado',                                         4000, 'bebida', 'img/bebidas/cafe-tinto.svg',    0, 10)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- Mensaje y reserva de ejemplo para probar el panel
INSERT INTO mensajes (nombre, email, asunto, mensaje) VALUES
('Ana Pérez', 'ana@correo.com', 'Reserva para el sábado', 'Hola, quisiera reservar una mesa para 4 personas el sábado en la noche.');

INSERT INTO reservas (nombre, email, telefono, fecha, hora, personas, mensaje) VALUES
('Carlos Gómez', 'carlos@correo.com', '3005551212', CURDATE() + INTERVAL 2 DAY, '19:30:00', 4, 'Mesa cerca de la ventana si es posible');
