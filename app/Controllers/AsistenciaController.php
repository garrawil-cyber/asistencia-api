<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Asistencia;
use App\Models\QrToken;
use App\Middleware\AuthMiddleware;

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

        // 1) Validar QR (solo por vigencia temporal — NO se marca como usado)
        $qr = QrToken::validar($qrToken);
        if (!$qr) Response::error('QR inválido o expirado', 410);

        // 2) ¿Ya marcó entrada hoy?
        $asistenciaHoy = Asistencia::buscarHoy($docenteId);

        if ($asistenciaHoy === null) {
            // → ENTRADA
            $resultado = $this->procesarEntrada($docenteId);
        } elseif ($asistenciaHoy['hora_salida'] === null) {
            // → SALIDA
            Asistencia::registrarSalida($asistenciaHoy['id'], date('Y-m-d H:i:s'));
            $resultado = [
                'tipo'    => 'salida',
                'hora'    => date('H:i:s'),
                'mensaje' => 'Salida registrada correctamente'
            ];
        } else {
            Response::error('Ya completaste tu marcación de hoy', 409);
        }

        // FIX #2: NO marcar el QR como usado. Varios docentes pueden usar el
        // mismo QR mientras esté vigente (60 seg por defecto).
        // La única defensa es el UNIQUE(docente_id, fecha) en asistencias.

        Response::success($resultado, 'Marcación exitosa');
    }

    /**
     * Calcula estado (presente/tarde) y registra entrada.
     */
    private function procesarEntrada(int $docenteId): array
    {
        $db  = Database::getConnection();
        $cfg = $db->query("SELECT clave, valor FROM configuracion")
                  ->fetchAll(\PDO::FETCH_KEY_PAIR);

        $horaOficial = $cfg['hora_entrada_oficial'] ?? '07:30:00';
        $tolerancia  = (int)($cfg['tolerancia_minutos'] ?? 5);

        // FIX #3: usar zona horaria explícita
        $tz    = new \DateTimeZone('America/Lima');
        $ahora = new \DateTime('now', $tz);
        $limite = (new \DateTime($horaOficial, $tz))->modify("+{$tolerancia} minutes");

        if ($ahora <= $limite) {
            $estado  = 'presente';
            $minutos = 0;
        } else {
            $estado  = 'tarde';
            $minutos = (int)(($ahora->getTimestamp() - (new \DateTime($horaOficial, $tz))->getTimestamp()) / 60);
        }

        $id = Asistencia::registrarEntrada(
            $docenteId,
            $ahora->format('Y-m-d H:i:s'),
            $estado,
            $minutos
        );

        return [
            'tipo'             => 'entrada',
            'asistencia_id'    => $id,
            'hora'             => $ahora->format('H:i:s'),
            'estado'           => $estado,
            'minutos_tardanza' => $minutos,
            'mensaje'          => $estado === 'presente'
                ? '¡Bienvenido! Entrada registrada a tiempo'
                : "Entrada registrada con {$minutos} min de tardanza"
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
}