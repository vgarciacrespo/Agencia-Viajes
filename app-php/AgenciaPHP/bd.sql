CREATE DATABASE IF NOT EXISTS agencia_viajes;
USE agencia_viajes;

-- Eliminar tablas anteriores si existen para reiniciar esquema
DROP TABLE IF EXISTS reservas;
DROP TABLE IF EXISTS viajes;
DROP TABLE IF EXISTS paquetes_turisticos;
DROP TABLE IF EXISTS paquetes;
DROP TABLE IF EXISTS proveedores;
DROP TABLE IF EXISTS usuarios;

-- 5. Proveedor
CREATE TABLE proveedores (
    id_proveedor VARCHAR(50) PRIMARY KEY,
    nombre_proveedor VARCHAR(100),
    pais_proveedor VARCHAR(100)
);

-- 4. Paquete Turistico
CREATE TABLE paquetes_turisticos (
    id_paquete VARCHAR(50) PRIMARY KEY,
    destino_pais VARCHAR(100),
    destino_ciudad VARCHAR(100),
    tipo_viaje VARCHAR(50),
    duracion INT,
    descripcion_detallada TEXT,
    itinerario TEXT,
    plazas INT,
    precio_base DOUBLE,
    id_proveedor VARCHAR(50),
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor) ON DELETE CASCADE
);

-- 3. Viaje (Instancia temporal)
CREATE TABLE viajes (
    id_viaje VARCHAR(50) PRIMARY KEY,
    id_paquete VARCHAR(50),
    fecha_salida DATE,
    fecha_llegada DATE,
    coeficiente_temporada DOUBLE, -- Multiplicador (ej. 1.2 para alta)
    plazas_disponibles INT DEFAULT 0,
    FOREIGN KEY (id_paquete) REFERENCES paquetes_turisticos(id_paquete) ON DELETE CASCADE
);

-- 1. Usuario
CREATE TABLE usuarios (
    dni VARCHAR(20) PRIMARY KEY,
    nombre VARCHAR(100),
    apellidos VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    password VARCHAR(255), -- 'contraseña'
    preferencias_usuario TEXT,
    tipo VARCHAR(50) -- Visitante, Usuario Registrado, Empleado
);

-- 2. Reserva
CREATE TABLE reservas (
    id_reserva VARCHAR(50) PRIMARY KEY,
    num_viajeros_adultos INT,
    num_viajeros_ninos INT,
    estado VARCHAR(50), -- Pendiente, Confirmada, Cancelada
    precio_final DOUBLE,
    dni_usuario VARCHAR(20),
    id_viaje VARCHAR(50),
    tipo_servicio VARCHAR(100),
    FOREIGN KEY (dni_usuario) REFERENCES usuarios(dni) ON DELETE CASCADE,
    FOREIGN KEY (id_viaje) REFERENCES viajes(id_viaje) ON DELETE CASCADE
);

-- INSERTS DE PRUEBA

-- Proveedores
INSERT INTO proveedores (id_proveedor, nombre_proveedor, pais_proveedor) VALUES
('PROV001', 'Viajes Mundo', 'Espana'),
('PROV002', 'Transportes Aereos Globales', 'USA'),
('PROV003', 'Ruta Aventura', 'Argentina'),
('PROV004', 'EuroTours', 'Alemania');

-- Paquetes
INSERT INTO paquetes_turisticos (id_paquete, destino_pais, destino_ciudad, tipo_viaje, duracion, descripcion_detallada, itinerario, plazas, precio_base, id_proveedor) VALUES
('PAQ001', 'Francia', 'Paris', 'Romantico', 5, 'Disfruta de la ciudad del amor con visitas a la Torre Eiffel y Louvre.', 'Dia 1: Llegada. Dia 2: Torre Eiffel. Dia 3: Louvre. Dia 4: Versalles. Dia 5: Regreso.', 20, 1200.00, 'PROV001'),
('PAQ002', 'Japon', 'Tokio', 'Cultural', 10, 'Inmersion en la cultura japonesa moderna y tradicional.', 'Dia 1-3: Tokio. Dia 4-6: Kyoto. Dia 7-10: Osaka y regreso.', 15, 2500.00, 'PROV002'),
('PAQ003', 'EEUU', 'Nueva York', 'Urbano', 7, 'La ciudad que nunca duerme. Times Square, Central Park y mas.', 'Dia 1: Llegada. Dia 2: Manhattan. Dia 3: Estatua Libertad. Dia 4: Broadway. Dia 5-7: Libre y regreso.', 25, 1800.50, 'PROV002'),
('PAQ004', 'Italia', 'Roma', 'Historico', 6, 'Descubre la historia del imperio romano.', 'Dia 1: Coliseo. Dia 2: Vaticano. Dia 3: Panteon. Dia 4-6: Libre.', 30, 950.00, 'PROV004'),
('PAQ005', 'Argentina', 'Buenos Aires', 'Gastronomico', 8, 'Tango, carne y vino en la capital argentina.', 'Dia 1: Llegada. Dia 2: La Boca. Dia 3: Palermo. Dia 4: Asado tradicional. Dia 5-8: Tigre y regreso.', 20, 1500.00, 'PROV003'),
('PAQ006', 'Reino Unido', 'Londres', 'Urbano', 5, 'Visita el Big Ben y el London Eye.', 'Dia 1: Llegada. Dia 2: Centro. Dia 3: Museos. Dia 4: Compras. Dia 5: Regreso.', 25, 1100.00, 'PROV004');

