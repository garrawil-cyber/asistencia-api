USE control_asistencia_docentes;

-- ============================================
-- IMPORTANTE: Reemplaza los hash por los que
-- generes con generar_password.php
-- ============================================

-- Admin (password: admin123)
INSERT INTO usuarios (username, password_hash, rol_id, activo)
VALUES ('admin', '$2y$10$REEMPLAZA_ESTE_HASH_ADMIN', 1, 1);

-- Coordinador (password: coord123)
INSERT INTO usuarios (username, password_hash, rol_id, activo)
VALUES ('coordinador', '$2y$10$REEMPLAZA_ESTE_HASH_COORD', 2, 1);

-- Docente 1 (password: docente123)
INSERT INTO usuarios (username, password_hash, rol_id, activo)
VALUES ('jperez', '$2y$10$REEMPLAZA_ESTE_HASH_DOCENTE', 3, 1);

-- Vincular docente al usuario
INSERT INTO docentes (usuario_id, dni, nombres, apellidos, email, telefono, especialidad)
VALUES (
    (SELECT id FROM usuarios WHERE username = 'jperez'),
    '12345678',
    'Juan',
    'Pérez Gómez',
    'jperez@colegio.edu.pe',
    '987654321',
    'Matemática'
);

-- Horario del docente (opcional, referencial)
INSERT INTO horarios (docente_id, curso_id, seccion_id, anio_escolar_id, dia_semana, hora_inicio, hora_fin, aula)
VALUES
    ((SELECT d.id FROM docentes d JOIN usuarios u ON u.id = d.usuario_id WHERE u.username = 'jperez'),
     (SELECT id FROM cursos WHERE nombre = 'Matemática'),
     (SELECT s.id FROM secciones s JOIN grados g ON g.id = s.grado_id WHERE g.nombre = '1ro Secundaria' AND s.nombre = 'A'),
     (SELECT id FROM anio_escolar WHERE anio = 2026),
     1, '07:30:00', '09:00:00', 'Aula 101'),
    ((SELECT d.id FROM docentes d JOIN usuarios u ON u.id = d.usuario_id WHERE u.username = 'jperez'),
     (SELECT id FROM cursos WHERE nombre = 'Matemática'),
     (SELECT s.id FROM secciones s JOIN grados g ON g.id = s.grado_id WHERE g.nombre = '1ro Secundaria' AND s.nombre = 'B'),
     (SELECT id FROM anio_escolar WHERE anio = 2026),
     1, '09:15:00', '10:45:00', 'Aula 102');