<?php
namespace App\Models;

use App\Core\Database;

class Usuario
{
    /** Cache de config para no leer el archivo en cada llamada */
    private static ?array $config = null;

    private static function config(): array
    {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../../config/config.php';
        }
        return self::$config;
    }

    /**
     * Busca un usuario activo por username, incluyendo su rol
     * y (si aplica) su ID de docente.
     */
    public static function buscarPorUsername(string $username): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.id, u.username, u.password_hash, u.activo,
                   u.ultimo_login, u.created_at,
                   r.nombre AS rol,
                   d.id     AS docente_id
            FROM usuarios u
            JOIN roles r ON r.id = u.rol_id
            LEFT JOIN docentes d ON d.usuario_id = u.id
            WHERE u.username = ? AND u.activo = 1
            LIMIT 1
        ");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Actualiza la fecha de último login.
     */
    public static function actualizarUltimoLogin(int $id): void
    {
        $db = Database::getConnection();
        $db->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?")
           ->execute([$id]);
    }

    /**
     * Guarda el token JWT emitido, para poder revocarlo después.
     */
    public static function guardarToken(
        int $usuarioId,
        string $token,
        string $dispositivo,
        string $ip
    ): void {
        $db = Database::getConnection();
        $config = self::config();
        $expira = date('Y-m-d H:i:s', time() + $config['jwt']['expira']);

        $db->prepare("
            INSERT INTO tokens_sesion
                (usuario_id, token, dispositivo, ip, expira_en)
            VALUES (?, ?, ?, ?, ?)
        ")->execute([$usuarioId, $token, $dispositivo, $ip, $expira]);
    }

    /**
     * Marca el token como revocado (logout).
     */
    public static function revocarToken(string $token): void
    {
        $db = Database::getConnection();
        $db->prepare("
            UPDATE tokens_sesion SET revocado = 1
            WHERE token = ? AND revocado = 0
        ")->execute([$token]);
    }

    /**
     * Limpia tokens expirados hace más de 30 días.
     * Pensado para cron job.
     */
    public static function limpiarTokensViejos(): int
    {
        $db = Database::getConnection();
        return $db->exec("
            DELETE FROM tokens_sesion
            WHERE expira_en < DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
    }
    /**
     * Actualiza la contraseña del usuario.
     */
    public static function actualizarPassword(int $usuarioId, string $nuevaPassword): bool
    {
        $db = Database::getConnection();
        $hash = password_hash($nuevaPassword, PASSWORD_BCRYPT);

        $stmt = $db->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$hash, $usuarioId]);
    }

    /**
     * Registra una acción en el log de auditoría.
     */
    public static function registrarLog(int $usuarioId, string $accion, ?string $detalle = null): void
    {
        $db = Database::getConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        try {
            $stmt = $db->prepare("
                INSERT INTO logs_actividad (usuario_id, accion, detalle, ip)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$usuarioId, $accion, $detalle, $ip]);
        } catch (\Exception $e) {
            // Silenciar errores de log para no romper el flujo principal
            error_log('Error al registrar log: ' . $e->getMessage());
        }
    }

    /**
     * Lista de logs con filtros y paginación.
     */
    public static function listarLogs(
        int $limit = 30,
        int $offset = 0,
        int $usuarioId = 0,
        string $desde = '',
        string $hasta = ''
    ): array {
        $db = Database::getConnection();

        $where = ['1=1'];
        $params = [];

        if ($usuarioId > 0) {
            $where[] = "l.usuario_id = ?";
            $params[] = $usuarioId;
        }
        if ($desde !== '') {
            $where[] = "DATE(l.created_at) >= ?";
            $params[] = $desde;
        }
        if ($hasta !== '') {
            $where[] = "DATE(l.created_at) <= ?";
            $params[] = $hasta;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $stmt = $db->prepare("
            SELECT l.id, l.accion, l.detalle, l.ip, l.created_at,
                   u.username, r.nombre AS rol
            FROM logs_actividad l
            LEFT JOIN usuarios u ON u.id = l.usuario_id
            LEFT JOIN roles r ON r.id = u.rol_id
            $whereSql
            ORDER BY l.id DESC
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Cuenta total de logs con filtros.
     */
    public static function contarLogs(int $usuarioId = 0, string $desde = '', string $hasta = ''): int
    {
        $db = Database::getConnection();

        $where = ['1=1'];
        $params = [];

        if ($usuarioId > 0) {
            $where[] = "usuario_id = ?";
            $params[] = $usuarioId;
        }
        if ($desde !== '') {
            $where[] = "DATE(created_at) >= ?";
            $params[] = $desde;
        }
        if ($hasta !== '') {
            $where[] = "DATE(created_at) <= ?";
            $params[] = $hasta;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $stmt = $db->prepare("SELECT COUNT(*) FROM logs_actividad $whereSql");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Lista simple de usuarios (para el filtro).
     */
    public static function listarTodos(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT u.id, u.username, r.nombre AS rol
            FROM usuarios u
            JOIN roles r ON r.id = u.rol_id
            WHERE u.activo = 1
            ORDER BY u.username
        ");
        return $stmt->fetchAll();
    }

    /**
     * Busca un usuario por su ID con datos completos (para el panel admin).
     */
    public static function buscarPorId(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.id, u.username, u.activo, u.ultimo_login, u.created_at,
                   u.password_hash,
                   r.nombre AS rol
            FROM usuarios u
            JOIN roles r ON r.id = u.rol_id
            WHERE u.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

}