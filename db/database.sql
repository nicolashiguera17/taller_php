

-- configuracion de la base de datos --
SHOW DATABASES;

CREATE DATABASE IF NOT EXISTS `taller_api`;

USE `taller_api`;

SHOW TABLES;

-- eliminar las tablas  --

DROP TABLE IF EXISTS promociones;

DROP TABLE IF EXISTS productos;

DROP TABLE IF EXISTS categorias;


-- tabla de categoria  --

CREATE TABLE IF NOT EXISTS categorias (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL UNIQUE
);

-- tabla de productos --
CREATE TABLE IF NOT EXISTS productos (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    precio DECIMAL(10,2) NOT NULL,
    categoria_id INT NOT NULL
);

-- tabla de promociones --
CREATE TABLE IF NOT EXISTS promociones (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    descripcion TEXT NOT NULL,
    descuento DECIMAL(10,2) NOT NULL, 
    producto_id INT NOT NULL
);

-- llaves foraneas  --

ALTER TABLE productos ADD CONSTRAINT productos_categoria_id
FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE;

ALTER TABLE promociones ADD CONSTRAINT promociones_producto_id
FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE;

-- datos de categoria --

INSERT INTO categorias(nombre) VALUES
('Ropa'),
('Libros '),
('Electrodomesticos'),
('Ferretería'),
('Automoviles'),
('Juguetes'),
('Muebles'),
('Cocina');


INSERT INTO productos (nombre, precio, categoria_id) VALUES
('Conjunto deportivo', 59.99, 1),
('Pantalón jeans', 89.90, 1),
('Novela "Cien Años de Soledad"', 45.00, 2),
('Enciclopedia Escolar', 120.00, 2),
('Nevera', 199.99, 3),
('Taladro Black & Decker', 240.00, 4),
('Llantas Michelin 15"', 350.00, 5),
('Carro a control remoto', 75.00, 6),
('Sofá de tres puestos', 899.99, 7),
('Juego de ollas acero inoxidable', 320.50, 8);

INSERT INTO promociones (descripcion, descuento, producto_id) VALUES
('¿¡Qué mejor que tener un conjunto con un 10% menos que pagar!?', 10.00, 1),
('¡Aprovecha ahora el descuento del 22% en esta nevera grandiosa!', 22.00, 5),
('Tu Taladro Black & Decker favorito con 12% de descuento por tiempo indefinido', 12.00, 6),
('Carro a control remoto con rebaja del 7% para quienes saben jugar con su economía', 7.00, 8),
('Descuento de 30% en sofá para tu comodidad y la de tu bolsillo', 30.00, 9);



-- ver los datos de las tablas --

SELECT id, nombre FROM categorias;

SELECT id, nombre, precio, categoria_id FROM productos;

SELECT id, descripcion, descuento, producto_id FROM promociones;