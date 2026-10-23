<?php
namespace App\Admin\Controllers;

use App\Admin\Middleware\SessionMiddleware;
use App\Core\Request;
use App\Core\Session;
use App\Models\Turno;
use App\Models\Usuario;

class TurnoController
{
    /**
     * GET /admin/turnos
     */
        public function index(): void
    {
        $user = SessionMiddleware::check();

        $busqueda = trim(Request::input('q', ''));
        $estado   = Request::input('estado', '');

        $turnos = Turno::listar($busqueda, $estado);

        $flashExito = Session::get('flash_exito');
        $flashError = Session::get('flash_error');
        Session::remove('flash_exito');
        Session::remove('flash_error');

        $datos = [
            'user'         => $user,
            'turnos'       => $turnos,
            'busqueda'     => $busqueda,
            'estado'       => $estado,
            'flashExito'   => $flashExito,
            'flashError'   => $flashError,
            'paginaActiva' => 'turnos'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/turnos/index.php';
    }

    /**
     * GET /admin/turnos/crear
     */
    public function mostrarCrear(): void
    {
        $user = SessionMiddleware::check();

        $errores = Session::get('errores', []);
        $old     = Session::get('old', []);
        Session::remove('errores');
        Session::remove('old');

        $datos = [
            'user'         => $user,
            'errores'      => $errores,
            'old'          => $old,
            'paginaActiva' => 'turnos'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/turnos/crear.php';
    }

    /**
     * POST /admin/turnos/crear
     */
    public function crear(): void
    {
        $user = SessionMiddleware::check();

        $datos = [
            'codigo'                 => trim(Request::input('codigo', '')),
            'nombre'                 => trim(Request::input('nombre', '')),
            'hora_entrada'           => Request::input('hora_entrada', ''),
            'hora_salida'            => Request::input('hora_salida', ''),
            'tolerancia_minutos'     => (int)Request::input('tolerancia_minutos', 5),
            'ventana_entrada_inicio' => Request::input('ventana_entrada_inicio', ''),
            'ventana_entrada_fin'    => Request::input('ventana_entrada_fin', ''),
            'ventana_salida_inicio'  => Request::input('ventana_salida_inicio', ''),
            'ventana_salida_fin'     => Request::input('ventana_salida_fin', ''),
            'activo'                 => Request::input('activo', '1')
        ];

        $errores = $this->validar($datos, true);

        if (!empty($errores)) {
            Session::set('errores', $errores);
            Session::set('old', $datos);
            header('Location: /asistencia-api/admin/turnos/crear');
            exit;
        }

        try {
            Turno::crear($datos);

            Usuario::registrarLog(
                (int)$user['id'],
                'crear_turno',
                'Creó turno: ' . $datos['nombre']
            );

            Session::set('flash_exito', 'Turno creado correctamente.');
            header('Location: /asistencia-api/admin/turnos');
            exit;
        } catch (\Exception $e) {
            Session::set('errores', ['general' => 'Error: ' . $e->getMessage()]);
            Session::set('old', $datos);
            header('Location: /asistencia-api/admin/turnos/crear');
            exit;
        }
    }

    /**
     * GET /admin/turnos/editar/{id}
     */
    public function mostrarEditar(string $id): void
    {
        $user = SessionMiddleware::check();

        $turno = Turno::buscarPorId((int)$id);
        if (!$turno) {
            Session::set('flash_error', 'Turno no encontrado.');
            header('Location: /asistencia-api/admin/turnos');
            exit;
        }

        $errores = Session::get('errores', []);
        $old     = Session::get('old', []);
        Session::remove('errores');
        Session::remove('old');

        if (!empty($old)) {
            $turno = array_merge($turno, $old);
        }

        $datos = [
            'user'         => $user,
            'turno'        => $turno,
            'errores'      => $errores,
            'paginaActiva' => 'turnos'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/turnos/editar.php';
    }

    /**
     * POST /admin/turnos/editar/{id}
     */
    public function editar(string $id): void
    {
        $user      = SessionMiddleware::check();
        $turnoId   = (int)$id;

        $turnoExistente = Turno::buscarPorId($turnoId);
        if (!$turnoExistente) {
            Session::set('flash_error', 'Turno no encontrado.');
            header('Location: /asistencia-api/admin/turnos');
            exit;
        }

        $datos = [
            'codigo'                 => trim(Request::input('codigo', '')),
            'nombre'                 => trim(Request::input('nombre', '')),
            'hora_entrada'           => Request::input('hora_entrada', ''),
            'hora_salida'            => Request::input('hora_salida', ''),
            'tolerancia_minutos'     => (int)Request::input('tolerancia_minutos', 5),
            'ventana_entrada_inicio' => Request::input('ventana_entrada_inicio', ''),
            'ventana_entrada_fin'    => Request::input('ventana_entrada_fin', ''),
            'ventana_salida_inicio'  => Request::input('ventana_salida_inicio', ''),
            'ventana_salida_fin'     => Request::input('ventana_salida_fin', ''),
            'activo'                 => Request::input('activo', '0')
        ];

        $errores = $this->validar($datos, false, $turnoId);

        if (!empty($errores)) {
            Session::set('errores', $errores);
            Session::set('old', $datos);
            header("Location: /asistencia-api/admin/turnos/editar/$turnoId");
            exit;
        }

        try {
            Turno::actualizar($turnoId, $datos);

            Usuario::registrarLog(
                (int)$user['id'],
                'editar_turno',
                'Editó turno: ' . $datos['nombre']
            );

            Session::set('flash_exito', 'Turno actualizado correctamente.');
            header('Location: /asistencia-api/admin/turnos');
            exit;
        } catch (\Exception $e) {
            Session::set('errores', ['general' => 'Error: ' . $e->getMessage()]);
            Session::set('old', $datos);
            header("Location: /asistencia-api/admin/turnos/editar/$turnoId");
            exit;
        }
    }

    /**
     * POST /admin/turnos/toggle/{id}
     */
    public function toggle(string $id): void
    {
        SessionMiddleware::check();
        $turnoId = (int)$id;

        $turno = Turno::buscarPorId($turnoId);
        if (!$turno) {
            Session::set('flash_error', 'Turno no encontrado.');
            header('Location: /asistencia-api/admin/turnos');
            exit;
        }

        $nuevoEstado = !$turno['activo'];
        Turno::cambiarEstado($turnoId, $nuevoEstado);

        Session::set('flash_exito', $nuevoEstado ? 'Turno activado.' : 'Turno desactivado.');
        header('Location: /asistencia-api/admin/turnos');
    }

    /**
     * Validaciones.
     */
    private function validar(array $datos, bool $esCreacion, int $excluirId = 0): array
    {
        $errores = [];

        if (empty($datos['codigo'])) {
            $errores['codigo'] = 'El código es obligatorio.';
        } elseif (!preg_match('/^[a-z0-9_]{2,20}$/', $datos['codigo'])) {
            $errores['codigo'] = 'Solo letras minúsculas, números y guiones bajos (2-20).';
        } elseif ($esCreacion && Turno::existeCodigo($datos['codigo'])) {
            $errores['codigo'] = 'Este código ya existe.';
        } elseif (!$esCreacion && Turno::existeCodigo($datos['codigo'], $excluirId)) {
            $errores['codigo'] = 'Este código ya existe.';
        }

        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio.';
        } elseif (strlen($datos['nombre']) > 50) {
            $errores['nombre'] = 'Máximo 50 caracteres.';
        }

        if (empty($datos['hora_entrada'])) {
            $errores['hora_entrada'] = 'La hora de entrada es obligatoria.';
        }
        if (empty($datos['hora_salida'])) {
            $errores['hora_salida'] = 'La hora de salida es obligatoria.';
        }
        if ($datos['hora_entrada'] >= $datos['hora_salida']) {
            $errores['hora_salida'] = 'La hora de salida debe ser posterior a la de entrada.';
        }

        if (empty($datos['ventana_entrada_inicio'])) {
            $errores['ventana_entrada_inicio'] = 'Obligatorio.';
        }
        if (empty($datos['ventana_entrada_fin'])) {
            $errores['ventana_entrada_fin'] = 'Obligatorio.';
        }
        if (empty($datos['ventana_salida_inicio'])) {
            $errores['ventana_salida_inicio'] = 'Obligatorio.';
        }
        if (empty($datos['ventana_salida_fin'])) {
            $errores['ventana_salida_fin'] = 'Obligatorio.';
        }

        if ($datos['tolerancia_minutos'] < 0 || $datos['tolerancia_minutos'] > 60) {
            $errores['tolerancia_minutos'] = 'Entre 0 y 60 minutos.';
        }

        return $errores;
    }
}