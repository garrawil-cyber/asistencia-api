<?php
namespace App\Models;

use App\Core\Database;

class Turno
{
    /**
     * Lista turnos con filtros de búsqueda y estado.
     * Incluye el conteo de docentes asignados a cada turno.
     */
    public static function listar(string $busqueda = '', string $estado = ''): array
    {
        $db = Database::getConnection();

        $where = [];
        $params = [];

        if ($busqueda !== '') {
            $where[] = "(t.codigo LIKE ? OR t.nombre LIKE ?)";
            $like = '%' . $busqueda . '%';
            $params[] = $like;
            $params[] = $like;
        }

        if ($estado === 'activos')   $where[] = "t.activo = 1";
        if ($estado === 'inactivos') $where[] = "t.activo = 0";

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT t.*,
                   (SELECT COUNT(*) FROM docentes d WHERE d.turno_id = t.id AND d.activo = 1) AS total_docentes
            FROM turnos t
            $whereSql
            ORDER BY t.activo DESC, t.hora_entrada ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Busca un turno por su ID.
     */
    public static function buscarPorId(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT t.*,
                   (SELECT COUNT(*) FROM docentes d WHERE d.turno_id = t.id AND d.activo = 1) AS total_docentes
            FROM turnos t
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Crea un nuevo turno.
     */
    public static function crear(array $datos): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO turnos 
                (codigo, nombre, hora_entrada, hora_salida, tolerancia_minutos,
                 ventana_entrada_inicio, ventana_entrada_fin,
                 ventana_salida_inicio, ventana_salida_fin, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $datos['codigo'],
            $datos['nombre'],
            $datos['hora_entrada'],
            $datos['hora_salida'],
            $datos['tolerancia_minutos'],
            $datos['ventana_entrada_inicio'],
            $datos['ventana_entrada_fin'],
            $datos['ventana_salida_inicio'],
            $datos['ventana_salida_fin'],
            (int)$datos['activo']
        ]);
        return (int)$db->lastInsertId();
    }

    /**
     * Actualiza un turno existente.
     */
    public static function actualizar(int $id, array $datos): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE turnos SET
                codigo = ?, nombre = ?, hora_entrada = ?, hora_salida = ?,
                tolerancia_minutos = ?, ventana_entrada_inicio = ?,
                ventana_entrada_fin = ?, ventana_salida_inicio = ?,
                ventana_salida_fin = ?, activo = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $datos['codigo'],
            $datos['nombre'],
            $datos['hora_entrada'],
            $datos['hora_salida'],
            $datos['tolerancia_minutos'],
            $datos['ventana_entrada_inicio'],
            $datos['ventana_entrada_fin'],
            $datos['ventana_salida_inicio'],
            $datos['ventana_salida_fin'],
            (int)$datos['activo'],
            $id
        ]);
    }

    /**
     * Activa o desactiva un turno.
     */
    public static function cambiarEstado(int $id, bool $activo): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE turnos SET activo = ? WHERE id = ?");
        return $stmt->execute([$activo ? 1 : 0, $id]);
    }

    /**
     * Verifica si un código ya existe (para validación).
     */
    public static function existeCodigo(string $codigo, int $excluirId = 0): bool
    {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM turnos WHERE codigo = ?";
        $params = [$codigo];
        
        if ($excluirId > 0) {
            $sql .= " AND id != ?";
            $params[] = $excluirId;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}