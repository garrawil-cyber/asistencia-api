<?php
namespace App\Admin\Controllers;

use App\Admin\Middleware\SessionMiddleware;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database; // Agregado para consultar turnos
use App\Models\Docente;
use App\Models\Usuario;

class DocenteController
{

  public function index(): void
    {
        $user = SessionMiddleware::check();

        $busqueda = trim(Request::input('q', ''));
        $estado   = Request::input('estado', '');
        $pagina   = max(1, (int)Request::input('pagina', 1));
        $porPag    = 15;
        $offset    = ($pagina - 1) * $porPag;

        $docentes = Docente::listar($porPag, $offset, $busqueda, $estado);
        $total    = Docente::contar($busqueda, $estado);
        $paginas  = (int)ceil($total / $porPag);

        $flashExito = Session::get('flash_exito');
        $flashError = Session::get('flash_error');
        Session::remove('flash_exito');
        Session::remove('flash_error');

        $datos = [
            'user'         => $user,
            'docentes'     => $docentes,
            'total'        => $total,
            'pagina'       => $pagina,
            'paginas'      => $paginas,
            'busqueda'     => $busqueda,
            'estado'       => $estado,
            'flashExito'   => $flashExito,
            'flashError'   => $flashError,
            'paginaActiva' => 'docentes'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/docentes/index.php';
    }

    public function mostrarCrear(): void
    {
        $user = SessionMiddleware::check();

        $errores = Session::get('errores', []);
        $old     = Session::get('old', []);
        Session::remove('errores');
        Session::remove('old');

        // Obtener turnos activos
        $db = Database::getConnection();
        $turnos = $db->query("SELECT * FROM turnos WHERE activo = 1 ORDER BY hora_entrada")->fetchAll();

        $datos = [
            'user'         => $user,
            'errores'      => $errores,
            'old'          => $old,
            'turnos'       => $turnos,
            'paginaActiva' => 'docentes'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/docentes/crear.php';
    }

    public function crear(): void
    {
        SessionMiddleware::check();

        $datos = [
            'dni'          => trim(Request::input('dni', '')),
            'nombres'      => trim(Request::input('nombres', '')),
            'apellidos'    => trim(Request::input('apellidos', '')),
            'email'        => trim(Request::input('email', '')),
            'telefono'     => trim(Request::input('telefono', '')),
            'especialidad' => trim(Request::input('especialidad', '')),
            'turno_id'     => Request::input('turno_id', null), // NUEVO
            'username'     => trim(Request::input('username', '')),
            'password'     => Request::input('password', '')
        ];

        $errores = $this->validar($datos, true);

        if (!empty($errores)) {
            Session::set('errores', $errores);
            Session::set('old', $datos);
            header('Location: /asistencia-api/admin/docentes/crear');
            exit;
        }

        try {
            Docente::crearConUsuario($datos);
            Usuario::registrarLog(
                (int)Session::get('usuario_id'),
                'crear_docente',
                'Creó al docente: ' . $datos['nombres'] . ' ' . $datos['apellidos']
            );
            Session::set('flash_exito', 'Docente registrado correctamente.');
            header('Location: /asistencia-api/admin/docentes');
            exit;
        } catch (\Exception $e) {
            Session::set('errores', ['general' => 'Error al guardar: ' . $e->getMessage()]);
            Session::set('old', $datos);
            header('Location: /asistencia-api/admin/docentes/crear');
            exit;
        }
    }

    public function mostrarEditar(string $id): void
    {
        $user = SessionMiddleware::check();

        $docente = Docente::buscarPorId((int)$id);
        if (!$docente) { /* ... */ }

        $errores = Session::get('errores', []);
        $old     = Session::get('old', []);
        Session::remove('errores');
        Session::remove('old');

        if (!empty($old)) {
            $docente = array_merge($docente, $old);
        }

        // Obtener turnos activos
        $db = Database::getConnection();
        $turnos = $db->query("SELECT * FROM turnos WHERE activo = 1 ORDER BY hora_entrada")->fetchAll();

        $datos = [
            'user'         => $user,
            'docente'      => $docente,
            'errores'      => $errores,
            'turnos'       => $turnos,
            'paginaActiva' => 'docentes'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/docentes/editar.php';
    }

        /**
     * POST /admin/docentes/editar/{id}
     */
    public function editar(string $id): void
    {
        SessionMiddleware::check();
        $docenteId = (int)$id;

        $docenteExistente = Docente::buscarPorId($docenteId);
        if (!$docenteExistente) {
            Session::set('flash_error', 'Docente no encontrado.');
            header('Location: /asistencia-api/admin/docentes');
            exit;
        }

        $datos = [
            'dni'          => trim(Request::input('dni', '')),
            'nombres'      => trim(Request::input('nombres', '')),
            'apellidos'    => trim(Request::input('apellidos', '')),
            'email'        => trim(Request::input('email', '')),
            'telefono'     => trim(Request::input('telefono', '')),
            'especialidad' => trim(Request::input('especialidad', '')),
            'turno_id'     => Request::input('turno_id', null) // NUEVO
        ];

        $errores = $this->validar($datos, false, $docenteId);

        if (!empty($errores)) {
            Session::set('errores', $errores);
            Session::set('old', $datos);
            header("Location: /asistencia-api/admin/docentes/editar/$docenteId");
            exit;
        }

        try {
            Docente::actualizar($docenteId, $datos);

            // ✅ CORREGIDO AQUÍ: Se agregaron los 3 parámetros requeridos
            Usuario::registrarLog(
                (int)Session::get('usuario_id'),
                'editar_docente',
                'Editó al docente: ' . $datos['nombres'] . ' ' . $datos['apellidos']
            );

            Session::set('flash_exito', 'Docente actualizado correctamente.');
            header('Location: /asistencia-api/admin/docentes');
            exit;
        } catch (\Exception $e) {
            Session::set('errores', ['general' => 'Error al actualizar: ' . $e->getMessage()]);
            Session::set('old', $datos);
            header("Location: /asistencia-api/admin/docentes/editar/$docenteId");
            exit;
        }
    }

    /**
     * POST /admin/docentes/toggle/{id}
     */
    public function toggle(string $id): void
    {
        SessionMiddleware::check();
        $docenteId = (int)$id;

        $docente = Docente::buscarPorId($docenteId);
        if (!$docente) {
            Session::set('flash_error', 'Docente no encontrado.');
            header('Location: /asistencia-api/admin/docentes');
            exit;
        }

        $nuevoEstado = !$docente['activo'];
        Docente::cambiarEstado($docenteId, $nuevoEstado);

        Usuario::registrarLog(
        (int)Session::get('usuario_id'),
        'toggle_docente',
        ($nuevoEstado ? 'Activó' : 'Desactivó') . ' al docente: ' . $docente['nombres']
        );

        Session::set('flash_exito', $nuevoEstado ? 'Docente activado.' : 'Docente desactivado.');
        header('Location: /asistencia-api/admin/docentes');
    }

       /**
     * Valida los datos de entrada.
     */
    private function validar(array $datos, bool $esCreacion, int $excluirId = 0): array
    {
        $errores = [];

        // DNI
        if (empty($datos['dni'])) {
            $errores['dni'] = 'El DNI es obligatorio.';
        } elseif (!preg_match('/^[0-9]{8}$/', $datos['dni'])) {
            $errores['dni'] = 'El DNI debe tener exactamente 8 dígitos numéricos.';
        } elseif (Docente::existeDni($datos['dni'], $excluirId)) {
            $errores['dni'] = 'Este DNI ya está registrado.';
        }

        // Nombres
        if (empty($datos['nombres'])) {
            $errores['nombres'] = 'Los nombres son obligatorios.';
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}$/u', $datos['nombres'])) {
            $errores['nombres'] = 'Solo letras y espacios (2-100 caracteres).';
        }

        // Apellidos
        if (empty($datos['apellidos'])) {
            $errores['apellidos'] = 'Los apellidos son obligatorios.';
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}$/u', $datos['apellidos'])) {
            $errores['apellidos'] = 'Solo letras y espacios (2-100 caracteres).';
        }

        // Email
        if (!empty($datos['email'])) {
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = 'Correo electrónico inválido.';
            } elseif (Docente::existeEmail($datos['email'], $excluirId)) {
                $errores['email'] = 'Este correo ya está registrado.';
            }
        }

        // Teléfono (opcional)
        if (!empty($datos['telefono']) && !preg_match('/^[0-9]{6,15}$/', $datos['telefono'])) {
            $errores['telefono'] = 'Solo números (6-15 dígitos).';
        }

        // Username (solo creación)
        if ($esCreacion) {
            if (empty($datos['username'])) {
                $errores['username'] = 'El usuario es obligatorio.';
            } elseif (!preg_match('/^[a-zA-Z0-9._]{3,50}$/', $datos['username'])) {
                $errores['username'] = 'Solo letras, números, puntos y guiones bajos (3-50).';
            } elseif (Docente::existeUsername($datos['username'])) {
                $errores['username'] = 'Este usuario ya existe.';
            }
        }

        // Password (solo creación)
        if ($esCreacion) {
            if (empty($datos['password'])) {
                $errores['password'] = 'La contraseña es obligatoria.';
            } elseif (strlen($datos['password']) < 6) {
                $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
            }
        }

        // NUEVA VALIDACIÓN: Turno único por docente
        if (empty($datos['turno_id'])) {
            $errores['turno_id'] = 'Debes asignar un turno al docente.';
        } else {
            // Verificar si el docente ya tiene un turno asignado diferente
            if (!$esCreacion && $excluirId > 0) {
                // ✅ CORREGIDO AQUÍ: Usar Docente:: en lugar de self::
                $docenteActual = Docente::buscarPorId($excluirId);
                
                // Si el docente ya tenía un turno y se intenta cambiar a otro
                if ($docenteActual && $docenteActual['turno_id'] != $datos['turno_id']) {
                    // Verificar si ya hay asistencias registradas en el turno anterior
                    $db = Database::getConnection();
                    $stmt = $db->prepare("SELECT COUNT(*) FROM asistencias WHERE docente_id = ? AND turno_id = ?");
                    $stmt->execute([$excluirId, $docenteActual['turno_id']]);
                    $asistenciasAnteriores = (int)$stmt->fetchColumn();
                    
                    if ($asistenciasAnteriores > 0) {
                        $errores['turno_id'] = 'No puedes cambiar el turno de este docente porque ya tiene asistencias registradas en su turno actual.';
                    }
                }
            }
            
            // Verificar que el turno seleccionado exista y esté activo
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT id FROM turnos WHERE id = ? AND activo = 1");
            $stmt->execute([$datos['turno_id']]);
            if (!$stmt->fetch()) {
                $errores['turno_id'] = 'El turno seleccionado no es válido o está inactivo.';
            }
        }

        return $errores;
    }
}