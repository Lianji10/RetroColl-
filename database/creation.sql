-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS RetroColl;
USE RetroColl;

-- 1. Tablas Independientes (Sin claves foráneas)
CREATE TABLE USUARIO (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_registro DATE DEFAULT (CURRENT_DATE),
    valoracion_promedio DECIMAL(3,2) DEFAULT 0
);

CREATE TABLE CATEGORIA (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE PLATAFORMA (
    id_plataforma INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL -- Ej: SNES, PS1
);

CREATE TABLE CERTIFICADO (
    id_certificado INT AUTO_INCREMENT PRIMARY KEY,
    archivo_url VARCHAR(255),
    fecha_emision DATE,
    es_valido BOOLEAN DEFAULT TRUE
);

-- 2. Tabla Principal (Con claves foráneas)
CREATE TABLE PRODUCTO (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    estado VARCHAR(50), -- Ej: Mint, Good
    fecha_publicacion DATE DEFAULT (CURRENT_DATE),
    
    -- Claves Foráneas
    id_vendedor INT NOT NULL,
    id_categoria INT NOT NULL,
    id_plataforma INT NOT NULL,
    id_certificado INT UNIQUE, -- UNIQUE para relación 1:1
    
    -- Definición de Relaciones
    FOREIGN KEY (id_vendedor) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES CATEGORIA(id_categoria),
    FOREIGN KEY (id_plataforma) REFERENCES PLATAFORMA(id_plataforma),
    FOREIGN KEY (id_certificado) REFERENCES CERTIFICADO(id_certificado)
);

-- 3. Tablas Transaccionales
CREATE TABLE COMPRA (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    precio_final DECIMAL(10, 2) NOT NULL,
    
    id_comprador INT NOT NULL,
    id_producto INT UNIQUE NOT NULL, -- UNIQUE para asegurar que un producto solo se compra una vez
    
    FOREIGN KEY (id_comprador) REFERENCES USUARIO(id_usuario),
    FOREIGN KEY (id_producto) REFERENCES PRODUCTO(id_producto)
);

CREATE TABLE VALORACION (
    id_valoracion INT AUTO_INCREMENT PRIMARY KEY,
    puntuacion INT CHECK (puntuacion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha DATE DEFAULT (CURRENT_DATE),
    
    id_emisor INT NOT NULL,
    id_receptor INT NOT NULL,
    
    FOREIGN KEY (id_emisor) REFERENCES USUARIO(id_usuario),
    FOREIGN KEY (id_receptor) REFERENCES USUARIO(id_usuario)
);