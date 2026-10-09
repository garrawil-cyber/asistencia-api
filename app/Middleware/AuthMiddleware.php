<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\JWT;
use App\Core\Database;

class AuthMiddleware
{
    /**
     * Valida JWT y verifica que no esté revocado en BD.
     * Devuelve el payload del token.
     */
    public static function check(): array
    {
        $token = Request::bearerToken();
        if (!$token) Response::error('Token no proporcionado', 401);

        $config  = require __DIR__ . '/../../config/config.php';
        $payload = JWT::decode($token, $config['jwt']['secret']);
        if (!$payload) Response::error('Token inválido o expirado', 401);

        // FIX #7: verificar que no esté revocado en BD
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id FROM tokens_sesion
            WHERE token = ? AND revocado = 0 AND expira_en > NOW()
        ");
        $stmt->execute([$token]);
        if (!$stmt->fetch()) {
            Response::error('Sesión cerrada o expirada', 401);
        }

        return $payload;
    }

    /**
     * Verifica rol(es). Devuelve payload.
     */
    public static function requireRol(array $roles): array
    {
        $user = self::check();
        if (!in_array($user['rol'], $roles, true)) {
            Response::error('No autorizado para esta acción', 403);
        }
        return $user;
    }

    /**
     * FIX #1: valida que la petición venga de un kiosko autenticado
     * mediante API Key en el header X-API-Key.
     */
    public static function checkKiosko(): void
    {
        $key = Request::header('X-API-Key');
        if (!$key) Response::error('API Key requerida', 401);

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM api_keys WHERE api_key = ? AND activo = 1");
        $stmt->execute([$key]);
        if (!$stmt->fetch()) Response::error('API Key inválida', 401);
    }
}