-- Inicialización completa del esquema para Clínica Dental
CREATE DATABASE IF NOT EXISTS clinica_dental_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clinica_dental_db;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tabla de Roles (RBAC)
CREATE TABLE IF NOT EXISTS roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabla de Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_rol INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_roles FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabla de Médicos
CREATE TABLE IF NOT EXISTS medicos (
    id_medico INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    especialidad VARCHAR(100) NOT NULL,
    numero_junta VARCHAR(50) NULL,
    telefono VARCHAR(20) NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_medicos_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabla de Horarios de Atención
CREATE TABLE IF NOT EXISTS horarios_atencion (
    id_horario INT AUTO_INCREMENT PRIMARY KEY,
    id_medico INT NOT NULL,
    dia_semana ENUM('Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_horarios_medicos FOREIGN KEY (id_medico) REFERENCES medicos(id_medico) ON DELETE CASCADE,
    INDEX idx_medico_dia (id_medico, dia_semana)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabla de Categorías de Servicio 
CREATE TABLE IF NOT EXISTS categorias_servicio (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabla de Servicios / Catálogo Dental
CREATE TABLE IF NOT EXISTS servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT NULL,
    duracion_minutos INT NOT NULL DEFAULT 30,
    precio_ref DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_servicios_categorias FOREIGN KEY (id_categoria) REFERENCES categorias_servicio(id_categoria) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabla de Pacientes y Expedientes
CREATE TABLE IF NOT EXISTS pacientes (
    id_paciente INT AUTO_INCREMENT PRIMARY KEY,
    codigo_expediente VARCHAR(30) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    documento_identidad VARCHAR(20) NULL,
    fecha_nacimiento DATE NULL,
    genero ENUM('M', 'F', 'Otro') NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(150) NULL,
    direccion TEXT NULL,
    antecedentes_medicos TEXT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_busqueda_paciente (nombres, apellidos, telefono, codigo_expediente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabla de Citas 
CREATE TABLE IF NOT EXISTS citas (
    id_cita INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_medico INT NOT NULL,
    id_servicio INT NOT NULL,
    id_usuario_registro INT NOT NULL,
    fecha_hora_inicio DATETIME NOT NULL,
    fecha_hora_fin DATETIME NOT NULL,
    estado ENUM('Programada', 'Confirmada', 'Atendida', 'Cancelada') DEFAULT 'Programada',
    motivo_consulta VARCHAR(255) NULL,
    observaciones TEXT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_citas_pacientes FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente) ON UPDATE CASCADE,
    CONSTRAINT fk_citas_medicos FOREIGN KEY (id_medico) REFERENCES medicos(id_medico) ON UPDATE CASCADE,
    CONSTRAINT fk_citas_servicios FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio) ON UPDATE CASCADE,
    CONSTRAINT fk_citas_usuarios FOREIGN KEY (id_usuario_registro) REFERENCES usuarios(id_usuario),
    INDEX idx_citas_traslape (id_medico, fecha_hora_inicio, fecha_hora_fin, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabla de Configuración de la Clínica
CREATE TABLE IF NOT EXISTS configuracion_clinica (
    id_config INT PRIMARY KEY DEFAULT 1,
    nombre_clinica VARCHAR(100) NOT NULL DEFAULT 'UnionDental',
    telefono VARCHAR(20) DEFAULT '2222-0000',
    email VARCHAR(100) DEFAULT 'contacto@uniondental.com',
    direccion VARCHAR(255) DEFAULT 'San Salvador, El Salvador',
    horario_apertura TIME DEFAULT '08:00:00',
    horario_cierre TIME DEFAULT '17:00:00',
    duracion_slot_minutos INT DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Tabla para Almacenamiento de Documentos / Expedientes PDF
CREATE TABLE IF NOT EXISTS paciente_documentos (
    id_documento INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_usuario_subio INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    tamano_bytes INT NOT NULL,
    descripcion VARCHAR(255) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_docs_pacientes FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente) ON DELETE CASCADE,
    CONSTRAINT fk_docs_usuarios FOREIGN KEY (id_usuario_subio) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Tabla de Consultas Médicas / Evolución Clínica
CREATE TABLE IF NOT EXISTS consultas_medicas (
    id_consulta INT AUTO_INCREMENT PRIMARY KEY,
    id_cita INT NOT NULL UNIQUE,
    id_paciente INT NOT NULL,
    id_medico INT NOT NULL,
    fecha_consulta DATETIME DEFAULT CURRENT_TIMESTAMP,
    motivo_consulta TEXT NOT NULL,
    sintomas TEXT NULL,
    diagnostico TEXT NOT NULL,
    tratamiento_realizado TEXT NOT NULL,
    receta_medica TEXT NULL,
    notas_observaciones TEXT NULL,
    proxima_cita_sugerida DATE NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_consulta_cita FOREIGN KEY (id_cita) REFERENCES citas(id_cita) ON UPDATE CASCADE,
    CONSTRAINT fk_consulta_paciente FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente) ON DELETE CASCADE,
    CONSTRAINT fk_consulta_medico FOREIGN KEY (id_medico) REFERENCES medicos(id_medico) ON UPDATE CASCADE,
    INDEX idx_consultas_paciente (id_paciente, fecha_consulta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- DATOS SEMILLA (SEED DATA)
-- =====================================================

-- Roles del sistema
INSERT IGNORE INTO roles (id_rol, nombre, descripcion) VALUES
(1, 'Administrador', 'Control total de la plataforma'),
(2, 'Recepcionista', 'Gestión de citas, pacientes y cobros/servicios'),
(3, 'Doctor', 'Consulta de agenda, horarios y expedientes'),
(4, 'Cliente', 'Visualización de citas y perfil');

-- Usuario Administrador Inicial (Password: Admin1234!)
INSERT IGNORE INTO usuarios (id_usuario, id_rol, nombre, apellido, email, password_hash, estado) VALUES
(1, 1, 'Admin', 'Sistema', 'admin@clinicadental.local', '$2y$10$wcCVUCY6sF.FdT5hnEz5yuCM..5t1EVf6aDzejGhN7IDsP64Mi8KW', 'activo');

-- Configuración Institucional Inicial
INSERT IGNORE INTO configuracion_clinica (id_config, nombre_clinica, telefono, email, direccion) VALUES
(1, 'UnionDental', '2222-0000', 'contacto@uniondental.com', 'San Salvador, El Salvador');

-- Catálogo Completo de Categorías en UTF-8
INSERT INTO categorias_servicio (id_categoria, nombre, descripcion, estado) VALUES
(1, 'Atención General', 'Consultas diagnósticas, limpiezas y profilaxis preventiva', 'activo'),
(2, 'Atención Especializada', 'Tratamientos complejos y procedimientos avanzados', 'activo'),
(3, 'Ortodoncia', 'Corrección de posición dental, brackets y alineadores', 'activo'),
(4, 'Endodoncia', 'Tratamientos de conductos y pulpa dental', 'activo'),
(5, 'Periodoncia', 'Tratamiento de encías, hueso y tejido de soporte', 'activo'),
(6, 'Odontopediatría', 'Atención integral odontológica para niños', 'activo'),
(7, 'Cirugía Maxilofacial', 'Extracciones quirúrgicas de cordales y cirugías orales', 'activo'),
(8, 'Estética Dental', 'Diseño de sonrisa, carillas y blanqueamiento', 'activo'),
(9, 'Rehabilitación Oral', 'Prótesis fijas, removibles e implantes dentales', 'activo')
ON DUPLICATE KEY UPDATE 
    nombre = VALUES(nombre),
    descripcion = VALUES(descripcion),
    estado = 'activo';

-- Servicios base iniciales
INSERT IGNORE INTO servicios (id_servicio, id_categoria, nombre, descripcion, duracion_minutos, precio_ref, estado) VALUES
(1, 1, 'Consulta General', 'Evaluación odontológica diagnóstica', 30, 25.00, 'activo'),
(2, 1, 'Limpieza Dental', 'Limpieza preventiva y profilaxis', 45, 30.00, 'activo'),
(3, 4, 'Endodoncia Unirradicular', 'Tratamiento de conducto en pieza anterior', 60, 120.00, 'activo');