<?php
namespace App\Models;

use App\Core\Database;

class Justificacion
{
    /**
     * Lista justificaciones con filtros y paginación.
     */
    public static function listar(
        int $limit = 20,
        int $offset = 0,
        string $estado = '',
        int $docenteId = 0,
        string $desde = '',
        string $hasta = ''
    ): array {
        $db = Database::getConnection();

        $where = ['1=1'];
        $params = [];

        if ($estado !== '') {
            $where[] = "j.estado = ?";
            $params[] = $estado;
        }
        if ($docenteId > 0) {
            $where[] = "a.docente_id = ?";
            $params[] = $docenteId;
        }
        if ($desde !== '') {
            $where[] = "DATE(j.fecha_solicitud) >= ?";
            $params[] = $desde;
        }
        if ($hasta !== '') {
            $where[] = "DATE(j.fecha_solicitud) <= ?";
            $params[] = $hasta;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $stmt = $db->prepare("
            SELECT j.id, j.estado, j.motivo, j.archivo_url, j.fecha_solicitud, j.fecha_respuesta,
                   a.id AS asistencia_id, a.fecha AS asistencia_fecha,
                   a.hora_entrada, a.hora_salida,
                   d.id AS docente_id, d.nombres, d.apellidos, d.dni,
                   u.username AS aprobador_username
            FROM justificaciones j
            JOIN asistencias a ON a.id = j.asistencia_id
            JOIN docentes d ON d.id = a.docente_id
            LEFT JOIN usuarios u ON u.id = j.aprobado_por
            $whereSql
            ORDER BY
                CASE j.estado
                    WHEN 'pendiente' THEN 1
                    WHEN 'aprobado' THEN 2
                    WHEN 'rechazado' THEN 3
                    ELSE 4
                END,
                j.fecha_solicitud DESC
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Cuenta total para paginación.
     */
    public static function contar(
        string $estado = '',
        int $docenteId = 0,
        string $desde = '',
        string $hasta = ''
    ): int {
        $db = Database::getConnection();

        $where = ['1=1'];
        $params = [];

        if ($estado !== '') {
            $where[] = "j.estado = ?";
            $params[] = $estado;
        }
        if ($docenteId > 0) {
            $where[] = "a.docente_id = ?";
            $params[] = $docenteId;
        }
        if ($desde !== '') {
            $where[] = "DATE(j.fecha_solicitud) >= ?";
            $params[] = $desde;
        }
        if ($hasta !== '') {
            $where[] = "DATE(j.fecha_solicitud) <= ?";
            $params[] = $hasta;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM justificaciones j
            JOIN asistencias a ON a.id = j.asistencia_id
            $whereSql
        ");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Resumen por estado.
     */
    public static function resumen(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT estado, COUNT(*) AS total
            FROM justificaciones
            GROUP BY estado
        ");

        $resumen = ['pendiente' => 0, 'aprobado' => 0, 'rechazado' => 0];
        foreach ($stmt->fetchAll() as $r) {
            $resumen[$r['estado']] = (int)$r['total'];
        }
        return $resumen;
    }

    /**
     * Busca una justificación por ID con todos los datos.
     */
    public static function buscarPorId(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT j.*,
                   a.fecha AS asistencia_fecha, a.hora_entrada, a.hora_salida,
                   a.minutos_tardanza,
                   e.codigo AS asistencia_estado,
                   d.id AS docente_id, d.nombres, d.apellidos, d.dni, d.especialidad,
                   u.username AS aprobador_username
            FROM justificaciones j
            JOIN asistencias a ON a.id = j.asistencia_id
            JOIN estados_asistencia e ON e.id = a.estado_id
            JOIN docentes d ON d.id = a.docente_id
            LEFT JOIN usuarios u ON u.id = j.aprobado_por
            WHERE j.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Crea una justificación.
     */
    public static function crear(array $datos): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO justificaciones (asistencia_id, motivo, archivo_url, estado)
            VALUES (?, ?, ?, 'pendiente')
        ");
        $stmt->execute([
            $datos['asistencia_id'],
            $datos['motivo'],
            $datos['archivo_url'] ?? null
        ]);
        return (int)$db->lastInsertId();
    }

    /**
     * Aprueba o rechaza una justificación.
     */
    public static function responder(int $id, string $estado, int $aprobadoPor): bool
    {
        $db = Database::getConnection();

        $db->beginTransaction();
        try {
            // 1. Actualizar la justificación
            $stmt = $db->prepare("
                UPDATE justificaciones
                SET estado = ?, aprobado_por = ?, fecha_respuesta = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$estado, $aprobadoPor, $id]);

            // 2. Si se aprueba, cambiar el estado de la asistencia
            if ($estado === 'aprobado') {
                $stmt = $db->prepare("
                    UPDATE asistencias a
                    JOIN justificaciones j ON j.asistencia_id = a.id
                    JOIN estados_asistencia e ON e.codigo = 'justificado'
                    SET a.estado_id = e.id
                    WHERE j.id = ?
                ");
                $stmt->execute([$id]);
            }

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene las asistencias sin justificación de un docente
     * (para poder crear una nueva).
     */
    public static function asistenciasSinJustificar(int $docenteId = 0): array
    {
        $db = Database::getConnection();

        $where = [
            "(e.codigo IN ('ausente', 'falta_salida', 'tarde'))",
            "j.id IS NULL"
        ];
        $params = [];

        if ($docenteId > 0) {
            $where[] = "a.docente_id = ?";
            $params[] = $docenteId;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $stmt = $db->prepare("
            SELECT a.id, a.fecha, a.hora_entrada, a.hora_salida,
                   a.minutos_tardanza,
                   e.codigo AS estado,
                   d.id AS docente_id, d.nombres, d.apellidos, d.dni
            FROM asistencias a
            JOIN estados_asistencia e ON e.id = a.estado_id
            JOIN docentes d ON d.id = a.docente_id
            LEFT JOIN justificaciones j ON j.asistencia_id = a.id
            $whereSql
            ORDER BY a.fecha DESC
            LIMIT 100
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}