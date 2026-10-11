<?php
namespace App\Admin\Controllers;

use App\Admin\Middleware\SessionMiddleware;
use App\Core\Request;
use App\Core\Session;
use App\Models\Docente;
use App\Models\Justificacion;
use App\Models\Usuario;

class JustificacionController
{
    /**
     * GET /admin/justificaciones
     */
    public function index(): void
    {
        $user = SessionMiddleware::check();

        $estado    = Request::input('estado', '');
        $docenteId = (int)Request::input('docente_id', 0);
        $desde     = Request::input('desde', '');
        $hasta     = Request::input('hasta', '');
        $pagina    = max(1, (int)Request::input('pagina', 1));
        $porPag    = (int)Request::input('por_pagina', 20);
        $porPag    = in_array($porPag, [10, 15, 25, 50, 100]) ? $porPag : 20;
        $offset    = ($pagina - 1) * $porPag;

        $justificaciones = Justificacion::listar($porPag, $offset, $estado, $docenteId, $desde, $hasta);
        $total           = Justificacion::contar($estado, $docenteId, $desde, $hasta);
        $paginas         = (int)ceil($total / $porPag);
        $resumen         = Justificacion::resumen();
        $docentes        = Docente::listar(500, 0, '', 'activos');

        $flashExito = Session::get('flash_exito');
        $flashError = Session::get('flash_error');
        Session::remove('flash_exito');
        Session::remove('flash_error');

        $datos = [
            'user'             => $user,
            'justificaciones'  => $justificaciones,
            'total'            => $total,
            'pagina'           => $pagina,
            'paginas'          => $paginas,
            'porPag'           => $porPag,
            'estado'           => $estado,
            'docenteId'        => $docenteId,
            'desde'            => $desde,
            'hasta'            => $hasta,
            'resumen'          => $resumen,
            'docentes'         => $docentes,
            'flashExito'       => $flashExito,
            'flashError'       => $flashError,
            'paginaActiva'     => 'justificaciones'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/justificaciones/index.php';
    }

    /**
     * GET /admin/justificaciones/ver/{id}
     */
    public function ver(string $id): void
    {
        $user = SessionMiddleware::check();

        $justificacion = Justificacion::buscarPorId((int)$id);
        if (!$justificacion) {
            Session::set('flash_error', 'Justificación no encontrada.');
            header('Location: /asistencia-api/admin/justificaciones');
            exit;
        }

        $datos = [
            'user'           => $user,
            'justificacion'  => $justificacion,
            'paginaActiva'   => 'justificaciones'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/justificaciones/ver.php';
    }

    /**
     * POST /admin/justificaciones/responder/{id}
     */
    public function responder(string $id): void
    {
        $user = SessionMiddleware::check();
        $justificacionId = (int)$id;

        $accion = Request::input('accion', '');
        if (!in_array($accion, ['aprobado', 'rechazado'])) {
            Session::set('flash_error', 'Acción inválida.');
            header('Location: /asistencia-api/admin/justificaciones');
            exit;
        }

        try {
            Justificacion::responder($justificacionId, $accion, (int)$user['id']);

            Usuario::registrarLog(
                (int)$user['id'],
                'responder_justificacion',
                ucfirst($accion) . ' justificación #' . $justificacionId
            );

            $mensaje = $accion === 'aprobado'
                ? 'Justificación aprobada correctamente.'
                : 'Justificación rechazada.';

            Session::set('flash_exito', $mensaje);
        } catch (\Exception $e) {
            Session::set('flash_error', 'Error al procesar: ' . $e->getMessage());
        }

        header('Location: /asistencia-api/admin/justificaciones');
        exit;
    }

    /**
     * GET /admin/justificaciones/crear
     */
    public function mostrarCrear(): void
    {
        $user = SessionMiddleware::check();

        $docenteId = (int)Request::input('docente_id', 0);
        $asistencias = Justificacion::asistenciasSinJustificar($docenteId);
        $docentes = Docente::listar(500, 0, '', 'activos');

        $errores = Session::get('errores', []);
        $old     = Session::get('old', []);
        Session::remove('errores');
        Session::remove('old');

        $datos = [
            'user'         => $user,
            'asistencias'  => $asistencias,
            'docentes'     => $docentes,
            'docenteId'    => $docenteId,
            'errores'      => $errores,
            'old'          => $old,
            'paginaActiva' => 'justificaciones'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/justificaciones/crear.php';
    }

    /**
     * POST /admin/justificaciones/crear
     */
    public function crear(): void
    {
        $user = SessionMiddleware::check();

        $asistenciaId = (int)Request::input('asistencia_id', 0);
        $motivo       = trim(Request::input('motivo', ''));

        $errores = [];
        if (!$asistenciaId) $errores['asistencia_id'] = 'Selecciona una asistencia.';
        if (empty($motivo)) $errores['motivo'] = 'El motivo es obligatorio.';
        elseif (strlen($motivo) < 10) $errores['motivo'] = 'Mínimo 10 caracteres.';

        if (!empty($errores)) {
            Session::set('errores', $errores);
            Session::set('old', ['asistencia_id' => $asistenciaId, 'motivo' => $motivo]);
            header('Location: /asistencia-api/admin/justificaciones/crear');
            exit;
        }

        // Procesar archivo si viene
        $archivoUrl = null;
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['archivo'];
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg', 'jpeg', 'png', 'pdf'];

            if (!in_array($extension, $permitidos)) {
                $errores['archivo'] = 'Solo se permiten JPG, PNG o PDF.';
            } elseif ($archivo['size'] > 5 * 1024 * 1024) {
                $errores['archivo'] = 'El archivo no puede superar 5 MB.';
            } else {
                $nombre = 'just_' . uniqid() . '.' . $extension;
                $destino = __DIR__ . '/../../../public/uploads/justificaciones/' . $nombre;

                if (move_uploaded_file($archivo['tmp_name'], $destino)) {
                    $archivoUrl = '/uploads/justificaciones/' . $nombre;
                } else {
                    $errores['archivo'] = 'Error al subir el archivo.';
                }
            }
        }

        if (!empty($errores)) {
            Session::set('errores', $errores);
            Session::set('old', ['asistencia_id' => $asistenciaId, 'motivo' => $motivo]);
            header('Location: /asistencia-api/admin/justificaciones/crear');
            exit;
        }

        try {
            Justificacion::crear([
                'asistencia_id' => $asistenciaId,
                'motivo'        => $motivo,
                'archivo_url'   => $archivoUrl
            ]);

            Usuario::registrarLog(
                (int)$user['id'],
                'crear_justificacion',
                'Creó justificación para asistencia #' . $asistenciaId
            );

            Session::set('flash_exito', 'Justificación registrada correctamente.');
            header('Location: /asistencia-api/admin/justificaciones');
            exit;
        } catch (\Exception $e) {
            Session::set('flash_error', 'Error al guardar: ' . $e->getMessage());
            header('Location: /asistencia-api/admin/justificaciones/crear');
            exit;
        }
    }
}