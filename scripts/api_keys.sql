USE control_asistencia_docentes;

CREATE TABLE IF NOT EXISTS api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    api_key VARCHAR(64) NOT NULL UNIQUE,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO api_keys (nombre, api_key)
VALUES ('Kiosko Entrada Principal', SHA2(CONCAT('KIOSKO_ENTRADA_', UUID()), 256));

-- Ver la key generada (guárdala para el kiosko)
SELECT id, nombre, api_key FROM api_keys;