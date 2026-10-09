-- ============================================
-- BASE DE DATOS: control_asistencia_docentes
-- ============================================
CREATE DATABASE IF NOT EXISTS control_asistencia_docentes
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE control_asistencia_docentes;

-- ============================================
-- 1. SEGURIDAD
-- ============================================
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(150)
) ENGINE=InnoDB;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    ultimo_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- ============================================
-- 2. DOCENTES
-- ============================================
CREATE TABLE docentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNIQUE,
    dni VARCHAR(15) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(120),
    telefono VARCHAR(20),
    especialidad VARCHAR(100),
    foto_url VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ============================================
-- 3. ESTRUCTURA ACADÉMICA
-- ============================================
CREATE TABLE anio_escolar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anio INT NOT NULL UNIQUE,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE grados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    nivel VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE secciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grado_id INT NOT NULL,
    nombre VARCHAR(10) NOT NULL,
    FOREIGN KEY (grado_id) REFERENCES grados(id)
) ENGINE=InnoDB;

CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    area VARCHAR(50)
) ENGINE=InnoDB;

-- ============================================
-- 4. HORARIOS (referencial, no obliga a marcar por bloque)
-- ============================================
CREATE TABLE horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    docente_id INT NOT NULL,
    curso_id INT NOT NULL,
    seccion_id INT NOT NULL,
    anio_escolar_id INT NOT NULL,
    dia_semana TINYINT NOT NULL COMMENT '1=Lunes ... 7=Domingo',
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    aula VARCHAR(30),
    FOREIGN KEY (docente_id) REFERENCES docentes(id),
    FOREIGN KEY (curso_id) REFERENCES cursos(id),
    FOREIGN KEY (seccion_id) REFERENCES secciones(id),
    FOREIGN KEY (anio_escolar_id) REFERENCES anio_escolar(id),
    INDEX idx_docente_dia (docente_id, dia_semana)
) ENGINE=InnoDB;

-- ============================================
-- 5. CONFIGURACIÓN DEL SISTEMA
-- ============================================
CREATE TABLE configuracion (
    clave VARCHAR(50) PRIMARY KEY,
    valor VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255)
) ENGINE=InnoDB;

-- ============================================
-- 6. ASISTENCIA (una fila por docente por día)
-- ============================================
CREATE TABLE estados_asistencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    descripcion VARCHAR(100)
) ENGINE=InnoDB;

CREATE TABLE asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    docente_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_entrada DATETIME,
    hora_salida DATETIME,
    minutos_tardanza INT DEFAULT 0,
    estado_id INT NOT NULL,
    metodo_marcacion VARCHAR(20) DEFAULT 'qr',
    latitud DECIMAL(10,8),
    longitud DECIMAL(11,8),
    observacion VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (docente_id) REFERENCES docentes(id),
    FOREIGN KEY (estado_id) REFERENCES estados_asistencia(id),
    UNIQUE KEY uk_docente_fecha (docente_id, fecha),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB;

-- ============================================
-- 7. JUSTIFICACIONES
-- ============================================
CREATE TABLE justificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asistencia_id INT NOT NULL,
    motivo TEXT NOT NULL,
    archivo_url VARCHAR(255),
    aprobado_por INT,
    estado VARCHAR(20) DEFAULT 'pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta DATETIME,
    FOREIGN KEY (asistencia_id) REFERENCES asistencias(id),
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ============================================
-- 8. QR DINÁMICO (el que se muestra en la entrada)
-- ============================================
CREATE TABLE qr_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(255) NOT NULL UNIQUE,
    generado_en DATETIME NOT NULL,
    expira_en DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    docente_id INT NULL COMMENT 'Se llena cuando alguien lo usa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_expira (expira_en),
    FOREIGN KEY (docente_id) REFERENCES docentes(id)
) ENGINE=InnoDB;

-- ============================================
-- 9. TOKENS DE SESIÓN (JWT)
-- ============================================
CREATE TABLE tokens_sesion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(500) NOT NULL UNIQUE,
    dispositivo VARCHAR(100),
    ip VARCHAR(45),
    expira_en DATETIME NOT NULL,
    revocado TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_token (token(255))
) ENGINE=InnoDB;

-- ============================================
-- 10. AUDITORÍA
-- ============================================
CREATE TABLE logs_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(100),
    detalle TEXT,
    ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;