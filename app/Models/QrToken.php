<?php
namespace App\Models;

use App\Core\Database;

class QrToken
{
    /**
     * Genera un nuevo token QR con vigencia limitada.
     * Lee los valores reales desde la BD para evitar desfases
     * entre la hora de PHP y la del servidor MySQL.
     */
    public static function generar(int $duracionSeg): array
    {
        $db = Database::getConnection();
        $token = bin2hex(random_bytes(32));

        $stmt = $db->prepare("
            INSERT INTO qr_tokens (token, generado_en, expira_en)
            VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL ? SECOND))
        ");
        $stmt->execute([$token, $duracionSeg]);
        $id = (int)$db->lastInsertId();

        // Leer valores reales desde la BD
        $stmt = $db->prepare("
            SELECT token, expira_en,
                   TIMESTAMPDIFF(SECOND, NOW(), expira_en) AS segundos_restantes
            FROM qr_tokens
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return [
            'id'                 => $id,
            'token'              => $row['token'],
            'expira_en'          => $row['expira_en'],
            'segundos_restantes' => (int)$row['segundos_restantes']
        ];
    }

    /**
     * Valida que el token exista y esté vigente.
     * No valida "usado" porque un mismo QR puede ser escaneado
     * por varios docentes mientras esté vigente.
     */
    public static function validar(string $token): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, token, generado_en, expira_en
            FROM qr_tokens
            WHERE token = ? AND expira_en > NOW()
            LIMIT 1
        ");
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    /**
     * @deprecated Ya no se usa. El QR se invalida por expiración
     * temporal, no por uso. Se mantiene para compatibilidad con
     * versiones anteriores.
     */
    public static function marcarUsado(int $id, int $docenteId): void
    {
        $db = Database::getConnection();
        $db->prepare("UPDATE qr_tokens SET usado = 1, docente_id = ? WHERE id = ?")
           ->execute([$docenteId, $id]);
    }

    /**
     * Elimina tokens expirados hace más de 1 día.
     * Pensado para ejecutarse como cron job diario.
     */
    public static function limpiarExpirados(): int
    {
        $db = Database::getConnection();
        return $db->exec("
            DELETE FROM qr_tokens
            WHERE expira_en < DATE_SUB(NOW(), INTERVAL 1 DAY)
        ");
    }
}