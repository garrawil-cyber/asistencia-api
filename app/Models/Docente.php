<?php
namespace App\Models;

use App\Core\Database;

class Docente
{
    /** ==================== CONSULTAS ==================== */

    public static function buscarPorId(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT d.*, u.username, u.activo AS usuario_activo, r.nombre AS rol
            FROM docentes d
            LEFT JOIN usuarios u ON u.id = d.usuario_id
            LEFT JOIN roles r ON r.id = u.rol_id
            WHERE d.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function buscarPorDni(string $dni): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM docentes WHERE dni = ?");
        $stmt->execute([$dni]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Lista docentes con filtros y paginación.
     */
    public static function listar(
        int $limit = 20,
        int $offset = 0,
        string $busqueda = '',
        string $estado = ''   // '' | 'activos' | 'inactivos'
    ): array {
        $db = Database::getConnection();

        $where = [];
        $params = [];

        if ($busqueda !== '') {
            $where[] = "(d.nombres LIKE ? OR d.apellidos LIKE ? OR d.dni LIKE ? OR u.username LIKE ?)";
            $like = '%' . $busqueda . '%';
            $params = array_merge($params, [$like, $like, $like, $like]);
        }

        if ($estado === 'activos')   $where[] = "d.activo = 1";
        if ($estado === 'inactivos') $where[] = "d.activo = 0";

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT d.id, d.dni, d.nombres, d.apellidos, d.email,
                   d.telefono, d.especialidad, d.activo, d.created_at,
                   u.username, u.id AS usuario_id
            FROM docentes d
            LEFT JOIN usuarios u ON u.id = d.usuario_id
            $whereSql
            ORDER BY d.activo DESC, d.apellidos ASC, d.nombres ASC
            LIMIT $limit OFFSET $offset
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Cuenta total para paginación.
     */
    public static function contar(string $busqueda = '', string $estado = ''): int
    {
        $db = Database::getConnection();

        $where = [];
        $params = [];

        if ($busqueda !== '') {
            $where[] = "(d.nombres LIKE ? OR d.apellidos LIKE ? OR d.dni LIKE ? OR u.username LIKE ?)";
            $like = '%' . $busqueda . '%';
            $params = array_merge($params, [$like, $like, $like, $like]);
        }

        if ($estado === 'activos')   $where[] = "d.activo = 1";
        if ($estado === 'inactivos') $where[] = "d.activo = 0";

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM docentes d
            LEFT JOIN usuarios u ON u.id = d.usuario_id
            $whereSql
        ");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /** ==================== ESCRITURA ==================== */

    /**
     * Crea un docente Y su usuario en una sola transacción.
     * Devuelve el ID del docente creado.
     */
    public static function crearConUsuario(array $datos): int
    {
        $db = Database::getConnection();

        $db->beginTransaction();
        try {
            // 1. Crear usuario
            $stmt = $db->prepare("
                INSERT INTO usuarios (username, password_hash, rol_id, activo)
                VALUES (?, ?, (SELECT id FROM roles WHERE nombre = 'docente'), 1)
            ");
            $hash = password_hash($datos['password'], PASSWORD_BCRYPT);
            $stmt->execute([$datos['username'], $hash]);
            $usuarioId = (int)$db->lastInsertId();

            // 2. Crear docente
            $stmt = $db->prepare("
                INSERT INTO docentes
                    (usuario_id, dni, nombres, apellidos, email, telefono, especialidad, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([
                $usuarioId,
                $datos['dni'],
                $datos['nombres'],
                $datos['apellidos'],
                $datos['email'] ?? null,
                $datos['telefono'] ?? null,
                $datos['especialidad'] ?? null
            ]);
            $docenteId = (int)$db->lastInsertId();

            $db->commit();
            return $docenteId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza datos del docente (no toca el usuario/password).
     */
    public static function actualizar(int $id, array $datos): bool
    {
        $db = Database::getConnection();

        $campos = [];
        $params = [];
        $permitidos = ['dni', 'nombres', 'apellidos', 'email', 'telefono', 'especialidad'];

        foreach ($permitidos as $campo) {
            if (array_key_exists($campo, $datos)) {
                $campos[] = "$campo = ?";
                $params[] = $datos[$campo];
            }
        }

        if (empty($campos)) return false;

        $params[] = $id;
        $stmt = $db->prepare("UPDATE docentes SET " . implode(', ', $campos) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    /**
     * Activa o desactiva un docente (soft delete).
     */
    public static function cambiarEstado(int $id, bool $activo): bool
    {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("UPDATE docentes SET activo = ? WHERE id = ?");
            $stmt->execute([$activo ? 1 : 0, $id]);

            // Sincronizar el usuario asociado
            $stmt = $db->prepare("
                UPDATE usuarios u
                JOIN docentes d ON d.usuario_id = u.id
                SET u.activo = ?
                WHERE d.id = ?
            ");
            $stmt->execute([$activo ? 1 : 0, $id]);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /** ==================== VALIDACIONES ==================== */

    public static function existeDni(string $dni, int $excluirId = 0): bool
    {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM docentes WHERE dni = ?";
        $params = [$dni];
        if ($excluirId > 0) {
            $sql .= " AND id != ?";
            $params[] = $excluirId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function existeUsername(string $username, int $excluirUsuarioId = 0): bool
    {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM usuarios WHERE username = ?";
        $params = [$username];
        if ($excluirUsuarioId > 0) {
            $sql .= " AND id != ?";
            $params[] = $excluirUsuarioId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function existeEmail(string $email, int $excluirId = 0): bool
    {
        if (empty($email)) return false;
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM docentes WHERE email = ?";
        $params = [$email];
        if ($excluirId > 0) {
            $sql .= " AND id != ?";
            $params[] = $excluirId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}