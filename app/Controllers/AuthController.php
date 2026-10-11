<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\JWT;
use App\Models\Usuario;
use App\Middleware\AuthMiddleware;

class AuthController
{
    public function login(): void
{
    $username    = trim(Request::input('username', ''));
    $password    = Request::input('password', '');
    $dispositivo = Request::input('dispositivo', 'desconocido');

    if (!$username || !$password) {
        Response::error('Usuario y contraseña son obligatorios', 422);
    }

    $user = Usuario::buscarPorUsername($username);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        Response::error('Credenciales inválidas', 401);
    }

    // ✨ NUEVO: Obtener el nombre completo del docente (si aplica)
    $nombreCompleto = null;
    if (!empty($user['docente_id'])) {
        $docente = \App\Models\Docente::buscarPorId((int)$user['docente_id']);
        if ($docente) {
            $nombreCompleto = trim($docente['nombres'] . ' ' . $docente['apellidos']);
        }
    }

    $config = require __DIR__ . '/../../config/config.php';
    $token  = JWT::encode([
        'usuario_id' => (int)$user['id'],
        'username'   => $user['username'],
        'rol'        => $user['rol'],
        'docente_id' => $user['docente_id'] !== null ? (int)$user['docente_id'] : null
    ], $config['jwt']['secret'], $config['jwt']['expira']);

    Usuario::guardarToken($user['id'], $token, $dispositivo, $_SERVER['REMOTE_ADDR'] ?? '');
    Usuario::actualizarUltimoLogin($user['id']);

    Response::success([
        'token'   => $token,
        'usuario' => [
            'id'              => (int)$user['id'],
            'username'        => $user['username'],
            'rol'             => $user['rol'],
            'docente_id'      => $user['docente_id'] !== null ? (int)$user['docente_id'] : null,
            'nombre_completo' => $nombreCompleto
        ]
    ], 'Login exitoso');
}

    public function logout(): void
    {
        $token = Request::bearerToken();
        if ($token) Usuario::revocarToken($token);
        Response::success(null, 'Sesión cerrada');
    }

    public function me(): void
    {
        $user = AuthMiddleware::check();
        Response::success($user, 'Datos del usuario');
    }
}