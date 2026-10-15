<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Asistencia;
use App\Models\QrToken;
use App\Middleware\AuthMiddleware;
use App\Core\TurnoHelper;

class AsistenciaController
{
    /**
     * POST /api/v1/asistencia/marcar
     * Docente escanea QR → registra entrada o salida según el estado del día.
     */
        public function marcar(): void
    {
        $user      = AuthMiddleware::requireRol(['docente']);
        $docenteId = (int)$user['docente_id'];

        if (!$docenteId) Response::error('Usuario sin perfil de docente', 403);

        $qrToken = Request::input('qr_token');
        if (!$qrToken) Response::error('Token QR requerido', 422);

        // 1. Validar QR
        $qr = QrToken::validar($qrToken);
        if (!$qr) Response::error('QR inválido o expirado', 410);

        // 2. Obtener el turno del docente
        $turno = TurnoHelper::obtenerTurnoDocente($docenteId);
        if (!$turno) {
            Response::error('No tienes un turno asignado. Contacta al administrador.', 403);
        }

        // 3. Buscar el estado del día
        $asistenciaHoy = Asistencia::buscarHoy($docenteId);

        // 4. Detectar qué acción corresponde
        $detalle = TurnoHelper::detectarAccion($turno, $asistenciaHoy);

        if (!empty($detalle['error'])) {
            Response::error($detalle['error'], 409);
        }

        // 5. Ejecutar la acción
        $tz = new \DateTimeZone('America/Lima');
        $ahora = new \DateTime('now', $tz);

        if ($detalle['accion'] === 'entrada') {
            $resultado = $this->procesarEntrada($docenteId, $turno, $ahora);
        } else {
            // SALIDA
            $estadoSalida = TurnoHelper::calcularEstadoSalida($turno, $ahora);

            Asistencia::registrarSalida(
                $asistenciaHoy['id'],
                $ahora->format('Y-m-d H:i:s'),
                $estadoSalida
            );

            $mensajeSalida = match($estadoSalida) {
                'salida_ok'         => 'Salida registrada correctamente',
                'salida_anticipada' => 'Salida anticipada registrada',
                'salida_tardia'     => 'Salida tardía registrada',
                default             => 'Salida registrada'
            };

            $resultado = [
                'tipo'          => 'salida',
                'hora'          => $ahora->format('H:i:s'),
                'estado_salida' => $estadoSalida,
                'mensaje'       => $mensajeSalida
            ];
        }

        Response::success($resultado, 'Marcación exitosa');
    }

    /**
     * Calcula el estado y registra la ENTRADA.
     */
    private function procesarEntrada(
        int $docenteId,
        array $turno,
        \DateTime $ahora
    ): array {
        // Calcular estado (presente o tarde)
        $calc = TurnoHelper::calcularEstadoEntrada($turno, $ahora);

        // Registrar
        $id = Asistencia::registrarEntrada(
            $docenteId,
            (int)$turno['id'],
            'entrada',
            $ahora->format('Y-m-d H:i:s'),
            $calc['estado'],
            $calc['minutos']
        );

        return [
            'tipo'             => 'entrada',
            'asistencia_id'    => $id,
            'hora'             => $ahora->format('H:i:s'),
            'estado'           => $calc['estado'],
            'minutos_tardanza' => $calc['minutos'],
            'turno'            => $turno['codigo'],
            'mensaje'          => $calc['estado'] === 'presente'
                ? '¡Bienvenido! Entrada registrada a tiempo'
                : "Entrada registrada con {$calc['minutos']} min de tardanza"
        ];
    }

     /**
     * GET /api/v1/asistencia/historial
     * Docente: ve su propio historial.
     * Admin/coordinador: DEBE pasar ?docente_id=X
     */
    public function historial(): void
    {
        $user = AuthMiddleware::requireRol(['docente', 'admin', 'coordinador']);

        // FIX #4: admin/coordinador puede consultar a cualquier docente
        if (in_array($user['rol'], ['admin', 'coordinador'], true)) {
            $docenteId = (int)Request::input('docente_id', 0);
            if (!$docenteId) Response::error('Debes especificar docente_id', 422);
        } else {
            $docenteId = (int)$user['docente_id'];
            if (!$docenteId) Response::error('Usuario sin perfil de docente', 403);
        }

        $desde = Request::input('desde', date('Y-m-01'));
        $hasta = Request::input('hasta', date('Y-m-d'));

        $data = Asistencia::historial($docenteId, $desde, $hasta);
        Response::success($data, 'Historial obtenido');
    }

    /**
     * GET /api/v1/asistencia/hoy
     * Solo para docentes: su estado de hoy.
     */
    public function hoy(): void
    {
        $user      = AuthMiddleware::requireRol(['docente']);
        $docenteId = (int)$user['docente_id'];

        if (!$docenteId) Response::error('Usuario sin perfil de docente', 403);

        $data = Asistencia::buscarHoy($docenteId);
        Response::success($data, $data ? 'Asistencia de hoy' : 'Aún no has marcado hoy');
    }

    /**
     * GET /api/v1/asistencia/estado-hoy
     * Devuelve el estado del día para el docente logueado.
     */
    public function estadoHoy(): void
    {
        $user      = AuthMiddleware::requireRol(['docente']);
        $docenteId = (int)$user['docente_id'];

        if (!$docenteId) Response::error('Usuario sin perfil de docente', 403);

        // Obtener el turno del docente
        $turno = TurnoHelper::obtenerTurnoDocente($docenteId);
        if (!$turno) {
            Response::success([
                'estado' => 'sin_turno',
                'turno'  => null,
                'entrada' => null,
                'salida'  => null,
                'mensaje' => 'No tienes un turno asignado'
            ], 'Estado de hoy');
        }

        // Buscar el estado de hoy
        $asistenciaHoy = Asistencia::buscarHoy($docenteId);

        if (!$asistenciaHoy) {
            Response::success([
                'estado'            => 'sin_marcar',
                'turno'             => $turno['codigo'],
                'turno_nombre'      => $turno['nombre'],
                'hora_entrada'      => null,
                'hora_salida'       => null,
                'minutos_tardanza'  => 0,
                'estado_asistencia' => null,
                'estado_salida'     => null
            ], 'Estado de hoy');
        }

        $estadoGeneral = $asistenciaHoy['hora_salida'] 
            ? 'completo' 
            : 'entrada_marcada';

        Response::success([
            'estado'            => $estadoGeneral,
            'turno'             => $turno['codigo'],
            'turno_nombre'      => $turno['nombre'],
            'hora_entrada'      => $asistenciaHoy['hora_entrada'],
            'hora_salida'       => $asistenciaHoy['hora_salida'],
            'minutos_tardanza'  => (int)$asistenciaHoy['minutos_tardanza'],
            'estado_asistencia' => $asistenciaHoy['estado'],
            'estado_salida'     => $asistenciaHoy['estado_salida']
        ], 'Estado de hoy');
    }
    
}