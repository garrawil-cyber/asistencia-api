<?php
namespace App\Admin\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Usuario;

class AdminAuthController
{
    /**
     * Muestra el formulario de login.
     * GET /admin/login
     */
    public function showLogin(): void
    {
        Session::start();

        // Si ya está logueado, redirigir al dashboard
        if (Session::get('usuario_id')) {
            header('Location: /asistencia-api/admin/dashboard');
            exit;
        }

        // Cargar vista
        require __DIR__ . '/../../../admin/views/auth/login.php';
    }

    /**
     * Procesa el login.
     * POST /admin/login
     */
    public function login(): void
    {
        Session::start();

        $username = trim(Request::input('username', ''));
        $password = Request::input('password', '');

        if (!$username || !$password) {
            $this->redirectWithError('Usuario y contraseña son obligatorios');
            return;
        }

        $user = Usuario::buscarPorUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->redirectWithError('Credenciales inválidas');
            return;
        }

        // Verificar que sea admin o coordinador (no docente)
        if (!in_array($user['rol'], ['admin', 'coordinador'], true)) {
            $this->redirectWithError('No tienes permisos para acceder al panel');
            return;
        }

        // Login exitoso → guardar datos en sesión
        Session::regenerate();
        Session::set('usuario_id', (int)$user['id']);
        Session::set('username',   $user['username']);
        Session::set('rol',        $user['rol']);

        Usuario::registrarLog((int)$user['id'], 'login', 'Inició sesión en el panel');

        Usuario::actualizarUltimoLogin((int)$user['id']);

        header('Location: /asistencia-api/admin/dashboard');
        exit;
    }

    /**
     * Cierra la sesión.
     * GET /admin/logout
     */
    public function logout(): void
    {

        $usuarioId = Session::get('usuario_id');
        $username  = Session::get('username');
        if ($usuarioId) {
            Usuario::registrarLog((int)$usuarioId, 'logout', 'Cerró sesión');
        }

        Session::destroy();
        header('Location: /asistencia-api/admin/login');
        exit;
    }

    private function redirectWithError(string $mensaje): void
    {
        Session::set('login_error', $mensaje);
        Session::set('login_username', Request::input('username', ''));
        header('Location: /asistencia-api/admin/login');
        exit;
    }
}