<?php
namespace App\Admin\Middleware;

use App\Core\Session;
use App\Core\Response;

class SessionMiddleware
{
    /**
     * Verifica que el usuario esté logueado en el panel.
     * Si no, redirige al login.
     */
    public static function check(): array
    {
        Session::start();

        $usuarioId = Session::get('usuario_id');
        $rol       = Session::get('rol');

        if (!$usuarioId || !in_array($rol, ['admin', 'coordinador'], true)) {
            // No autenticado o sin permisos → redirigir al login
            header('Location: /asistencia-api/admin/login');
            exit;
        }

        return [
            'id'       => $usuarioId,
            'username' => Session::get('username'),
            'rol'      => $rol
        ];
    }

    /**
     * Verifica que sea ADMIN específicamente.
     */
    public static function requireAdmin(): array
    {
        $user = self::check();
        if ($user['rol'] !== 'admin') {
            http_response_code(403);
            echo "No tienes permisos para acceder a esta sección.";
            exit;
        }
        return $user;
    }
}