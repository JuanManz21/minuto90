-- ============================================================
-- Minuto 90' - Base de datos
-- Importar este archivo en phpMyAdmin (pestaña "Importar")
-- o ejecutar: mysql -u root -p < minuto90.sql
-- ============================================================

DROP DATABASE IF EXISTS minuto90;
CREATE DATABASE minuto90 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE minuto90;

-- ------------------------------------------------------------
-- USUARIOS
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(120)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,   -- guardada con password_hash()
    direccion   VARCHAR(200)  DEFAULT NULL,
    telefono    VARCHAR(40)   DEFAULT NULL,
    rol         ENUM('usuario', 'admin') NOT NULL DEFAULT 'usuario',
    creado_en   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- CATEGORIAS
-- ------------------------------------------------------------
CREATE TABLE categorias (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    slug    VARCHAR(50)  NOT NULL UNIQUE,  -- se usa en la URL: catalogo.php?cat=hombre
    nombre  VARCHAR(80)  NOT NULL
) ENGINE=InnoDB;

INSERT INTO categorias (slug, nombre) VALUES
('novedades', 'Novedades'),
('hombre',    'Hombre'),
('ninos',     'Niños'),
('guayos',    'Guayos'),
('descuento', 'Descuento');

-- ------------------------------------------------------------
-- PRODUCTOS
-- ------------------------------------------------------------
CREATE TABLE productos (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(150)   NOT NULL,
    descripcion   VARCHAR(255)   DEFAULT NULL,
    precio        INT            NOT NULL,          -- en pesos, sin decimales
    precio_antes  INT            DEFAULT NULL,      -- precio tachado (opcional)
    descuento     INT            DEFAULT NULL,      -- porcentaje, ej: 20
    imagen        VARCHAR(150)   NOT NULL,          -- nombre del archivo en /img
    tallas        VARCHAR(120)   NOT NULL DEFAULT 'M,L,XL',
    categoria_id  INT            NOT NULL,
    destacado     TINYINT(1)     NOT NULL DEFAULT 0, -- aparece en el home
    creado_en     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- NOVEDADES (categoria_id = 1)
INSERT INTO productos (nombre, descripcion, precio, imagen, tallas, categoria_id, destacado) VALUES
('Uniforme Real Madrid',      'Conjunto de uniforme para hombres', 524550, 'CamisaR.png',  'M,L,XL', 1, 0),
('Uniforme Fc Barcelona',     'Conjunto de uniforme para hombres', 520000, 'CamisaB.png',  'M,L,XL', 1, 0),
('Uniforme Psg',              'Conjunto de uniforme para hombres', 627000, 'CamisaP.png',  'M,L,XL', 1, 0),
('Camiseta Liverpool 25/26',  'Camiseta para hombres',             498000, 'CamisaL.png',  'M,L,XL', 1, 0),
('Camiseta Arsenal 25/26',    'Camiseta para hombres',             470000, 'CamisaA.png',  'M,L,XL', 1, 0),
('Camiseta Fc Bayern 25/26',  'Camiseta para hombres',             510000, 'CamisaBD.png', 'M,L,XL', 1, 0);

-- HOMBRE (categoria_id = 2)
INSERT INTO productos (nombre, descripcion, precio, imagen, tallas, categoria_id, destacado) VALUES
('Camiseta del Liverpool',           'Camiseta para hombres', 379000, 'CamisaL.png',  'M,L,XL', 2, 0),
('Camiseta 25/26 del Real Madrid',   'Camiseta para hombres', 479000, 'CamisaRA.png', 'M,L,XL', 2, 0),
('Camiseta del Arsenal',             'Camiseta para hombres', 429000, 'CamisaA.png',  'M,L,XL', 2, 0),
('Camiseta del Fc Barcelona',        'Camiseta para hombres', 459000, 'CamisaB.png',  'M,L,XL', 2, 0),
('Camiseta del Psg',                 'Camiseta para hombres', 489000, 'CamisaP.png',  'M,L,XL', 2, 0),
('Camiseta del Milan AC',            'Camiseta para hombres', 445000, 'CamisaMD.png', 'M,L,XL', 2, 0);

-- NIÑOS (categoria_id = 3)
INSERT INTO productos (nombre, descripcion, precio, imagen, tallas, categoria_id, destacado) VALUES
('Uniforme Real Madrid Kids',   'Conjunto de uniforme para niños', 500550, 'UniRN.png', '8,10,12,16', 3, 0),
('Uniforme Fc Barcelona Kids',  'Conjunto de uniforme para niños', 350000, 'UniBN.png', '8,10,12,16', 3, 0),
('Uniforme Psg Kids',           'Conjunto de uniforme para niños', 330000, 'UniPN.png', '8,10,12,16', 3, 0),
('Camiseta Real Madrid Kids',   'Camiseta para niños',             280000, 'UniRN.png', '8,10,12,16', 3, 0),
('Camiseta Fc Barcelona Kids',  'Camiseta para niños',             265000, 'UniBN.png', '8,10,12,16', 3, 0),
('Camiseta Psg Kids',           'Camiseta para niños',             259000, 'UniPN.png', '8,10,12,16', 3, 0);

-- GUAYOS (categoria_id = 4)
INSERT INTO productos (nombre, descripcion, precio, imagen, tallas, categoria_id, destacado) VALUES
('Adidas F50 LEAGUE',        'Guayos para todos', 300000, 'GuayosF50.png', '8.5,9,9.5,10.5,11,11.5,12,12.5', 4, 0),
('Guayos Nike Mercurial',    'Guayos para todos', 504990, 'GuayosNM.png',  '8.5,9,9.5,10.5,11,11.5,12,12.5', 4, 0),
('Puma Future Z 1.1',        'Guayos para todos', 209950, 'GuayosPF.png',  '8.5,9,9.5,10.5,11,11.5,12,12.5', 4, 0),
('Adidas F50 ELITE',         'Guayos para todos', 450000, 'GuayosF50.png', '8.5,9,9.5,10.5,11,11.5,12,12.5', 4, 0),
('Nike Mercurial Superfly',  'Guayos para todos', 629000, 'GuayosNM.png',  '8.5,9,9.5,10.5,11,11.5,12,12.5', 4, 0),
('Puma Future Ultimate',     'Guayos para todos', 389000, 'GuayosPF.png',  '8.5,9,9.5,10.5,11,11.5,12,12.5', 4, 0);

-- DESCUENTO (categoria_id = 5)
INSERT INTO productos (nombre, descripcion, precio, precio_antes, descuento, imagen, tallas, categoria_id, destacado) VALUES
('Equipacion Liverpool 25/26',    'Conjunto de uniforme para hombres', 180000, 225000, 20, 'CamisalD.png', 'M,L,XL', 5, 0),
('Equipacion Milan AC 25/26',     'Conjunto de uniforme para hombres', 220000, 293000, 25, 'CamisaMD.png', 'M,L,XL', 5, 0),
('Equipacion FC Bayern 25/26',    'Conjunto de uniforme para hombres', 215000, 253000, 15, 'BayernD.png',  'M,L,XL', 5, 1),
('Equipacion Real Madrid 25/26',  'Conjunto de uniforme para hombres', 115000, 230000, 50, 'RealMD.png',   'M,L,XL', 5, 1),
('Equipacion Fc Barcelona 25/26', 'Conjunto de uniforme para hombres', 260000, 289000, 10, 'CamisaB.png',  'M,L,XL', 5, 0),
('Equipacion Psg 25/26',          'Conjunto de uniforme para hombres', 240000, 293000, 18, 'CamisaP.png',  'M,L,XL', 5, 0);

-- ------------------------------------------------------------
-- PEDIDOS
-- ------------------------------------------------------------
CREATE TABLE pedidos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id      INT           NOT NULL,
    numero_pedido   VARCHAR(20)   NOT NULL UNIQUE,
    nombre_envio    VARCHAR(120)  NOT NULL,
    direccion       VARCHAR(200)  NOT NULL,
    ciudad          VARCHAR(120)  NOT NULL,
    telefono        VARCHAR(40)   NOT NULL,
    metodo_pago     VARCHAR(60)   NOT NULL,
    subtotal        INT           NOT NULL,
    envio           INT           NOT NULL DEFAULT 5000,
    total           INT           NOT NULL,
    estado          ENUM('bodega','enviado','recibido') NOT NULL DEFAULT 'bodega',
    creado_en       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedido_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- DETALLE DE CADA PEDIDO
-- ------------------------------------------------------------
CREATE TABLE pedido_items (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id        INT           NOT NULL,
    producto_id      INT           DEFAULT NULL,
    nombre_producto  VARCHAR(150)  NOT NULL,  -- se guarda copia por si el producto cambia
    imagen           VARCHAR(150)  DEFAULT NULL,
    talla            VARCHAR(20)   NOT NULL,
    cantidad         INT           NOT NULL DEFAULT 1,
    precio_unitario  INT           NOT NULL,
    CONSTRAINT fk_item_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_item_producto
        FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- USUARIOS DE PRUEBA Y ADMINISTRADOR
-- ------------------------------------------------------------
-- Usuario Administrador creado por defecto.
-- Correo: admin@minuto90.com
-- Contraseña: admin123
INSERT INTO usuarios (nombre, email, password, direccion, telefono, rol) VALUES
('Administrador', 'admin@minuto90.com', '$2y$10$JXibhBZePIt6saA7ftE36.F3FAZYHuK2dwpiOjy9ye56GdySA3d2q', 'Oficina Principal', '3000000000', 'admin');
