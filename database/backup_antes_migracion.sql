-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 15-10-2026 a las 13:09:50
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `control_asistencia_docentes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anio_escolar`
--

DROP TABLE IF EXISTS `anio_escolar`;
CREATE TABLE IF NOT EXISTS `anio_escolar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `anio` int NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `anio` (`anio`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `anio_escolar`
--

INSERT INTO `anio_escolar` (`id`, `anio`, `fecha_inicio`, `fecha_fin`, `activo`) VALUES
(1, 2026, '2026-03-01', '2026-12-20', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `api_keys`
--

DROP TABLE IF EXISTS `api_keys`;
CREATE TABLE IF NOT EXISTS `api_keys` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `api_key` varchar(64) NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_key` (`api_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `api_keys`
--

INSERT INTO `api_keys` (`id`, `nombre`, `api_key`, `activo`, `created_at`) VALUES
(1, 'Kiosko Entrada Principal', '0c74c3aa1c1e18c2e5fae3c6b7669f543019457d31f6e70f5906d8fea10aad7a', 1, '2026-10-07 20:32:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

DROP TABLE IF EXISTS `asistencias`;
CREATE TABLE IF NOT EXISTS `asistencias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `docente_id` int NOT NULL,
  `fecha` date NOT NULL,
  `hora_entrada` datetime DEFAULT NULL,
  `hora_salida` datetime DEFAULT NULL,
  `minutos_tardanza` int DEFAULT '0',
  `estado_id` int NOT NULL,
  `metodo_marcacion` varchar(20) DEFAULT 'qr',
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_docente_fecha` (`docente_id`,`fecha`),
  KEY `estado_id` (`estado_id`),
  KEY `idx_fecha` (`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `asistencias`
--

INSERT INTO `asistencias` (`id`, `docente_id`, `fecha`, `hora_entrada`, `hora_salida`, `minutos_tardanza`, `estado_id`, `metodo_marcacion`, `latitud`, `longitud`, `observacion`, `created_at`, `updated_at`) VALUES
(2, 1, '2026-10-07', '2026-10-07 23:13:56', '2026-10-07 23:14:05', 943, 2, 'qr', NULL, NULL, NULL, '2026-10-08 04:13:56', '2026-10-08 04:14:05'),
(4, 1, '2026-10-08', '2026-10-08 22:43:07', '2026-10-08 22:44:09', 913, 2, 'qr', NULL, NULL, NULL, '2026-10-09 03:43:07', '2026-10-09 03:44:09'),
(5, 1, '2026-10-09', '2026-10-09 11:23:00', '2026-10-09 11:25:39', 233, 4, 'qr', NULL, NULL, NULL, '2026-10-09 16:23:00', '2026-10-11 15:30:14'),
(6, 1, '2026-10-10', '2026-10-10 22:15:00', '2026-10-10 23:48:52', 885, 2, 'qr', NULL, NULL, NULL, '2026-10-11 03:15:00', '2026-10-11 04:48:52'),
(7, 1, '2026-10-11', '2026-10-11 01:11:08', '2026-10-11 01:11:21', 0, 1, 'qr', NULL, NULL, NULL, '2026-10-11 06:11:08', '2026-10-11 06:11:21'),
(8, 5, '2026-10-11', '2026-10-11 13:12:57', '2026-10-11 14:05:16', 342, 2, 'qr', NULL, NULL, NULL, '2026-10-11 18:12:57', '2026-10-11 19:05:16'),
(9, 5, '2026-10-12', '2026-10-12 15:14:21', '2026-10-12 17:16:43', 464, 2, 'qr', NULL, NULL, NULL, '2026-10-12 20:14:21', '2026-10-12 22:16:43'),
(10, 1, '2026-10-12', '2026-10-12 15:15:17', '2026-10-12 16:18:40', 465, 2, 'qr', NULL, NULL, NULL, '2026-10-12 20:15:17', '2026-10-12 21:18:40'),
(11, 6, '2026-10-12', '2026-10-12 17:25:06', '2026-10-12 17:26:34', 595, 2, 'qr', NULL, NULL, NULL, '2026-10-12 22:25:06', '2026-10-12 22:26:34'),
(12, 5, '2026-10-13', '2026-10-13 12:31:49', '2026-10-13 13:06:39', 301, 2, 'qr', NULL, NULL, NULL, '2026-10-13 17:31:49', '2026-10-13 18:06:39'),
(13, 6, '2026-10-13', '2026-10-13 13:01:50', '2026-10-13 13:02:52', 331, 2, 'qr', NULL, NULL, NULL, '2026-10-13 18:01:50', '2026-10-13 18:02:52'),
(14, 7, '2026-10-13', '2026-10-13 13:13:19', '2026-10-13 13:13:39', 343, 2, 'qr', NULL, NULL, NULL, '2026-10-13 18:13:19', '2026-10-13 18:13:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
CREATE TABLE IF NOT EXISTS `configuracion` (
  `clave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`clave`, `valor`, `descripcion`) VALUES
('hora_entrada_oficial', '07:30:00', 'Hora oficial de entrada'),
('hora_salida_oficial', '14:00:00', 'Hora oficial de salida'),
('nombre_colegio', 'I.E. SARA A. BULLON', 'Nombre de la institución'),
('qr_duracion_segundos', '60', 'Duración del QR dinámico antes de regenerarse'),
('tolerancia_minutos', '5', 'Minutos de tolerancia para tardanza');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

DROP TABLE IF EXISTS `cursos`;
CREATE TABLE IF NOT EXISTS `cursos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `area` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `nombre`, `area`) VALUES
(1, 'Matemática', 'Ciencias'),
(2, 'Comunicación', 'Letras'),
(3, 'Historia', 'Letras'),
(4, 'Ciencia y Tecnología', 'Ciencias');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

DROP TABLE IF EXISTS `docentes`;
CREATE TABLE IF NOT EXISTS `docentes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `dni` varchar(15) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni` (`dni`),
  UNIQUE KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `docentes`
--

INSERT INTO `docentes` (`id`, `usuario_id`, `dni`, `nombres`, `apellidos`, `email`, `telefono`, `especialidad`, `foto_url`, `activo`, `created_at`) VALUES
(1, 4, '12345678', 'Juan Carlos', 'Pérez Gómez', 'jperez@colegio.edu.pe', '987654321', 'Matemática', NULL, 1, '2026-10-07 20:08:15'),
(2, 5, '87654321', 'María Teresa', 'López Torres', 'mlopez@colegio.edu.pe', '987111222', 'Lenguaje y Comunicación', NULL, 1, '2026-10-09 17:52:52'),
(3, 6, '11223344', 'Carlos', 'Ramírez Torres', 'cramirez@colegio.edu.pe', '955111222', 'Historia del Perú', NULL, 1, '2026-10-09 18:08:04'),
(4, 7, '54545445', 'Fernando', 'Santamaria Bances', 'fernando@gmail.com', '121212121', 'Lenguaje', NULL, 1, '2026-10-09 19:19:28'),
(5, 8, '16631277', 'Wilmer', 'Garcia Ramirez', 'garrawil@gmail.com', '906962893', 'Analisis Orientado a Objetos', NULL, 1, '2026-10-11 18:12:09'),
(6, 9, '14141414', 'Sixta', 'Cobenas Garcia', 'sixta@gmail.com', '1414141', 'Logico Matematico', NULL, 1, '2026-10-12 22:23:41'),
(7, 10, '45142414', 'Ariana Kassandra', 'Regalado', 'ariana@gmail.com', '', 'Literatura', NULL, 1, '2026-10-13 18:12:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_asistencia`
--

DROP TABLE IF EXISTS `estados_asistencia`;
CREATE TABLE IF NOT EXISTS `estados_asistencia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `estados_asistencia`
--

INSERT INTO `estados_asistencia` (`id`, `codigo`, `descripcion`) VALUES
(1, 'presente', 'Llegó dentro de la tolerancia'),
(2, 'tarde', 'Llegó después de los 5 minutos de tolerancia'),
(3, 'ausente', 'No marcó entrada'),
(4, 'justificado', 'Ausencia con permiso aprobado'),
(5, 'falta_salida', 'Marcó entrada pero no salida');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados`
--

DROP TABLE IF EXISTS `grados`;
CREATE TABLE IF NOT EXISTS `grados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `nivel` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `grados`
--

INSERT INTO `grados` (`id`, `nombre`, `nivel`) VALUES
(1, '1ro Secundaria', 'secundaria'),
(2, '2do Secundaria', 'secundaria'),
(3, '3ro Secundaria', 'secundaria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

DROP TABLE IF EXISTS `horarios`;
CREATE TABLE IF NOT EXISTS `horarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `docente_id` int NOT NULL,
  `curso_id` int NOT NULL,
  `seccion_id` int NOT NULL,
  `anio_escolar_id` int NOT NULL,
  `dia_semana` tinyint NOT NULL COMMENT '1=Lunes ... 7=Domingo',
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `aula` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `curso_id` (`curso_id`),
  KEY `seccion_id` (`seccion_id`),
  KEY `anio_escolar_id` (`anio_escolar_id`),
  KEY `idx_docente_dia` (`docente_id`,`dia_semana`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id`, `docente_id`, `curso_id`, `seccion_id`, `anio_escolar_id`, `dia_semana`, `hora_inicio`, `hora_fin`, `aula`) VALUES
(1, 1, 1, 1, 1, 1, '07:30:00', '09:00:00', 'Aula 101'),
(2, 1, 1, 2, 1, 1, '09:15:00', '10:45:00', 'Aula 102');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `justificaciones`
--

DROP TABLE IF EXISTS `justificaciones`;
CREATE TABLE IF NOT EXISTS `justificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `asistencia_id` int NOT NULL,
  `motivo` text NOT NULL,
  `archivo_url` varchar(255) DEFAULT NULL,
  `aprobado_por` int DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'pendiente',
  `fecha_solicitud` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_respuesta` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asistencia_id` (`asistencia_id`),
  KEY `aprobado_por` (`aprobado_por`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `justificaciones`
--

INSERT INTO `justificaciones` (`id`, `asistencia_id`, `motivo`, `archivo_url`, `aprobado_por`, `estado`, `fecha_solicitud`, `fecha_respuesta`) VALUES
(2, 5, 'Certificado médico por enfermedad - Adjunto comprobante', '/uploads/justificaciones/just_6acba67c7d989.jpg', 1, 'aprobado', '2026-10-11 15:08:44', '2026-10-11 10:30:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs_actividad`
--

DROP TABLE IF EXISTS `logs_actividad`;
CREATE TABLE IF NOT EXISTS `logs_actividad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `accion` varchar(100) DEFAULT NULL,
  `detalle` text,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `logs_actividad`
--

INSERT INTO `logs_actividad` (`id`, `usuario_id`, `accion`, `detalle`, `ip`, `created_at`) VALUES
(1, 1, 'editar_docente', 'Editó al docente: María Teresa López Torres', '192.168.0.19', '2026-10-09 19:17:33'),
(2, 1, 'crear_docente', 'Creó al docente: Fernando Santamaria Bances', '192.168.0.19', '2026-10-09 19:19:28'),
(3, 1, 'exportar', 'Exportó reporte del  al ', '192.168.0.19', '2026-10-09 19:20:57'),
(4, 1, 'exportar', 'Exportó reporte del  al ', '192.168.0.19', '2026-10-09 19:21:24'),
(5, 1, 'login', 'Inició sesión en el panel', '192.168.0.19', '2026-10-09 19:22:03'),
(6, 1, 'exportar', 'Exportó reporte del  al ', '192.168.0.19', '2026-10-09 19:22:11'),
(7, 1, 'exportar', 'Exportó reporte del  al ', '192.168.0.19', '2026-10-09 19:22:33'),
(8, 1, 'login', 'Inició sesión en el panel', '192.168.0.19', '2026-10-09 19:26:20'),
(9, 1, 'exportar', 'Exportó reporte del  al ', '192.168.0.19', '2026-10-09 19:26:30'),
(10, 1, 'exportar', 'Exportó reporte del 2026-10-01 al 2026-10-09', '192.168.0.19', '2026-10-09 19:30:21'),
(11, 1, 'exportar', 'Exportó reporte del 2026-10-01 al 2026-10-09', '192.168.0.19', '2026-10-09 19:30:40'),
(12, 1, 'login', 'Inició sesión en el panel', '192.168.0.19', '2026-10-09 19:49:51'),
(13, 1, 'login', 'Inició sesión en el panel', '192.168.0.19', '2026-10-09 19:58:52'),
(14, 1, 'cambio_password', 'Cambió su contraseña', '192.168.0.19', '2026-10-09 20:14:25'),
(15, 1, 'logout', 'Cerró sesión', '192.168.0.19', '2026-10-09 20:15:07'),
(16, 1, 'login', 'Inició sesión en el panel', '192.168.0.19', '2026-10-09 20:15:36'),
(17, 1, 'cambio_password', 'Cambió su contraseña', '192.168.0.19', '2026-10-09 20:16:06'),
(18, 1, 'login', 'Inició sesión en el panel', '192.168.0.19', '2026-10-09 20:24:15'),
(19, 1, 'cambio_password', 'Cambió su contraseña', '192.168.0.19', '2026-10-09 20:25:12'),
(20, 1, 'logout', 'Cerró sesión', '192.168.0.19', '2026-10-09 20:32:41'),
(21, 1, 'login', 'Inició sesión en el panel', '192.168.0.19', '2026-10-09 20:32:55'),
(22, 1, 'cambio_password', 'Cambió su contraseña', '192.168.0.19', '2026-10-09 20:33:30'),
(23, 1, 'cambio_password', 'Cambió su contraseña', '192.168.0.19', '2026-10-09 20:43:14'),
(24, 1, 'login', 'Inició sesión (prueba)', '192.168.0.19', '2026-10-09 20:53:19'),
(25, 1, 'login', 'Inició sesión (prueba)', '192.168.0.19', '2026-10-09 20:53:19'),
(26, 1, 'login', 'Inició sesión (prueba)', '192.168.0.19', '2026-10-09 20:53:19'),
(27, 1, 'login', 'Inició sesión (prueba)', '192.168.0.19', '2026-10-09 20:53:19'),
(28, 1, 'login', 'Inició sesión (prueba)', '192.168.0.19', '2026-10-09 20:53:19'),
(29, 1, 'login', 'Inició sesión (prueba)', '192.168.0.19', '2026-10-09 20:53:19'),
(30, 1, 'login', 'Inició sesión (prueba)', '192.168.0.19', '2026-10-09 20:53:19'),
(31, 1, 'login', 'Inició sesión (prueba)', '192.168.0.19', '2026-10-09 20:53:19'),
(32, 1, 'login', 'Inició sesión (prueba)', '192.168.0.19', '2026-10-09 20:53:19'),
(33, 1, 'login', 'Inició sesión (prueba)', '192.168.0.19', '2026-10-09 20:53:19'),
(34, 1, 'login', 'Inició sesión en el panel', '192.168.0.19', '2026-10-09 21:01:07'),
(35, 1, 'login', 'Inició sesión en el panel', '::1', '2026-10-11 14:04:57'),
(36, 1, 'login', 'Inició sesión en el panel', '192.168.0.3', '2026-10-11 14:05:17'),
(37, 1, 'login', 'Inició sesión en el panel', '::1', '2026-10-11 14:41:27'),
(38, 1, 'crear_justificacion', 'Creó justificación para asistencia #5', '::1', '2026-10-11 14:45:38'),
(39, 1, 'crear_justificacion', 'Creó justificación para asistencia #5', '::1', '2026-10-11 15:08:44'),
(40, 1, 'responder_justificacion', 'Aprobado justificación #2', '::1', '2026-10-11 15:30:14'),
(41, 1, 'login', 'Inició sesión en el panel', '192.168.0.3', '2026-10-11 15:50:39'),
(42, 1, 'logout', 'Cerró sesión', '::1', '2026-10-11 16:04:12'),
(43, 1, 'login', 'Inició sesión en el panel', '::1', '2026-10-11 16:04:31'),
(44, 1, 'login', 'Inició sesión en el panel', '192.168.0.3', '2026-10-11 16:19:08'),
(45, 1, 'login', 'Inició sesión en el panel', '::1', '2026-10-11 17:03:10'),
(46, 1, 'login', 'Inició sesión en el panel', '192.168.0.3', '2026-10-11 17:04:00'),
(47, 1, 'login', 'Inició sesión en el panel', '::1', '2026-10-11 17:10:08'),
(48, 1, 'login', 'Inició sesión en el panel', '192.168.0.3', '2026-10-11 17:12:00'),
(49, 1, 'crear_docente', 'Creó al docente: Wilmer Garcia Ramirez', '::1', '2026-10-11 18:12:09'),
(50, 1, 'logout', 'Cerró sesión', '::1', '2026-10-11 18:57:31'),
(51, 1, 'login', 'Inició sesión en el panel', '192.168.0.19', '2026-10-12 22:18:21'),
(52, 1, 'crear_docente', 'Creó al docente: Sixta Cobenas Garcia', '192.168.0.19', '2026-10-12 22:23:41'),
(53, 1, 'logout', 'Cerró sesión', '192.168.0.19', '2026-10-12 22:27:49'),
(54, 1, 'login', 'Inició sesión en el panel', '::1', '2026-10-13 18:07:52'),
(55, 1, 'exportar', 'Exportó reporte del 2026-10-13 al 2026-10-13', '::1', '2026-10-13 18:09:07'),
(56, 1, 'exportar', 'Exportó reporte del 2026-10-13 al 2026-10-13', '::1', '2026-10-13 18:09:37'),
(57, 1, 'exportar', 'Exportó reporte del 2026-10-09 al 2026-10-13', '::1', '2026-10-13 18:10:01'),
(58, 1, 'crear_docente', 'Creó al docente: Ariana Kassandra Regalado', '::1', '2026-10-13 18:12:10'),
(59, 1, 'logout', 'Cerró sesión', '::1', '2026-10-13 18:31:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `qr_tokens`
--

DROP TABLE IF EXISTS `qr_tokens`;
CREATE TABLE IF NOT EXISTS `qr_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `generado_en` datetime NOT NULL,
  `expira_en` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT '0',
  `docente_id` int DEFAULT NULL COMMENT 'Se llena cuando alguien lo usa',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_expira` (`expira_en`),
  KEY `docente_id` (`docente_id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `qr_tokens`
--

INSERT INTO `qr_tokens` (`id`, `token`, `generado_en`, `expira_en`, `usado`, `docente_id`, `created_at`) VALUES
(5, '1aa15dbc17306f57c537864f1caf7e8e765e7fca7aa777a5afaf1da20ffbbe9a', '2026-10-07 23:13:53', '2026-10-07 23:14:53', 0, NULL, '2026-10-08 04:13:53'),
(6, 'a000d8a7ce040a0d724e4510bef1a931d63ad35ad65a28f20b8a07c2525c8c9d', '2026-10-08 20:43:13', '2026-10-08 20:44:13', 0, NULL, '2026-10-09 01:43:13'),
(7, '6134ed4052a2e4cd29004e46cdaf4f3ec6e67d3b39b704e5d69ef8ddd9b5f71d', '2026-10-08 20:44:13', '2026-10-08 20:45:13', 0, NULL, '2026-10-09 01:44:13'),
(8, '15a31b59c9ca705126777003ee2c841e222850716cc6747c2203987671992156', '2026-10-08 20:45:13', '2026-10-08 20:46:13', 0, NULL, '2026-10-09 01:45:13'),
(9, '3bdf264519c99d2fce41b981c85c91195339cdedd2b5e4c35f84c49c5e2c0e34', '2026-10-08 20:46:14', '2026-10-08 20:47:14', 0, NULL, '2026-10-09 01:46:14'),
(10, '7b54a8c517b2c9f35d0299e442d5b23fdf5cc0598bfb2897fe731d5a5a804f1c', '2026-10-08 20:47:14', '2026-10-08 20:48:14', 0, NULL, '2026-10-09 01:47:14'),
(11, '3b26f872d9fdacab0dab6459cf6201102b5b90cdc6e5506052910fbc1cb20562', '2026-10-08 20:48:15', '2026-10-08 20:49:15', 0, NULL, '2026-10-09 01:48:15'),
(12, '8e03ad59c63780eff6fa9f1ca8f3305abc1bfdd39699fe4387dea875a32a54a6', '2026-10-08 20:49:41', '2026-10-08 20:50:41', 0, NULL, '2026-10-09 01:49:41'),
(13, 'b4929dbedb5d54a9b09ac26d0450c6f69c05723fe9be42fbd5a7dc107815a9be', '2026-10-08 20:50:42', '2026-10-08 20:51:42', 0, NULL, '2026-10-09 01:50:42'),
(14, 'ab3e436c6dad798715b66dbf1ad5a3868a156351f8e70ff008cacb3ba1d465f7', '2026-10-08 20:51:42', '2026-10-08 20:52:42', 0, NULL, '2026-10-09 01:51:42'),
(15, '0cccffe1d9f8c04d70b9f3b4ab4e7e7b9c92fb001a594aa1c0c921b6713b7fdd', '2026-10-08 20:52:42', '2026-10-08 20:53:42', 0, NULL, '2026-10-09 01:52:42'),
(16, '812d222ec03494ce2bc0705fc896afe559dcba15ca55abe494f468825ce30912', '2026-10-08 20:53:42', '2026-10-08 20:54:42', 0, NULL, '2026-10-09 01:53:42'),
(17, 'b02125def0feb021b678f279efbe774f27f9a1f50b5d6ee27bdb0a226db1ffd6', '2026-10-08 20:54:43', '2026-10-08 20:55:43', 0, NULL, '2026-10-09 01:54:43'),
(18, 'ebd78ab080a84faa4ccdebb8f1942b832ce6a55b1653a871a72ff22c4162a7c1', '2026-10-08 20:55:43', '2026-10-08 20:56:43', 0, NULL, '2026-10-09 01:55:43'),
(19, '2555678a4e8e35614af6560f13894ee55b339b7ddc1d560a0cbbdee3e5d29dad', '2026-10-08 20:57:00', '2026-10-08 20:58:00', 0, NULL, '2026-10-09 01:57:00'),
(20, '48914077c33694930f791ed1cfbf7297c76c75d8d489546ac8161b38f6006fb9', '2026-10-08 21:19:39', '2026-10-08 21:20:39', 0, NULL, '2026-10-09 02:19:39'),
(21, 'bf57240baa02bc63c557bc4c377529de0e0b238860b451d31b39843f5b2c39e1', '2026-10-08 21:20:40', '2026-10-08 21:21:40', 0, NULL, '2026-10-09 02:20:40'),
(22, '7c0eb8559951c8923a5acab65374b5d9fba230b0f665cf3fa2cc69d1a3e94a03', '2026-10-08 21:26:20', '2026-10-08 21:27:20', 0, NULL, '2026-10-09 02:26:20'),
(23, 'c13d474f4e8322263690b3dc02d864ab82b748ee1ea84446f5c3e9086452cc94', '2026-10-08 21:27:20', '2026-10-08 21:28:20', 0, NULL, '2026-10-09 02:27:20'),
(24, '75db2203da28daebd611fc3f312eba9c88bc3054b0f5de534371b423a60b10ac', '2026-10-08 21:28:21', '2026-10-08 21:29:21', 0, NULL, '2026-10-09 02:28:21'),
(25, '557a85305b76c4bdabddc2788e36c712f1b10ae35b977b368bd351c79fa0dae2', '2026-10-08 21:29:21', '2026-10-08 21:30:21', 0, NULL, '2026-10-09 02:29:21'),
(26, 'ffebd59105c9b19ec14522adb11d2c10e00b5a9b16792afd28042228434ebf5d', '2026-10-08 21:39:38', '2026-10-08 21:40:38', 0, NULL, '2026-10-09 02:39:38'),
(27, '9435653f6bc299a74ae3e2f58e7f48d44edbd46dce8d9dbf38bafc069f7dc526', '2026-10-08 21:42:43', '2026-10-08 21:43:43', 0, NULL, '2026-10-09 02:42:43'),
(28, '39b79853bc98e29b6a400fa078f003a8d810f3e17206ce9b461e0fb1d6bc68d1', '2026-10-08 21:55:34', '2026-10-08 21:56:34', 0, NULL, '2026-10-09 02:55:34'),
(29, '9aaf212c366fd0960956e35cb6124cd61142595bf8c00bc5659218a8dab1eb05', '2026-10-08 22:03:33', '2026-10-08 22:04:33', 0, NULL, '2026-10-09 03:03:33'),
(30, '8830ce85bef01d38a794a7a1c60b66a5685817f9b42d14bf1d6815ccada803ff', '2026-10-08 22:04:34', '2026-10-08 22:05:34', 0, NULL, '2026-10-09 03:04:34'),
(31, '0a19efa762b38eb790cf52211de334c76cdc112cc8839abccf8d956e9c9f8c7b', '2026-10-08 22:05:48', '2026-10-08 22:06:48', 0, NULL, '2026-10-09 03:05:48'),
(32, 'b32ccf9867c329678d1587f9f617e8d2e78ffd96c4936b0ef64ba2bdfcc3e9d1', '2026-10-08 22:07:56', '2026-10-08 22:08:56', 0, NULL, '2026-10-09 03:07:56'),
(33, '539489a2a0a543c89087825f658d82a61abdddae003133c12de544cfbe4d059a', '2026-10-08 22:09:56', '2026-10-08 22:10:56', 0, NULL, '2026-10-09 03:09:56'),
(34, '8b3c2a0e8371658882be32099bb4c4322c865c17c26fb610a45efb7ae1e04b75', '2026-10-08 22:11:04', '2026-10-08 22:12:04', 0, NULL, '2026-10-09 03:11:04'),
(35, 'c2c4f2adfd54fa2a98883254a87890dc5cb192325a295e729756318f4cdd5434', '2026-10-08 22:14:52', '2026-10-08 22:15:52', 0, NULL, '2026-10-09 03:14:52'),
(36, '8ff870d3d7b98c1c3d7c7f2fd846f87c0253e7c6ce5143b23ca9ca9138de00fd', '2026-10-08 22:17:05', '2026-10-08 22:18:05', 0, NULL, '2026-10-09 03:17:05'),
(37, 'a38a9c4ce879ea2f90a1c095b431e5f6e4ec2eb21e7356bb4a8c6b0ece1a889f', '2026-10-08 22:19:14', '2026-10-08 22:20:14', 0, NULL, '2026-10-09 03:19:14'),
(38, 'dceb88a92214b4664d39bbbb7f6361cad6117d073b45be11f0ff5144ecf26749', '2026-10-08 22:20:15', '2026-10-08 22:21:15', 0, NULL, '2026-10-09 03:20:15'),
(39, 'cc707eec733a92c2ec674cb53eb20374ba1db2c89124e4b3c69516ee5176cb1f', '2026-10-08 22:23:02', '2026-10-08 22:24:02', 0, NULL, '2026-10-09 03:23:02'),
(40, 'd4809903af124d3d296a4cdb8324ae7f7965c85bc2d55d5abd8bfeb1aa129766', '2026-10-08 22:24:02', '2026-10-08 22:25:02', 0, NULL, '2026-10-09 03:24:02'),
(41, 'e790b41c14c886dda0befa43ce85f59d1d7c7dbbdda30457ac4845ef9e8ea357', '2026-10-08 22:41:34', '2026-10-08 22:42:34', 0, NULL, '2026-10-09 03:41:34'),
(42, 'd6eed229d7505056d58492a435cfb3f32362bb273f8eb7d85f43c547abd8d53f', '2026-10-08 22:42:35', '2026-10-08 22:43:35', 0, NULL, '2026-10-09 03:42:35'),
(43, '5be6dfae21beaf6b06c87d7a7cad0d9eb95cda4ec089a47edc459b02e29b6f03', '2026-10-08 22:43:59', '2026-10-08 22:44:59', 0, NULL, '2026-10-09 03:43:59'),
(44, '4c31414d8fdcf3b317ef6a39bbb09a94f60f604f4b7c4f4fe0cdfaf1c3fa6751', '2026-10-08 22:44:59', '2026-10-08 22:45:59', 0, NULL, '2026-10-09 03:44:59'),
(45, '0d57725381a43b75da660c7018d918100e7de1da2bc43c923610b77d9d588df6', '2026-10-08 22:45:59', '2026-10-08 22:46:59', 0, NULL, '2026-10-09 03:45:59'),
(46, 'ba735f1d893eb077b1c71c7593dfef03dc0a0a61164f428fa2cac46411eab946', '2026-10-08 22:47:28', '2026-10-08 22:48:28', 0, NULL, '2026-10-09 03:47:28'),
(47, 'c3154e380f2fca074d7fdf7ada2e5dae88c03522425b1c051e5acd7dd19263e6', '2026-10-08 22:48:29', '2026-10-08 22:49:29', 0, NULL, '2026-10-09 03:48:29'),
(48, '8e3d5dc27f75f7dcce5838d9892ea65885f84f18590b51c3af5ad4a068767940', '2026-10-08 22:52:23', '2026-10-08 22:53:23', 0, NULL, '2026-10-09 03:52:23'),
(49, 'd109d676f274b547f8b7d743f6eef8a17166186dd5f3f5d734880dd2b5ce7890', '2026-10-08 22:53:23', '2026-10-08 22:54:23', 0, NULL, '2026-10-09 03:53:23'),
(50, 'f8f35fa73205613b6bef8003ae65959505aa2d8e5ceb9d94adc46cff41a7ca41', '2026-10-08 22:54:24', '2026-10-08 22:55:24', 0, NULL, '2026-10-09 03:54:24'),
(51, '48f13a0fbbe3eff1050929e0454e5eb31c4aed8615cc2d3480415f09e7b44220', '2026-10-09 10:17:04', '2026-10-09 10:18:04', 0, NULL, '2026-10-09 15:17:04'),
(52, 'e6739546506bfc9f5cd5362ed2d1d62d11801f664cd67e633ae78794ede59527', '2026-10-09 11:16:58', '2026-10-09 11:17:58', 0, NULL, '2026-10-09 16:16:58'),
(53, '499ad8d0f20ac98cb661fb790caba08d1886fcc3787ea90883ed4cc43b85101f', '2026-10-09 11:20:11', '2026-10-09 11:21:11', 0, NULL, '2026-10-09 16:20:11'),
(54, '89e3a2a70d624037cbd8f2204e06e23283d3c58ffc72a14c8245e0f5e3c77118', '2026-10-09 11:22:51', '2026-10-09 11:23:51', 0, NULL, '2026-10-09 16:22:51'),
(55, 'b8bf1053bc923a0f52675ea10a6120469b390e340d8028dce2fd0af3a5c6e50f', '2026-10-09 11:23:51', '2026-10-09 11:24:51', 0, NULL, '2026-10-09 16:23:51'),
(56, 'a2ad34b559ca9be2753bc60e83ccd065224f8561942a48d167ec3cff590b103a', '2026-10-09 11:25:36', '2026-10-09 11:26:36', 0, NULL, '2026-10-09 16:25:36'),
(57, '2ccb7ad101390cbf1eb1272f249b34ee0e697132aa9a20a96603c80c6735a701', '2026-10-09 11:26:37', '2026-10-09 11:27:37', 0, NULL, '2026-10-09 16:26:37'),
(58, '1b74b0501111ebce999882892d052d22379872a9a003c864c79f879a911c1e5e', '2026-10-09 17:44:33', '2026-10-09 17:45:33', 0, NULL, '2026-10-09 22:44:33'),
(59, '66ffa312d80757c961615933f3eca46bf28a78aecdbf7b8b1ce2530fdf592ce6', '2026-10-09 17:45:33', '2026-10-09 17:46:33', 0, NULL, '2026-10-09 22:45:33'),
(60, 'c7e2962a2d58ff3a3b067cab2a266096c1673f8321667359a2ff35902e980593', '2026-10-09 17:46:33', '2026-10-09 17:47:33', 0, NULL, '2026-10-09 22:46:33'),
(61, '00dc0c1cee33553db0586d4d480f43d91f386a8a3d11aa7b988f0881cac14fc8', '2026-10-10 22:14:27', '2026-10-10 22:15:27', 0, NULL, '2026-10-11 03:14:27'),
(62, 'c1fc7e814ed9480159180dcf13848fc97955240cf363b425e6d217736ab636d5', '2026-10-10 22:15:38', '2026-10-10 22:16:38', 0, NULL, '2026-10-11 03:15:38'),
(63, '7070e6e2c51f37b768e8bd94343c56de877b5583f13f93de5a4c524f4a01cc78', '2026-10-10 22:49:25', '2026-10-10 22:50:25', 0, NULL, '2026-10-11 03:49:25'),
(64, 'b0c9737be16f00a3f7acf687d65a256f407fe154087f11a638a4733fd8134d05', '2026-10-10 23:36:54', '2026-10-10 23:37:54', 0, NULL, '2026-10-11 04:36:54'),
(65, '8e777ae8d3991d4a9dc2c2c1b759886b5d0ad67dc59e85e4d099d7280b2a1c4e', '2026-10-10 23:48:49', '2026-10-10 23:49:49', 0, NULL, '2026-10-11 04:48:49'),
(66, 'b9652060ee852f61c7cf34929b8f1c82386a8daccac18d584a5d12e39b350ae1', '2026-10-10 23:50:06', '2026-10-10 23:51:06', 0, NULL, '2026-10-11 04:50:06'),
(67, 'e769fd31dea424fb046d72151be65c1a0fb91cd47e25a619fd6a8d02a06df2f5', '2026-10-11 00:32:17', '2026-10-11 00:33:17', 0, NULL, '2026-10-11 05:32:17'),
(68, 'a5e4284a67dbd207f9749f51f7efc08364379a6596e21da71502a0da98d5d1c5', '2026-10-11 00:50:01', '2026-10-11 00:51:01', 0, NULL, '2026-10-11 05:50:01'),
(69, '55d3bec6775d1293aeea76710e6aea59319f64b3657e8c1f82d004aae2c93b70', '2026-10-11 01:10:43', '2026-10-11 01:11:43', 0, NULL, '2026-10-11 06:10:43'),
(70, 'a155c660bcd458eb9becd896d70438967fbdf51c6854f26512172d0269d597dc', '2026-10-11 01:11:43', '2026-10-11 01:12:43', 0, NULL, '2026-10-11 06:11:43'),
(71, '378d3013a400bb7b91d2b0a55f31a09e91d3e612c9b64fd8cfecd9e52bcf8c0c', '2026-10-11 01:12:43', '2026-10-11 01:13:43', 0, NULL, '2026-10-11 06:12:43'),
(72, '16a5eae05d2fcc520d4d4a64b86eeb6018f82af76e5f8e9c80994b8f1c211c96', '2026-10-11 01:13:43', '2026-10-11 01:14:43', 0, NULL, '2026-10-11 06:13:43'),
(73, '5534670f065ff45adefc552f9be1daa30e7bfa491f5bc7cf3772f9bd1c81ac53', '2026-10-11 13:00:23', '2026-10-11 13:01:23', 0, NULL, '2026-10-11 18:00:23'),
(74, '1ca428d9aeecd936071b6f60137b0301b0a552834c3301c70ff2af2055127c48', '2026-10-11 13:01:23', '2026-10-11 13:02:23', 0, NULL, '2026-10-11 18:01:23'),
(75, '9f23d8d8decfd873ed46aaa91d5bf901c95c405c68dbddb885b347d1369f465a', '2026-10-11 13:04:41', '2026-10-11 13:05:41', 0, NULL, '2026-10-11 18:04:41'),
(76, '9ec986e10e31fc483eced3ede553291b55777f286196c09a35ca6a3e58ebff56', '2026-10-11 13:05:41', '2026-10-11 13:06:41', 0, NULL, '2026-10-11 18:05:41'),
(77, '415fdc85e74368633d75065557220afbc02849aa94e1f3c36085d9c1236b2a30', '2026-10-11 13:06:41', '2026-10-11 13:07:41', 0, NULL, '2026-10-11 18:06:41'),
(78, 'ba9004669a2d0ff86d62eb543fdc4436137d7875353c6572d0c6abf62835bc67', '2026-10-11 13:07:43', '2026-10-11 13:08:43', 0, NULL, '2026-10-11 18:07:43'),
(79, 'c7f218ad433138487542fb8e2a434059db9c8b21c4e60b591c58d7e82538da4c', '2026-10-11 13:08:43', '2026-10-11 13:09:43', 0, NULL, '2026-10-11 18:08:43'),
(80, '9ace95a177248670c3a51846acb53c4a0fde0094a33aab3b7bbc028c799dd3f7', '2026-10-11 13:12:23', '2026-10-11 13:13:23', 0, NULL, '2026-10-11 18:12:23'),
(81, '3a3b002e09207a82c6ed0e55840429a3b28e0db3d2096e2c7fe618ac1d76aa5b', '2026-10-11 14:03:02', '2026-10-11 14:04:02', 0, NULL, '2026-10-11 19:03:02'),
(82, '3a2a616c37a459b106b70357788ef3eb523c45f011ee44e45ce836b9175e0d05', '2026-10-11 14:04:03', '2026-10-11 14:05:03', 0, NULL, '2026-10-11 19:04:03'),
(83, 'c4045a9f5b639e34573686be72a12b8f237aa6518ce8a6bd0d66fe6257c6c444', '2026-10-11 14:05:07', '2026-10-11 14:06:07', 0, NULL, '2026-10-11 19:05:07'),
(84, '58ceb9d3225b17e0d798b41399c1cb892739004e0c5a970be2849055a910dc72', '2026-10-11 14:06:07', '2026-10-11 14:07:07', 0, NULL, '2026-10-11 19:06:07'),
(85, 'a9c80a294de06a4af9d9b344b191a936d10410a554458a125ae38b42b3d4910d', '2026-10-11 14:08:28', '2026-10-11 14:09:28', 0, NULL, '2026-10-11 19:08:28'),
(86, '41bf13552b129be55e2b2213f07d9f9d6eba57eabaff70a51c3dc203386ccb8c', '2026-10-11 14:09:28', '2026-10-11 14:10:28', 0, NULL, '2026-10-11 19:09:28'),
(87, 'c957f9f80d8b7c31a68f01e32e58b05f7b79d662cac06d801e0558719e8199df', '2026-10-12 15:14:00', '2026-10-12 15:15:00', 0, NULL, '2026-10-12 20:14:00'),
(88, '923fdcd6caf1e3c76394d7f428d57f92643b319d077e546f2d167710777e10ea', '2026-10-12 15:15:00', '2026-10-12 15:16:00', 0, NULL, '2026-10-12 20:15:00'),
(89, '2432ad7563626e0236f8f6688895a0dfc7363a92b51884cf87fd835a02023e2b', '2026-10-12 15:16:01', '2026-10-12 15:17:01', 0, NULL, '2026-10-12 20:16:01'),
(90, '8b55e462c6ddef91206feb338248ab00343b99b549e9cc4b7bb20cd99b7406cc', '2026-10-12 15:30:05', '2026-10-12 15:31:05', 0, NULL, '2026-10-12 20:30:05'),
(91, 'a79fb458349f943ea2e29c488e61123fa9634e7f52857286e4ed7adc9b4be3e9', '2026-10-12 16:01:07', '2026-10-12 16:02:07', 0, NULL, '2026-10-12 21:01:07'),
(92, '410633d57208ccf21df8341cdee162f9623e22dca50f6a7d23a2f0cdde4936dc', '2026-10-12 16:02:08', '2026-10-12 16:03:08', 0, NULL, '2026-10-12 21:02:08'),
(93, 'aaafa8e08497c4d08def88aa5c00a99908e866c59c4a95b00a7fb95abd66c458', '2026-10-12 16:11:05', '2026-10-12 16:12:05', 0, NULL, '2026-10-12 21:11:05'),
(94, '62c0709dbf009973f4456161701fbc696b433e2992912face83c8a95cb27c154', '2026-10-12 16:18:31', '2026-10-12 16:19:31', 0, NULL, '2026-10-12 21:18:31'),
(95, '194a04d14efda9077e8e98c515d35cdb4fe0779f841beb44d3864735e0520106', '2026-10-12 16:19:31', '2026-10-12 16:20:31', 0, NULL, '2026-10-12 21:19:31'),
(96, 'e10153133bb9198375adc92af79e1c5000d15b726799afb26991c12d475d1054', '2026-10-12 16:21:16', '2026-10-12 16:22:16', 0, NULL, '2026-10-12 21:21:16'),
(97, 'bdc6c8afbb18ff18658ff7851bc7fe44185855b16ee531df7eda65e7a53b4d4e', '2026-10-12 16:22:17', '2026-10-12 16:23:17', 0, NULL, '2026-10-12 21:22:17'),
(98, '3f142ba7fe2fda3a38f270bbab80bcfcf564394b873944bb854defbbe388a4ba', '2026-10-12 16:23:20', '2026-10-12 16:24:20', 0, NULL, '2026-10-12 21:23:20'),
(99, '75b753557c532890d2202c6ac1245f3e63ff2a4a7ca3e7c84ce832726bf900c5', '2026-10-12 16:30:27', '2026-10-12 16:31:27', 0, NULL, '2026-10-12 21:30:27'),
(100, '8b351fa031fb31e64596255af2f74a638d829f6259799453c21890cbdf3e99c0', '2026-10-12 16:31:42', '2026-10-12 16:32:42', 0, NULL, '2026-10-12 21:31:42'),
(101, '7b3059bfffce122c2a23d57ccd213510009f70e11e07d5c0b31f79c5040559e2', '2026-10-12 16:36:23', '2026-10-12 16:37:23', 0, NULL, '2026-10-12 21:36:23'),
(102, '24de3fd02cfc0635b7279c01e732254d920969689d52dce1285d60d972f59294', '2026-10-12 17:14:53', '2026-10-12 17:15:53', 0, NULL, '2026-10-12 22:14:53'),
(103, '0eebbcaae23c8d97583bca57c6b5a2fbef98cb05950e0a17a1148c35f8fa42c8', '2026-10-12 17:15:53', '2026-10-12 17:16:53', 0, NULL, '2026-10-12 22:15:53'),
(104, 'ac9b85451ea96d7d5e9c51870a6bb4c77dcb740ea384edddbf932e5a1a3e5880', '2026-10-12 17:16:53', '2026-10-12 17:17:53', 0, NULL, '2026-10-12 22:16:53'),
(105, '91f0ae8d22fbc6e2b673eea4ec1d69cdbbf346eb8426ff457ce35ba74774c029', '2026-10-12 17:20:46', '2026-10-12 17:21:46', 0, NULL, '2026-10-12 22:20:46'),
(106, '178619a8fdecb364b7b02771a221a49a41b0537f60f58728a43ce9be40c936ca', '2026-10-12 17:21:46', '2026-10-12 17:22:46', 0, NULL, '2026-10-12 22:21:46'),
(107, '1a337bfb47f119145efa16fc2fc00b42951fe212d5bab6f1fa0f448f1d408045', '2026-10-12 17:24:02', '2026-10-12 17:25:02', 0, NULL, '2026-10-12 22:24:02'),
(108, '134105a5ecb186bed5a9ba830fc266aa3e88a5618721fe05dac3e16f21955f2b', '2026-10-12 17:25:02', '2026-10-12 17:26:02', 0, NULL, '2026-10-12 22:25:02'),
(109, 'e3415c15cf093feb7f62a873200c454db296a631f20de072284576cbfcc29c63', '2026-10-12 17:26:02', '2026-10-12 17:27:02', 0, NULL, '2026-10-12 22:26:02'),
(110, '58f6f469502f38559e40329f72d26776ddbfa7303c60ff6f776198a10b9cc5c0', '2026-10-13 12:31:36', '2026-10-13 12:32:36', 0, NULL, '2026-10-13 17:31:36'),
(111, 'f3cdede1c049e21340bbf9615a5eb31b0b59cd75e3d29171a295eef7dde63716', '2026-10-13 13:01:36', '2026-10-13 13:02:36', 0, NULL, '2026-10-13 18:01:36'),
(112, 'c886ee4f4cafe9c4ffa01c8cf21aa4c52686a4930bf50d5b174d4e4c434bc4d4', '2026-10-13 13:02:36', '2026-10-13 13:03:36', 0, NULL, '2026-10-13 18:02:36'),
(113, '8668dffc7b46a2122853be68f9881f59594a5a9e484967c342c119db2fcc4d93', '2026-10-13 13:03:36', '2026-10-13 13:04:36', 0, NULL, '2026-10-13 18:03:36'),
(114, '355e23fa575f643c57a15e8489e2a7f3e16eb52e479ccc4b5d4d229f820fa30f', '2026-10-13 13:04:36', '2026-10-13 13:05:36', 0, NULL, '2026-10-13 18:04:36'),
(115, '380edb85c409263f9b4107058734474a402125f73d2e2a0aebcc9047b4ac4bab', '2026-10-13 13:05:36', '2026-10-13 13:06:36', 0, NULL, '2026-10-13 18:05:36'),
(116, 'e839b3dda31591fced97e62581915ad23a06e8b992da264e433f4e9cba51cd5f', '2026-10-13 13:06:36', '2026-10-13 13:07:36', 0, NULL, '2026-10-13 18:06:36'),
(117, '100ccf1185d156eaf575efa8a848937119c89257393db1afd83f33cf850dda77', '2026-10-13 13:12:32', '2026-10-13 13:13:32', 0, NULL, '2026-10-13 18:12:32'),
(118, '963398ada0a8bfc5c57b296de6f5b460a51605e3ac7406809a0a00fe13620d95', '2026-10-13 13:13:32', '2026-10-13 13:14:32', 0, NULL, '2026-10-13 18:13:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'admin', 'Administrador total del sistema'),
(2, 'coordinador', 'Aprueba justificaciones y ve reportes'),
(3, 'docente', 'Solo marca su asistencia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secciones`
--

DROP TABLE IF EXISTS `secciones`;
CREATE TABLE IF NOT EXISTS `secciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `grado_id` int NOT NULL,
  `nombre` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `grado_id` (`grado_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `secciones`
--

INSERT INTO `secciones` (`id`, `grado_id`, `nombre`) VALUES
(1, 1, 'A'),
(2, 1, 'B'),
(3, 2, 'A'),
(4, 2, 'B'),
(5, 3, 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_sesion`
--

DROP TABLE IF EXISTS `tokens_sesion`;
CREATE TABLE IF NOT EXISTS `tokens_sesion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `token` varchar(500) NOT NULL,
  `dispositivo` varchar(100) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `expira_en` datetime NOT NULL,
  `revocado` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `usuario_id` (`usuario_id`),
  KEY `idx_token` (`token`(255))
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tokens_sesion`
--

INSERT INTO `tokens_sesion` (`id`, `usuario_id`, `token`, `dispositivo`, `ip`, `expira_en`, `revocado`, `created_at`) VALUES
(1, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjoxLCJ1c2VybmFtZSI6ImFkbWluIiwicm9sIjoiYWRtaW4iLCJkb2NlbnRlX2lkIjpudWxsLCJpYXQiOjE3OTE0MzA0ODQsImV4cCI6MTc5MTQ1OTI4NH0.746hu9QV059Ktn_jqJUZBI9yzIXZKJxt0qSdlHviqBg', 'Postman', '::1', '2026-10-08 06:34:44', 0, '2026-10-08 03:34:44'),
(2, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE0MzA4NDcsImV4cCI6MTc5MTQ1OTY0N30.CUP-l0LSwf5tLjInnQmjanaoFFrRuITHl1ZZdIu6_bE', 'Postman', '::1', '2026-10-08 06:40:47', 1, '2026-10-08 03:40:47'),
(3, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE0Njk1NTMsImV4cCI6MTc5MTQ5ODM1M30.kXwLr-5-KlYQuKipR5N3a_yMV7TQ-Xu6NetK6GByhkQ', 'Postman', '::1', '2026-10-08 17:25:53', 0, '2026-10-08 14:25:53'),
(4, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE0Njk2MTAsImV4cCI6MTc5MTQ5ODQxMH0.KomK5L_Fg2O03zs8GTSxE4SU2JSUpN61nJhFFVa1RmM', 'Postman', '::1', '2026-10-08 17:26:50', 0, '2026-10-08 14:26:50'),
(5, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE0OTIyNDIsImV4cCI6MTc5MTUyMTA0Mn0.bSKJUAUKKd71h-Nk418FNYwC3TBh6hZThVFOaZgbrQo', 'Android', '192.168.0.3', '2026-10-08 23:44:02', 0, '2026-10-08 20:44:02'),
(6, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE0OTI1MzEsImV4cCI6MTc5MTUyMTMzMX0.KA3t_oQS74L6I_cdp8Ds3kZZ7RsLrZ4P449lFtIhMZM', 'Android', '192.168.0.3', '2026-10-08 23:48:51', 0, '2026-10-08 20:48:51'),
(7, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MDM4OTYsImV4cCI6MTc5MTUzMjY5Nn0._mhBVn_YRdDFA9lokndAc8h2NNfcjb9eKakkrwpCSjk', 'Android', '192.168.0.3', '2026-10-09 02:58:16', 0, '2026-10-08 23:58:16'),
(8, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MDQ1MDEsImV4cCI6MTc5MTUzMzMwMX0.zH7F_aY365vB_uImd8sP-jC_AlnMInmZqG38ARZQKGk', 'Android', '192.168.0.3', '2026-10-09 03:08:21', 0, '2026-10-09 00:08:21'),
(9, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MDQ1MTAsImV4cCI6MTc5MTUzMzMxMH0.yUEotS75TAVCFAnVm61g2t-H47-s0-fjLqDTg-TdUL0', 'Android', '192.168.0.3', '2026-10-09 03:08:30', 0, '2026-10-09 00:08:30'),
(10, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MDc2MjAsImV4cCI6MTc5MTUzNjQyMH0.VJdgc_v13qTKTDc8FNkiw9RwHf04bMXDsCiNwnWSOs4', 'Android', '192.168.0.3', '2026-10-09 04:00:20', 0, '2026-10-09 01:00:20'),
(11, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjoxLCJ1c2VybmFtZSI6ImFkbWluIiwicm9sIjoiYWRtaW4iLCJkb2NlbnRlX2lkIjpudWxsLCJpYXQiOjE3OTE1MDgwMDIsImV4cCI6MTc5MTUzNjgwMn0.8509jwXCpn8tzCZs-8YWdT6rZvdTkM2QMa-01_Tffe4', 'Android', '192.168.0.3', '2026-10-09 04:06:42', 0, '2026-10-09 01:06:42'),
(12, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjoxLCJ1c2VybmFtZSI6ImFkbWluIiwicm9sIjoiYWRtaW4iLCJkb2NlbnRlX2lkIjpudWxsLCJpYXQiOjE3OTE1MDg5OTcsImV4cCI6MTc5MTUzNzc5N30.pPO54SWHw9AHqtCyaMARhRCJtb3bt9_ZaV5DS92fOnk', 'Android', '192.168.0.3', '2026-10-09 04:23:17', 0, '2026-10-09 01:23:17'),
(13, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MTIzNzQsImV4cCI6MTc5MTU0MTE3NH0.nHzrvHIDE0i7dOVtInobybhQ53gPVje6HFtJRe9nlDQ', 'Android', '192.168.0.3', '2026-10-09 05:19:34', 0, '2026-10-09 02:19:34'),
(14, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MTI3MjcsImV4cCI6MTc5MTU0MTUyN30.bc4eQSl5ws2G1fnOAN-yLdEse4dq1Gor6mVQp6o0_ME', 'Android', '192.168.0.3', '2026-10-09 05:25:27', 0, '2026-10-09 02:25:27'),
(15, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MTM2OTUsImV4cCI6MTc5MTU0MjQ5NX0.TjMTt2bCHF-HGNkRSVjAK2_bSugKjM3qDmIK4J8Li_8', 'Android', '192.168.0.3', '2026-10-09 05:41:35', 0, '2026-10-09 02:41:35'),
(16, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MTQ0ODcsImV4cCI6MTc5MTU0MzI4N30.0rSpUK7xkBuDSQ1BeTLMrVmq1fgzHLvjpmcqywiz6vs', 'Android', '192.168.0.3', '2026-10-09 05:54:47', 0, '2026-10-09 02:54:47'),
(17, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MTQ5OTQsImV4cCI6MTc5MTU0Mzc5NH0.bq8M8dtdX8_feP7lVwwQH9dtMPeHhbvSUdpuaE18Q2M', 'Android', '192.168.0.3', '2026-10-09 06:03:14', 0, '2026-10-09 03:03:14'),
(18, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MTUyMTQsImV4cCI6MTc5MTU0NDAxNH0.yWMl1qv7dN5kB0cVBd63kHsEGWJPWYXokyUXeqp8CSc', 'Android', '192.168.0.3', '2026-10-09 06:06:54', 0, '2026-10-09 03:06:54'),
(19, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MTU2NTYsImV4cCI6MTc5MTU0NDQ1Nn0.DiwdhalHwX1vQecn1n9eZfTqwq_euJnrbXzp7MI0WE8', 'Android', '192.168.0.3', '2026-10-09 06:14:16', 0, '2026-10-09 03:14:16'),
(20, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MTU5NzEsImV4cCI6MTc5MTU0NDc3MX0.wKceqJPN9GH3O22zTDfUj21oj0qsZ3_v01QUoj9f5K4', 'Android', '192.168.0.3', '2026-10-09 06:19:31', 0, '2026-10-09 03:19:31'),
(21, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MTcyODIsImV4cCI6MTc5MTU0NjA4Mn0.PxOSXgh3VzR3pshZ6Bwmuxq8wil_bWmN6N-LKNSZGlY', 'Android', '192.168.0.3', '2026-10-09 06:41:22', 0, '2026-10-09 03:41:22'),
(22, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MTczNzgsImV4cCI6MTc5MTU0NjE3OH0.hoSyFYyItvNk0lNokB_33dgAsIHQ4G6MxGZPD2jHHRI', 'Android', '192.168.0.3', '2026-10-09 06:42:58', 0, '2026-10-09 03:42:58'),
(23, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1MTc5MzIsImV4cCI6MTc5MTU0NjczMn0.E8ZbanILZx0JC71GAFD-CzWoIwCWF5CXKmbcvhSuRKI', 'Android', '192.168.0.3', '2026-10-09 06:52:12', 0, '2026-10-09 03:52:12'),
(24, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1NjI5MzksImV4cCI6MTc5MTU5MTczOX0.61LpHYEVGGSsiiiW5VfQyY0vtW_pnIqaKtalBDgz1bU', 'Android', '192.168.0.3', '2026-10-09 19:22:19', 0, '2026-10-09 16:22:19'),
(25, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE1ODU5MTYsImV4cCI6MTc5MTYxNDcxNn0.Lj__D6XRrRTpb97JQ-l2Dha8xcbnBPmIOBugyLo3jJM', 'Android', '192.168.0.3', '2026-10-10 01:45:16', 0, '2026-10-09 22:45:16'),
(26, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE2ODg0OTYsImV4cCI6MTc5MTcxNzI5Nn0.RnMkpnAruq3Vc2zBdzJR6-CZJeqwI-ps56hINWZd3Os', 'Android', '192.168.0.3', '2026-10-11 06:14:56', 0, '2026-10-11 03:14:56'),
(27, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE2OTM4ODAsImV4cCI6MTc5MTcyMjY4MH0.r6mMPGZp7UTE4qRmSixF5jDKBbsoHH_VenL9AIzneDs', 'Android', '192.168.0.3', '2026-10-11 07:44:40', 0, '2026-10-11 04:44:40'),
(28, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE2OTQxMTgsImV4cCI6MTc5MTcyMjkxOH0.WvX-DfebfuNQ69TnmNzXIEVziWlA-zMMhmDmXpaZL_c', 'Android', '192.168.0.3', '2026-10-11 07:48:38', 0, '2026-10-11 04:48:38'),
(29, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE2OTY0NDMsImV4cCI6MTc5MTcyNTI0M30.8apy_T-nYdJlYbvoURObt-yG8oa3XrgZxTaummdOH3A', 'Android', '192.168.0.3', '2026-10-11 08:27:23', 0, '2026-10-11 05:27:23'),
(30, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE2OTY4NDcsImV4cCI6MTc5MTcyNTY0N30.7ICoWHblGVtf8FarlCzStqK1PURphNKfPNv-p6wcvC0', 'Android', '192.168.0.3', '2026-10-11 08:34:07', 0, '2026-10-11 05:34:07'),
(31, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE2OTkwNjUsImV4cCI6MTc5MTcyNzg2NX0.yk5zpgWa4noYNk89cCit7ccNGMPDk_Judd_ImE8108k', 'Android', '192.168.0.3', '2026-10-11 09:11:05', 0, '2026-10-11 06:11:05'),
(32, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE3Mzk4MzEsImV4cCI6MTc5MTc2ODYzMX0.cKRQW7lV1pIzwkkV29Yk9B8QcCA6vwXNXA6WR1Do8UY', 'Postman', '::1', '2026-10-11 20:30:31', 0, '2026-10-11 17:30:31'),
(33, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE3NDEwNTcsImV4cCI6MTc5MTc2OTg1N30.2myiWJEBIMrNeFqKuf5iVyRwejseIjYx_MkDbmnFW_4', 'Android', '192.168.0.3', '2026-10-11 20:50:57', 0, '2026-10-11 17:50:57'),
(34, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE3NDEwOTMsImV4cCI6MTc5MTc2OTg5M30.t_KWOpybFIS5lKPx25VMBx34ol_LTEti86K1f01-dyY', 'Android', '192.168.0.3', '2026-10-11 20:51:33', 0, '2026-10-11 17:51:33'),
(35, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE3NDEzNTAsImV4cCI6MTc5MTc3MDE1MH0._Q5zAW4rtIiccKzFEFBI7unTonVJSZ26vui7QMNNB20', 'Android', '192.168.0.3', '2026-10-11 20:55:50', 0, '2026-10-11 17:55:51'),
(36, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE3NDE4ODksImV4cCI6MTc5MTc3MDY4OX0.-MLLWuxv1vwDCrsRIWX6z7YE9IHchJo5jJxF9OijqzU', 'Android', '192.168.0.3', '2026-10-11 21:04:49', 0, '2026-10-11 18:04:49'),
(37, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjoxLCJ1c2VybmFtZSI6ImFkbWluIiwicm9sIjoiYWRtaW4iLCJkb2NlbnRlX2lkIjpudWxsLCJpYXQiOjE3OTE3NDE5NDMsImV4cCI6MTc5MTc3MDc0M30.iWtvgClAI2bO-boIsneAEPDVF63MJKiWl137RV34ucM', 'Android', '192.168.0.3', '2026-10-11 21:05:43', 0, '2026-10-11 18:05:43'),
(38, 8, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo4LCJ1c2VybmFtZSI6IndnYXJjaWEiLCJyb2wiOiJkb2NlbnRlIiwiZG9jZW50ZV9pZCI6NSwiaWF0IjoxNzkxNzQyMzY3LCJleHAiOjE3OTE3NzExNjd9.F6pEjDd4dSh4Al0nSovgm5he8cHAw4865WBTTVsgLrc', 'Android', '192.168.0.3', '2026-10-11 21:12:47', 0, '2026-10-11 18:12:47'),
(39, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjoxLCJ1c2VybmFtZSI6ImFkbWluIiwicm9sIjoiYWRtaW4iLCJkb2NlbnRlX2lkIjpudWxsLCJpYXQiOjE3OTE3NDM0MzgsImV4cCI6MTc5MTc3MjIzOH0.E9v89kVvtXhubRAc09r5qEnKKrPaq7sPxs0oAgFUKIc', 'Android', '192.168.0.3', '2026-10-11 21:30:38', 0, '2026-10-11 18:30:38'),
(40, 1, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjoxLCJ1c2VybmFtZSI6ImFkbWluIiwicm9sIjoiYWRtaW4iLCJkb2NlbnRlX2lkIjpudWxsLCJpYXQiOjE3OTE3NDM3NTQsImV4cCI6MTc5MTc3MjU1NH0.UDndOGQmWOyq-1bQA7Y1JG4ayeIEogCcdTzR-PdwIPg', 'Android', '192.168.0.3', '2026-10-11 21:35:54', 0, '2026-10-11 18:35:54'),
(41, 8, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo4LCJ1c2VybmFtZSI6IndnYXJjaWEiLCJyb2wiOiJkb2NlbnRlIiwiZG9jZW50ZV9pZCI6NSwiaWF0IjoxNzkxNzQzODA3LCJleHAiOjE3OTE3NzI2MDd9.3pcNIJzaRUPm_jVxrSh8XI5_4uGCWYg6wVoukLkhOoA', 'Android', '192.168.0.3', '2026-10-11 21:36:47', 0, '2026-10-11 18:36:47'),
(42, 8, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo4LCJ1c2VybmFtZSI6IndnYXJjaWEiLCJyb2wiOiJkb2NlbnRlIiwiZG9jZW50ZV9pZCI6NSwiaWF0IjoxNzkxODM1OTcxLCJleHAiOjE3OTE4NjQ3NzF9.vY5Rsr-ighQwmFV1QphhI4Q99S8w2vUxySSe1atyKRI', 'Android', '192.168.0.3', '2026-10-12 23:12:51', 0, '2026-10-12 20:12:51'),
(43, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE4MzYwOTUsImV4cCI6MTc5MTg2NDg5NX0.Dw9Yr5MM3Iz1Hz7tAeRDe3i3GsPaivRd_qNbrnpsflw', 'Android', '192.168.0.3', '2026-10-12 23:14:55', 0, '2026-10-12 20:14:55'),
(44, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE4MzcxNTcsImV4cCI6MTc5MTg2NTk1N30.HW8QHWxZBgOFXHvEWtatAx9dmXdpcJwj_2cnd_w0ZLs', 'Postman', '::1', '2026-10-12 23:32:37', 0, '2026-10-12 20:32:37'),
(45, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE4Mzc0NTAsImV4cCI6MTc5MTg2NjI1MH0.6cugiOIdBuKeF3wFbaykDCya01KycUIISr6v-iRNeG8', 'Postman', '::1', '2026-10-12 23:37:30', 0, '2026-10-12 20:37:30'),
(46, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE4Mzc0ODAsImV4cCI6MTc5MTg2NjI4MH0.voflSFX3Mf4Osy004a5cGPUMH54ANHH_-Hxu5vFLuhI', 'Postman', '::1', '2026-10-12 23:38:00', 0, '2026-10-12 20:38:00'),
(47, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE4MzkzNjksImV4cCI6MTc5MTg2ODE2OX0.G7B0eyEMZ0mzevUXSopKXoCnEXYYuzyxW8hb803Usdc', 'Android', '192.168.0.19', '2026-10-13 00:09:29', 0, '2026-10-12 21:09:29'),
(48, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE4Mzk4OTAsImV4cCI6MTc5MTg2ODY5MH0.Jw31vp3Yt2XCM4EAh1u8f7pcMXEOJbJTBuYTRk9trFE', 'Android', '192.168.0.3', '2026-10-13 00:18:10', 0, '2026-10-12 21:18:10'),
(49, 4, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo0LCJ1c2VybmFtZSI6ImpwZXJleiIsInJvbCI6ImRvY2VudGUiLCJkb2NlbnRlX2lkIjoxLCJpYXQiOjE3OTE4NDAxMTAsImV4cCI6MTc5MTg2ODkxMH0.OBgdbmA_AGogkOObrO5S8H2KzdG6KjHXcLYDLVLKZbA', 'Android', '192.168.0.3', '2026-10-13 00:21:50', 0, '2026-10-12 21:21:50'),
(50, 8, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo4LCJ1c2VybmFtZSI6IndnYXJjaWEiLCJyb2wiOiJkb2NlbnRlIiwiZG9jZW50ZV9pZCI6NSwiaWF0IjoxNzkxODQzMzU5LCJleHAiOjE3OTE4NzIxNTl9.rUiOvsnUm_TTE0gYX0KzkKDIA8k3CLuA7mQR-78lz0U', 'Android', '192.168.0.3', '2026-10-13 01:15:59', 0, '2026-10-12 22:15:59'),
(51, 8, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo4LCJ1c2VybmFtZSI6IndnYXJjaWEiLCJyb2wiOiJkb2NlbnRlIiwiZG9jZW50ZV9pZCI6NSwiaWF0IjoxNzkxODQzNDM2LCJleHAiOjE3OTE4NzIyMzZ9.smtWukWLuN57OzmyZK7b5eNzk46j72iyBIW2zRuk9nE', 'Android', '192.168.0.3', '2026-10-13 01:17:16', 0, '2026-10-12 22:17:16'),
(52, 9, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo5LCJ1c2VybmFtZSI6InNjb2JlbmFzIiwicm9sIjoiZG9jZW50ZSIsImRvY2VudGVfaWQiOjYsImlhdCI6MTc5MTg0Mzg4NiwiZXhwIjoxNzkxODcyNjg2fQ.j9nfjUsG4iPw39yWzpGcQC9Koj5mcvqwVRJeedFRpl4', 'Android', '192.168.0.3', '2026-10-13 01:24:46', 0, '2026-10-12 22:24:46'),
(53, 9, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo5LCJ1c2VybmFtZSI6InNjb2JlbmFzIiwicm9sIjoiZG9jZW50ZSIsImRvY2VudGVfaWQiOjYsImlhdCI6MTc5MTg0Mzk4MSwiZXhwIjoxNzkxODcyNzgxfQ.EO6QXYiG_3G2fNlXjLW9xVKa_Uwqfyu_QBreZF3ptFw', 'Android', '192.168.0.3', '2026-10-13 01:26:21', 0, '2026-10-12 22:26:21'),
(54, 8, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo4LCJ1c2VybmFtZSI6IndnYXJjaWEiLCJyb2wiOiJkb2NlbnRlIiwiZG9jZW50ZV9pZCI6NSwiaWF0IjoxNzkxOTEyNjExLCJleHAiOjE3OTE5NDE0MTF9.dtATO7FncTe0SwoE6w0j4M18UVLnRVsTuW_TNbD89Hg', 'Android', '192.168.0.3', '2026-10-13 20:30:11', 0, '2026-10-13 17:30:11'),
(55, 9, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo5LCJ1c2VybmFtZSI6InNjb2JlbmFzIiwicm9sIjoiZG9jZW50ZSIsImRvY2VudGVfaWQiOjYsImlhdCI6MTc5MTkxNDQ4MCwiZXhwIjoxNzkxOTQzMjgwfQ.PWQpYPIFw42lpOQ9yueNXHjNFAXlep1VMbVbsGMU8X0', 'Android', '192.168.0.13', '2026-10-13 21:01:20', 0, '2026-10-13 18:01:20'),
(56, 9, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo5LCJ1c2VybmFtZSI6InNjb2JlbmFzIiwicm9sIjoiZG9jZW50ZSIsImRvY2VudGVfaWQiOjYsImlhdCI6MTc5MTkxNDY1NywiZXhwIjoxNzkxOTQzNDU3fQ.yLNFyEBLvN2FS-Hv21Qs8TN3FzeEGmSIWhy-7nTeR4c', 'Android', '192.168.0.13', '2026-10-13 21:04:17', 0, '2026-10-13 18:04:17'),
(57, 8, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo4LCJ1c2VybmFtZSI6IndnYXJjaWEiLCJyb2wiOiJkb2NlbnRlIiwiZG9jZW50ZV9pZCI6NSwiaWF0IjoxNzkxOTE0Nzg1LCJleHAiOjE3OTE5NDM1ODV9.6Qjsf7p_rrEa9visHpO6Exgv-XAKdOLIe4bvml1caSM', 'Android', '192.168.0.13', '2026-10-13 21:06:25', 0, '2026-10-13 18:06:25'),
(58, 10, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjoxMCwidXNlcm5hbWUiOiJhcmlhbmEiLCJyb2wiOiJkb2NlbnRlIiwiZG9jZW50ZV9pZCI6NywiaWF0IjoxNzkxOTE1MTg2LCJleHAiOjE3OTE5NDM5ODZ9.wx9vtQk128q7yLbh3nb1ODu2uMCoGjcoTbOpTmZk1oU', 'Android', '192.168.0.13', '2026-10-13 21:13:06', 0, '2026-10-13 18:13:06'),
(59, 9, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c3VhcmlvX2lkIjo5LCJ1c2VybmFtZSI6InNjb2JlbmFzIiwicm9sIjoiZG9jZW50ZSIsImRvY2VudGVfaWQiOjYsImlhdCI6MTc5MTkxNTI5NSwiZXhwIjoxNzkxOTQ0MDk1fQ.w-DK9C02xuiCIff927JL0CfHhkET2cxK1J9MMABnFmo', 'Android', '192.168.0.13', '2026-10-13 21:14:55', 0, '2026-10-13 18:14:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol_id` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `ultimo_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `rol_id` (`rol_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password_hash`, `rol_id`, `activo`, `ultimo_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$pYh2xNzMv03yUBWnpiNVP.1TzFA24Mp4ZYAZ8VugjMVZ7qwRez1Mi', 1, 1, '2026-10-13 13:07:52', '2026-10-07 19:13:22'),
(3, 'coordinador', '$2y$10$bXQBgUnPrQcRhZgd9goLhu3VKvfFsAV9knrxk5pUBtGJee.17SlUO', 2, 1, '2026-10-09 11:30:08', '2026-10-07 20:08:15'),
(4, 'jperez', '$2y$10$YVXDbawwQM2KX5fYov9TqO9BtjqF2tJZuEOR187rHeoX0A2fmf2M.', 3, 1, '2026-10-12 16:21:50', '2026-10-07 20:08:15'),
(5, 'mlopez', '$2y$10$C2kHSSjdTTrwh2dXDyrHwu7PbTp0LQu7BaktwA896FBQpSDvOKLi2', 3, 1, NULL, '2026-10-09 17:52:52'),
(6, 'cramirez', '$2y$10$t/7B.fGxeHXuFZfwYK/vY.5d6GEjn9IUSicvChNwkVTKTnGp38RFu', 3, 1, NULL, '2026-10-09 18:08:04'),
(7, 'fsantamaria', '$2y$10$F1s5H37tpb2D.gqL9RRazeI80G9lZiDzODCVh1aieHWsR96EFL.Mu', 3, 1, NULL, '2026-10-09 19:19:28'),
(8, 'wgarcia', '$2y$10$uvFmiVbJ/EnT3odP0i4lj.9NFPpN9vcZDW1fULsnkjj3X6b3.yf1G', 3, 1, '2026-10-13 13:06:25', '2026-10-11 18:12:09'),
(9, 'scobenas', '$2y$10$VCygz7x/wr9VpWKhFJny/uEAi8g13jNnLBWxaIxSLAxcWBTquirpi', 3, 1, '2026-10-13 13:14:55', '2026-10-12 22:23:41'),
(10, 'ariana', '$2y$10$iAn2yfBa1uAz6oS0t0Zja.F3A2nSgkjlHRPfd1uLhlE8t/tzGCDYi', 3, 1, '2026-10-13 13:13:06', '2026-10-13 18:12:10');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`docente_id`) REFERENCES `docentes` (`id`),
  ADD CONSTRAINT `asistencias_ibfk_2` FOREIGN KEY (`estado_id`) REFERENCES `estados_asistencia` (`id`);

--
-- Filtros para la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD CONSTRAINT `docentes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`docente_id`) REFERENCES `docentes` (`id`),
  ADD CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `horarios_ibfk_3` FOREIGN KEY (`seccion_id`) REFERENCES `secciones` (`id`),
  ADD CONSTRAINT `horarios_ibfk_4` FOREIGN KEY (`anio_escolar_id`) REFERENCES `anio_escolar` (`id`);

--
-- Filtros para la tabla `justificaciones`
--
ALTER TABLE `justificaciones`
  ADD CONSTRAINT `justificaciones_ibfk_1` FOREIGN KEY (`asistencia_id`) REFERENCES `asistencias` (`id`),
  ADD CONSTRAINT `justificaciones_ibfk_2` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `logs_actividad`
--
ALTER TABLE `logs_actividad`
  ADD CONSTRAINT `logs_actividad_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `qr_tokens`
--
ALTER TABLE `qr_tokens`
  ADD CONSTRAINT `qr_tokens_ibfk_1` FOREIGN KEY (`docente_id`) REFERENCES `docentes` (`id`);

--
-- Filtros para la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD CONSTRAINT `secciones_ibfk_1` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`);

--
-- Filtros para la tabla `tokens_sesion`
--
ALTER TABLE `tokens_sesion`
  ADD CONSTRAINT `tokens_sesion_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
