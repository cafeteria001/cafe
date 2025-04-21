-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS bar_cafe_db;
USE bar_cafe_db;

-- Tabla de categorías de productos
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de productos (carta)
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255),
    destacado BOOLEAN DEFAULT FALSE,
    disponible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- Tabla de eventos/recitales
CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    imagen VARCHAR(255),
    cupo_maximo INT,
    precio_entrada DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de mesas
CREATE TABLE IF NOT EXISTS mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(10) NOT NULL,
    capacidad INT NOT NULL,
    ubicacion VARCHAR(50),
    estado ENUM('disponible', 'reservada', 'ocupada') DEFAULT 'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de reservas
CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mesa_id INT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    num_personas INT NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada', 'completada') DEFAULT 'pendiente',
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id) ON DELETE SET NULL
);

-- Tabla de invitados para eventos
CREATE TABLE IF NOT EXISTS invitados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    estado ENUM('pendiente', 'confirmado', 'cancelado') DEFAULT 'pendiente',
    codigo_acceso VARCHAR(20) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE
);

-- Tabla de usuarios administradores
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    ultimo_acceso TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar categorías de ejemplo
INSERT INTO categorias (nombre, descripcion) VALUES
('Café', 'Variedades de café y bebidas calientes'),
('Pastelería', 'Productos frescos de panadería y pastelería'),
('Bebidas', 'Refrescos, zumos y bebidas frías'),
('Comidas', 'Platos para desayunos y almuerzos');

-- Insertar productos de ejemplo
INSERT INTO productos (categoria_id, nombre, descripcion, precio, destacado) VALUES
(1, 'Café Espresso', 'Café concentrado con un aroma intenso', 1.50, TRUE),
(1, 'Cappuccino', 'Café con leche y espuma de leche', 2.50, TRUE),
(2, 'Croissant', 'Croissant de mantequilla recién horneado', 1.80, TRUE),
(2, 'Tarta de Zanahoria', 'Tarta casera con zanahoria y frosting de queso', 3.50, FALSE),
(3, 'Zumo de Naranja', 'Zumo natural de naranja exprimido al momento', 2.00, TRUE),
(4, 'Sandwich Vegetariano', 'Con aguacate, queso, tomate y lechuga', 4.50, FALSE);

-- Insertar mesas de ejemplo
INSERT INTO mesas (numero, capacidad, ubicacion) VALUES
('A1', 2, 'Ventana'),
('A2', 2, 'Ventana'),
('B1', 4, 'Centro'),
('B2', 4, 'Centro'),
('C1', 6, 'Terraza'),
('C2', 8, 'Terraza');

-- Insertar usuario administrador
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Administrador', 'admin@barcafe.com', '$2y$10$uLRoSB7zU1x5Fk6BDkX5UeVJMjN4JEJbCg1CrSi2NA7UO2UysCbWS', 'admin');
-- (La contraseña en el hash es "admin123")