<?php
namespace App\Admin\Controllers;

use App\Admin\Middleware\SessionMiddleware;
use App\Core\Request;
use App\Core\Session;
use App\Models\Usuario;

class PerfilController
{
    /**
     * GET /admin/perfil
     */
    public function index(): void
    {
        $user = SessionMiddleware::check();

        // Cargar datos completos del usuario
        $usuarioData = Usuario::buscarPorId((int)$user['id']);

        if (!$usuarioData) {
            // Si por alguna razón no existe, redirigir al dashboard
            header('Location: /asistencia-api/admin/dashboard');
            exit;
        }

        $flashExito = Session::get('flash_exito');
        $flashError = Session::get('flash_error');
        $errores    = Session::get('errores', []);
        Session::remove('flash_exito');
        Session::remove('flash_error');
        Session::remove('errores');

        $datos = [
            'user'         => $user,
            'perfil'      => $usuarioData,
            'flashExito'   => $flashExito,
            'flashError'   => $flashError,
            'errores'      => $errores,
            'paginaActiva' => 'perfil'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/perfil/index.php';
    }

    /**
     * POST /admin/perfil/cambiar-password
     */
    public function cambiarPassword(): void
    {
        $user = SessionMiddleware::check();

        $actual   = Request::input('password_actual', '');
        $nueva    = Request::input('password_nueva', '');
        $confirma = Request::input('password_confirmar', '');

        $errores = [];

        // Validaciones
        if (empty($actual)) {
            $errores['password_actual'] = 'Debes ingresar tu contraseña actual.';
        }
        if (empty($nueva)) {
            $errores['password_nueva'] = 'La nueva contraseña es obligatoria.';
        } elseif (strlen($nueva) < 6) {
            $errores['password_nueva'] = 'Debe tener al menos 6 caracteres.';
        } elseif ($nueva === $actual) {
            $errores['password_nueva'] = 'La nueva contraseña debe ser diferente a la actual.';
        }
        if ($nueva !== $confirma) {
            $errores['password_confirmar'] = 'Las contraseñas no coinciden.';
        }

        // Verificar contraseña actual
        if (empty($errores['password_actual'])) {
            $usuarioData = Usuario::buscarPorUsername($user['username']);
            if (!password_verify($actual, $usuarioData['password_hash'])) {
                $errores['password_actual'] = 'La contraseña actual es incorrecta.';
            }
        }

        if (!empty($errores)) {
            Session::set('errores', $errores);
            header('Location: /asistencia-api/admin/perfil');
            exit;
        }

        // Actualizar
        try {
            Usuario::actualizarPassword((int)$user['id'], $nueva);

            // Registrar log
            Usuario::registrarLog((int)$user['id'], 'cambio_password', 'Cambió su contraseña');

            Session::set('flash_exito', 'Contraseña actualizada correctamente.');
            header('Location: /asistencia-api/admin/perfil');
            exit;
        } catch (\Exception $e) {
            Session::set('flash_error', 'Error al actualizar: ' . $e->getMessage());
            header('Location: /asistencia-api/admin/perfil');
            exit;
        }
    }
}