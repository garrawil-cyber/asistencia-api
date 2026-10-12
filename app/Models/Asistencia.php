<?php
namespace App\Models;

use App\Core\Database;

class Asistencia
{
    /**
     * Devuelve el registro de asistencia del docente para HOY.
     * La fecha se calcula con la zona horaria del servidor MySQL.
     */
    public static function buscarHoy(int $docenteId): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT a.id, a.docente_id, a.fecha, a.hora_entrada, a.hora_salida,
                   a.minutos_tardanza, a.metodo_marcacion, a.observacion,
                   e.codigo AS estado, e.descripcion AS estado_descripcion
            FROM asistencias a
            JOIN estados_asistencia e ON e.id = a.estado_id
            WHERE a.docente_id = ? AND a.fecha = CURDATE()
            LIMIT 1
        ");
        $stmt->execute([$docenteId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Registra la ENTRADA del día.
     * La fecha se deriva de $horaEntrada (con zona horaria de PHP/Lima),
     * no de CURDATE() de MySQL, para mantener consistencia.
     *
     * Lanza RuntimeException (código 409) si ya existe registro del día.
     */
    public static function registrarEntrada(
        int $docenteId,
        string $horaEntrada,
        string $estado,
        int $minutos
    ): int {
        $db = Database::getConnection();
        $estadoId = self::idEstado($estado);

        // Derivar la fecha del string de hora (formato 'YYYY-MM-DD HH:MM:SS')
        $fecha = substr($horaEntrada, 0, 10);

        try {
            $stmt = $db->prepare("
                INSERT INTO asistencias
                    (docente_id, fecha, hora_entrada, minutos_tardanza,
                     estado_id, metodo_marcacion)
                VALUES (?, ?, ?, ?, ?, 'qr')
            ");
            $stmt->execute([$docenteId, $fecha, $horaEntrada, $minutos, $estadoId]);
            return (int)$db->lastInsertId();
        } catch (\PDOException $e) {
            // Código SQLSTATE 23000 = violación de UNIQUE / FK
            if ($e->getCode() === '23000') {
                throw new \RuntimeException('Ya registraste tu entrada hoy', 409);
            }
            throw $e;
        }
    }

    /**
     * Registra la SALIDA sobre un registro de asistencia existente.
     */
    public static function registrarSalida(int $asistenciaId, string $horaSalida): void
    {
        $db = Database::getConnection();
        $db->prepare("UPDATE asistencias SET hora_salida = ? WHERE id = ?")
           ->execute([$horaSalida, $asistenciaId]);
    }

    /**
     * Historial de asistencias en un rango de fechas.
     */
    public static function historial(int $docenteId, string $desde, string $hasta): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT a.id, a.fecha, a.hora_entrada, a.hora_salida,
                   a.minutos_tardanza, a.metodo_marcacion, a.observacion,
                   e.codigo AS estado
            FROM asistencias a
            JOIN estados_asistencia e ON e.id = a.estado_id
            WHERE a.docente_id = ? AND a.fecha BETWEEN ? AND ?
            ORDER BY a.fecha DESC
        ");
        $stmt->execute([$docenteId, $desde, $hasta]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene el ID del estado a partir de su código.
     */
    private static function idEstado(string $codigo): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM estados_asistencia WHERE codigo = ?");
        $stmt->execute([$codigo]);
        $id = $stmt->fetchColumn();

        if (!$id) {
            throw new \RuntimeException("Estado de asistencia desconocido: $codigo", 500);
        }
        return (int)$id;
    }

        /**
     * Lista de asistencias con filtros y paginación (para el panel admin).
     */
    public static function listarConFiltros(
        string $desde,
        string $hasta,
        int $docenteId = 0,
        string $estado = '',
        int $limit = 20,
        int $offset = 0
    ): array {
        $db = Database::getConnection();

        $where = ["a.fecha BETWEEN ? AND ?"];
        $params = [$desde, $hasta];

        if ($docenteId > 0) {
            $where[] = "a.docente_id = ?";
            $params[] = $docenteId;
        }

        if ($estado !== '') {
            $where[] = "e.codigo = ?";
            $params[] = $estado;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $sql = "
            SELECT
                a.id,
                a.fecha,
                a.hora_entrada,
                a.hora_salida,
                a.minutos_tardanza,
                a.metodo_marcacion,
                a.observacion,
                e.codigo AS estado,
                d.id AS docente_id,
                d.dni,
                d.nombres,
                d.apellidos,
                d.especialidad
            FROM asistencias a
            JOIN docentes d ON d.id = a.docente_id
            JOIN estados_asistencia e ON e.id = a.estado_id
            $whereSql
            ORDER BY a.fecha DESC, a.hora_entrada DESC
            LIMIT $limit OFFSET $offset
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Cuenta registros para paginación (mismos filtros).
     */
    public static function contarConFiltros(
        string $desde,
        string $hasta,
        int $docenteId = 0,
        string $estado = ''
    ): int {
        $db = Database::getConnection();

        $where = ["a.fecha BETWEEN ? AND ?"];
        $params = [$desde, $hasta];

        if ($docenteId > 0) {
            $where[] = "a.docente_id = ?";
            $params[] = $docenteId;
        }

        if ($estado !== '') {
            $where[] = "e.codigo = ?";
            $params[] = $estado;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM asistencias a
            JOIN estados_asistencia e ON e.id = a.estado_id
            $whereSql
        ");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Resumen por estado en un rango (para KPIs de reportes).
     */
    public static function resumenPorEstado(
        string $desde,
        string $hasta,
        int $docenteId = 0
    ): array {
        $db = Database::getConnection();

        $where = ["a.fecha BETWEEN ? AND ?"];
        $params = [$desde, $hasta];

        if ($docenteId > 0) {
            $where[] = "a.docente_id = ?";
            $params[] = $docenteId;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $stmt = $db->prepare("
            SELECT e.codigo AS estado, COUNT(*) AS total
            FROM asistencias a
            JOIN estados_asistencia e ON e.id = a.estado_id
            $whereSql
            GROUP BY e.codigo
        ");
        $stmt->execute($params);

        $resumen = [
            'presente'     => 0,
            'tarde'        => 0,
            'ausente'      => 0,
            'justificado'  => 0,
            'falta_salida' => 0
        ];

        foreach ($stmt->fetchAll() as $r) {
            $resumen[$r['estado']] = (int)$r['total'];
        }

        return $resumen;
    }

    /**
     * Datos para exportar a CSV (sin paginación).
     */
    public static function exportarConFiltros(
        string $desde,
        string $hasta,
        int $docenteId = 0,
        string $estado = ''
    ): array {
        return self::listarConFiltros($desde, $hasta, $docenteId, $estado, 10000, 0);
    }

        /**
     * Devuelve el estado del día del docente:
     * - sin_marcar: no marcó entrada
     * - entrada_marcada: marcó entrada pero no salida
     * - completo: marcó entrada y salida
     */
    public static function estadoHoy(int $docenteId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT
                a.id,
                a.fecha,
                a.hora_entrada,
                a.hora_salida,
                a.minutos_tardanza,
                e.codigo AS estado
            FROM asistencias a
            JOIN estados_asistencia e ON e.id = a.estado_id
            WHERE a.docente_id = ? AND a.fecha = CURDATE()
            LIMIT 1
        ");
        $stmt->execute([$docenteId]);
        $asistencia = $stmt->fetch();

        if (!$asistencia) {
            return [
                'estado' => 'sin_marcar',
                'entrada' => null,
                'salida' => null,
                'minutos_tardanza' => 0,
                'estado_asistencia' => null
            ];
        }

        $estadoGeneral = $asistencia['hora_salida'] ? 'completo' : 'entrada_marcada';

        return [
            'estado' => $estadoGeneral,
            'entrada' => $asistencia['hora_entrada'],
            'salida' => $asistencia['hora_salida'],
            'minutos_tardanza' => (int)$asistencia['minutos_tardanza'],
            'estado_asistencia' => $asistencia['estado']
        ];
    }

}