-- Viajes (Instancias)
-- PAQ001 (Paris): 5 dias
-- PAQ002 (Tokio): 10 dias
-- PAQ003 (NY): 7 dias
-- PAQ004 (Roma): 6 dias
-- PAQ005 (Buenos Aires): 8 dias
-- PAQ006 (Londres): 5 dias

INSERT INTO viajes (id_viaje, id_paquete, fecha_salida, fecha_llegada, coeficiente_temporada) VALUES
('VIAJE001', 'PAQ001', '2026-06-10', '2026-06-15', 1.2),
('VIAJE002', 'PAQ001', '2026-09-10', '2026-09-15', 1.0),
('VIAJE003', 'PAQ002', '2026-07-01', '2026-07-11', 1.5),
('VIAJE004', 'PAQ003', '2025-12-20', '2025-12-27', 1.8), 
('VIAJE005', 'PAQ003', '2026-05-15', '2026-05-22', 1.1),
('VIAJE006', 'PAQ004', '2026-04-10', '2026-04-16', 1.1),
('VIAJE007', 'PAQ004', '2026-08-20', '2026-08-26', 1.3),
('VIAJE008', 'PAQ005', '2026-11-05', '2026-11-13', 1.0),
('VIAJE009', 'PAQ006', '2026-03-15', '2026-03-20', 1.0),
('VIAJE010', 'PAQ006', '2026-12-05', '2026-12-10', 1.4),
('VIAJE011', 'PAQ001', '2026-07-20', '2026-07-25', 1.4), 
('VIAJE012', 'PAQ001', '2026-08-05', '2026-08-10', 1.4), 
('VIAJE013', 'PAQ002', '2026-04-10', '2026-04-20', 1.1), 
('VIAJE014', 'PAQ002', '2026-10-15', '2026-10-25', 1.0), 
('VIAJE015', 'PAQ003', '2026-06-05', '2026-06-12', 1.2), 
('VIAJE016', 'PAQ003', '2026-11-20', '2026-11-27', 1.5), 
('VIAJE017', 'PAQ004', '2026-05-10', '2026-05-16', 1.1), 
('VIAJE018', 'PAQ004', '2026-09-05', '2026-09-11', 1.2), 
('VIAJE019', 'PAQ005', '2026-02-14', '2026-02-22', 1.0), 
('VIAJE020', 'PAQ005', '2026-12-30', '2027-01-07', 1.5), 
('VIAJE021', 'PAQ006', '2026-06-15', '2026-06-20', 1.2), 
('VIAJE022', 'PAQ006', '2026-09-20', '2026-09-25', 1.1); 

-- Usuarios
INSERT INTO usuarios (dni, nombre, apellidos, email, telefono, password, preferencias_usuario, tipo) VALUES
('12345678K', 'Carlos', 'Fernandez', 'carlos@carlos.com', '600000000', '$2y$10$SMCb7UnMb4h1B6u8tuma..xxBxvN9XcX0s4cApJfWO9iPRMBCYIRC', '', 'admin'),
('USER001', 'Juan', 'Perez', 'usuario@usuario.com', '699000111', '$2y$10$SMCb7UnMb4h1B6u8tuma..xxBxvN9XcX0s4cApJfWO9iPRMBCYIRC', 'Me gusta la playa', 'usuario');

-- Reservas
INSERT INTO reservas (id_reserva, num_viajeros_adultos, num_viajeros_ninos, estado, precio_final, dni_usuario, id_viaje, tipo_servicio) VALUES
('RES001', 2, 0, 'Confirmada', 2880.00, '12345678K', 'VIAJE001', 'Todo Incluido'),
('RES002', 1, 1, 'Confirmada', 3000.00, '12345678K', 'VIAJE003', 'Media Pension'),
('RES003', 2, 2, 'Pendiente', 4500.50, '12345678K', 'VIAJE004', 'Solo Desayuno');

-- Actualizar plazas disponibles iniciales desde paquetes
UPDATE viajes v
JOIN paquetes_turisticos p ON v.id_paquete = p.id_paquete
SET v.plazas_disponibles = p.plazas;

