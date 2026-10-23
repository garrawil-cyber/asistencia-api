<?php
namespace App\Admin\Controllers;

use App\Admin\Middleware\SessionMiddleware;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Models\Usuario;

class UsuarioController
{
    /**
     * GET /admin/usuarios
     */
    public function index(): void
    {
        $user = SessionMiddleware::check();

        // Solo el administrador puede gestionar usuarios
        if ($user['rol'] !== 'admin') {
            Session::set('flash_error', 'No tienes permisos para acceder a esta sección.');
            header('Location: /asistencia-api/admin/dashboard');
            exit;
        }

        $db = Database::getConnection();
        
        // Listar usuarios con rol admin o coordinador (excluyendo docentes)
        $stmt = $db->query("
            SELECT u.*, r.nombre AS rol_nombre 
            FROM usuarios u 
            JOIN roles r ON r.id = u.rol_id 
            WHERE r.nombre IN ('admin', 'coordinador')
            ORDER BY u.id ASC
        ");
        $usuarios = $stmt->fetchAll();

        $flashExito = Session::get('flash_exito');
        $flashError = Session::get('flash_error');
        $errores    = Session::get('errores', []);
        $old        = Session::get('old', []);
        Session::remove('flash_exito');
        Session::remove('flash_error');
        Session::remove('errores');
        Session::remove('old');

        extract([
            'user' => $user, 'usuarios' => $usuarios,
            'flashExito' => $flashExito, 'flashError' => $flashError,
            'errores' => $errores, 'old' => $old,
            'paginaActiva' => 'usuarios'
        ]);
        require __DIR__ . '/../../../admin/views/usuarios/index.php';
    }

    /**
     * POST /admin/usuarios/crear
     */
    public function crear(): void
    {
        $user = SessionMiddleware::check();
        if ($user['rol'] !== 'admin') { header('Location: /asistencia-api/admin/dashboard'); exit; }

        $datos = [
            'username'        => trim(Request::input('username', '')),
            'nombre_completo' => trim(Request::input('nombre_completo', '')),
            'password'        => Request::input('password', ''),
            'rol_id'          => (int)Request::input('rol_id', 2) // 2 = coordinador por defecto
        ];

        $errores = [];
        if (empty($datos['username'])) $errores['username'] = 'El usuario es obligatorio.';
        elseif (!preg_match('/^[a-zA-Z0-9._]{3,50}$/', $datos['username'])) $errores['username'] = 'Solo letras, números, puntos y _ (3-50).';
        elseif (Usuario::existeUsername($datos['username'])) $errores['username'] = 'Este usuario ya existe.';
        
        if (empty($datos['nombre_completo'])) $errores['nombre_completo'] = 'El nombre completo es obligatorio.';
        if (empty($datos['password'])) $errores['password'] = 'La contraseña es obligatoria.';
        elseif (strlen($datos['password']) < 6) $errores['password'] = 'Mínimo 6 caracteres.';

        if (!empty($errores)) {
            Session::set('errores', $errores);
            Session::set('old', $datos);
            header('Location: /asistencia-api/admin/usuarios');
            exit;
        }

        $db = Database::getConnection();
        $hash = password_hash($datos['password'], PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO usuarios (username, nombre_completo, password_hash, rol_id, activo) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$datos['username'], $datos['nombre_completo'], $hash, $datos['rol_id']]);

        Usuario::registrarLog((int)$user['id'], 'crear_usuario', 'Creó al usuario: ' . $datos['username']);
        Session::set('flash_exito', 'Usuario creado correctamente.');
        header('Location: /asistencia-api/admin/usuarios');
    }

    /**
     * POST /admin/usuarios/editar/{id}
     */
    public function editar(string $id): void
    {
        $user = SessionMiddleware::check();
        if ($user['rol'] !== 'admin') { header('Location: /asistencia-api/admin/dashboard'); exit; }
        
        $usuarioId = (int)$id;
        $db = Database::getConnection();
        
        $nombre = trim(Request::input('nombre_completo', ''));
        $rolId  = (int)Request::input('rol_id', 2);
        $pass   = Request::input('password', '');

        if ($usuarioId === (int)$user['id'] && $rolId !== 1) {
            Session::set('flash_error', 'No puedes quitarte el rol de administrador a ti mismo.');
            header('Location: /asistencia-api/admin/usuarios');
            exit;
        }

        $stmt = $db->prepare("UPDATE usuarios SET nombre_completo = ?, rol_id = ? WHERE id = ?");
        $stmt->execute([$nombre, $rolId, $usuarioId]);

        if (!empty($pass)) {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $db->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?")->execute([$hash, $usuarioId]);
        }

        Usuario::registrarLog((int)$user['id'], 'editar_usuario', 'Editó al usuario ID: ' . $usuarioId);
        Session::set('flash_exito', 'Usuario actualizado correctamente.');
        header('Location: /asistencia-api/admin/usuarios');
    }

        /**
     * POST /admin/usuarios/toggle/{id}
     */
    public function toggle(string $id): void
    {
        $user = SessionMiddleware::check();
        if ($user['rol'] !== 'admin') { header('Location: /asistencia-api/admin/dashboard'); exit; }
        
        $usuarioId = (int)$id;
        if ($usuarioId === (int)$user['id']) {
            Session::set('flash_error', 'No puedes desactivar tu propia cuenta.');
            header('Location: /asistencia-api/admin/usuarios');
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->query("SELECT activo, username FROM usuarios WHERE id = $usuarioId");
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            Session::set('flash_error', 'Usuario no encontrado.');
            header('Location: /asistencia-api/admin/usuarios');
            exit;
        }

        $nuevoEstado = !$usuario['activo'];
        $db->prepare("UPDATE usuarios SET activo = ? WHERE id = ?")->execute([$nuevoEstado ? 1 : 0, $usuarioId]);
        
        Session::set('flash_exito', $nuevoEstado ? 'Usuario activado.' : 'Usuario desactivado.');
        header('Location: /asistencia-api/admin/usuarios');
    }
}