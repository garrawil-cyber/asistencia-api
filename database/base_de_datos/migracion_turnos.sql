-- ============================================================
-- MIGRACIÓN: Sistema Multi-Turno
-- Versión 2 (idempotente)
-- Fecha: 15/10/2026
-- ============================================================

USE control_asistencia_docentes;

-- ------------------------------------------------------------
-- 1. Tabla TURNOS (idempotente)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    hora_entrada TIME NOT NULL,
    hora_salida TIME NOT NULL,
    tolerancia_minutos INT DEFAULT 5,
    ventana_entrada_inicio TIME NOT NULL,
    ventana_entrada_fin TIME NOT NULL,
    ventana_salida_inicio TIME NOT NULL,
    ventana_salida_fin TIME NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insertar turnos (IGNORE evita duplicados)
INSERT IGNORE INTO turnos 
    (codigo, nombre, hora_entrada, hora_salida, tolerancia_minutos,
     ventana_entrada_inicio, ventana_entrada_fin,
     ventana_salida_inicio, ventana_salida_fin)
VALUES
    ('manana', 'Turno Mañana', '07:00:00', '12:45:00', 5,
     '05:00:00', '09:00:00', '12:00:00', '15:00:00'),
    ('tarde', 'Turno Tarde', '13:00:00', '18:45:00', 5,
     '12:00:00', '15:00:00', '18:00:00', '21:00:00');

-- ------------------------------------------------------------
-- 2. Estados de salida (IGNORE)
-- ------------------------------------------------------------
INSERT IGNORE INTO estados_asistencia (codigo, descripcion) VALUES
    ('salida_anticipada', 'Salió antes de la hora oficial'),
    ('salida_ok', 'Salió en el horario correcto'),
    ('salida_tardia', 'Salió después de la hora oficial');

-- ------------------------------------------------------------
-- 3. Columnas nuevas (usar procedimiento para evitar errores)
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS agregar_columna_si_no_existe;

DELIMITER //
CREATE PROCEDURE agregar_columna_si_no_existe(
    IN tabla_name VARCHAR(64),
    IN columna_name VARCHAR(64),
    IN definicion TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = tabla_name
          AND COLUMN_NAME = columna_name
    ) THEN
        SET @sql = CONCAT('ALTER TABLE ', tabla_name, ' ADD COLUMN ', definicion);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

CALL agregar_columna_si_no_existe('docentes', 'turno_id', 'turno_id INT NULL AFTER especialidad');
CALL agregar_columna_si_no_existe('asistencias', 'turno_id', 'turno_id INT NULL AFTER docente_id');
CALL agregar_columna_si_no_existe('asistencias', 'bloque', 'bloque VARCHAR(20) NULL AFTER turno_id');
CALL agregar_columna_si_no_existe('asistencias', 'estado_salida', 'estado_salida VARCHAR(30) NULL AFTER observacion');

DROP PROCEDURE IF EXISTS agregar_columna_si_no_existe;

-- ------------------------------------------------------------
-- 4. Asignar turno mañana a docentes sin turno
-- ------------------------------------------------------------
UPDATE docentes 
SET turno_id = (SELECT id FROM turnos WHERE codigo = 'manana')
WHERE turno_id IS NULL;

-- ------------------------------------------------------------
-- 5. Migrar asistencias existentes (turno_id y bloque a valores por defecto)
-- ------------------------------------------------------------
UPDATE asistencias 
SET turno_id = (SELECT id FROM turnos WHERE codigo = 'manana')
WHERE turno_id IS NULL;

UPDATE asistencias 
SET bloque = 'entrada'
WHERE bloque IS NULL;

-- ------------------------------------------------------------
-- 6. Verificación
-- ------------------------------------------------------------
SELECT '✓ Migración completada' AS mensaje;
SELECT COUNT(*) AS total_turnos FROM turnos;
SELECT COUNT(*) AS total_estados_salida FROM estados_asistencia WHERE codigo LIKE 'salida_%';
SELECT COUNT(*) AS docentes_con_turno FROM docentes WHERE turno_id IS NOT NULL;