<?php
namespace App\Core;

class TurnoHelper
{

    /**
     * Detecta la acción que corresponde según la hora actual
     * y el estado del día del docente.
     *
     * @param array      $turno      Datos del turno (con ventanas)
     * @param array|null $estadoDia  Estado del día del docente (null si no ha marcado)
     * @return array
     */
    public static function detectarAccion(array $turno, ?array $estadoDia): array
    {
        $tz = new \DateTimeZone('America/Lima');
        $ahora = new \DateTime('now', $tz);
        $horaActual = $ahora->format('H:i:s');

        // 1. Determinar qué acción le toca según el estado del día
        if ($estadoDia === null || empty($estadoDia['hora_entrada'])) {
            // No marcó nada → ENTRADA
            return self::validarEntrada($turno, $horaActual);
        }

        if (!empty($estadoDia['hora_entrada']) && empty($estadoDia['hora_salida'])) {
            // Marcó entrada pero no salida → SALIDA
            return self::validarSalida($turno, $horaActual);
        }

        // Ya marcó entrada Y salida
        return [
            'error' => 'Ya completaste tu marcación de hoy'
        ];
    }

    /**
     * Valida si la hora actual está dentro de la ventana de entrada.
     */
    private static function validarEntrada(array $turno, string $horaActual): array
    {
        $inicio = $turno['ventana_entrada_inicio'];
        $fin    = $turno['ventana_entrada_fin'];

        if ($horaActual < $inicio || $horaActual > $fin) {
            return [
                'error' => sprintf(
                    'Fuera del horario de marcación de entrada (%s a %s)',
                    substr($inicio, 0, 5),
                    substr($fin, 0, 5)
                )
            ];
        }

        return [
            'accion' => 'entrada',
            'bloque' => 'entrada',
            'error'  => null
        ];
    }

    /**
     * Valida si la hora actual está dentro de la ventana de salida.
     */
    private static function validarSalida(array $turno, string $horaActual): array
    {
        $inicio = $turno['ventana_salida_inicio'];
        $fin    = $turno['ventana_salida_fin'];

        if ($horaActual < $inicio || $horaActual > $fin) {
            return [
                'error' => sprintf(
                    'Fuera del horario de marcación de salida (%s a %s)',
                    substr($inicio, 0, 5),
                    substr($fin, 0, 5)
                )
            ];
        }

        return [
            'accion' => 'salida',
            'bloque' => 'salida',
            'error'  => null
        ];
    }

    /**
     * Calcula el estado de la ENTRADA según la hora de marcación.
     * Devuelve: 'presente' o 'tarde' + minutos de tardanza.
     */
    public static function calcularEstadoEntrada(array $turno, \DateTime $horaMarcacion): array
    {
        $tz = new \DateTimeZone('America/Lima');

        $horaOficial = $turno['hora_entrada'];
        $tolerancia  = (int)$turno['tolerancia_minutos'];

        $limite = (new \DateTime($horaOficial, $tz))
                    ->modify("+{$tolerancia} minutes");

        if ($horaMarcacion <= $limite) {
            return [
                'estado'  => 'presente',
                'minutos' => 0
            ];
        }

        $horaOficialDt = new \DateTime($horaOficial, $tz);
        $minutos = (int)(($horaMarcacion->getTimestamp() - $horaOficialDt->getTimestamp()) / 60);

        return [
            'estado'  => 'tarde',
            'minutos' => $minutos
        ];
    }

    /**
     * Calcula el estado de la SALIDA según la hora de marcación.
     * Devuelve: 'salida_ok', 'salida_anticipada' o 'salida_tardia'.
     */
    public static function calcularEstadoSalida(array $turno, \DateTime $horaMarcacion): string
    {
        $tz = new \DateTimeZone('America/Lima');

        $horaOficial = $turno['hora_salida'];
        $horaOficialDt = new \DateTime($horaOficial, $tz);

        // Si sale ANTES de la hora oficial → anticipada
        if ($horaMarcacion < $horaOficialDt) {
            return 'salida_anticipada';
        }

        // Si sale dentro de los 15 minutos después → OK
        $limiteOk = (clone $horaOficialDt)->modify("+15 minutes");
        if ($horaMarcacion <= $limiteOk) {
            return 'salida_ok';
        }

        // Si sale más de 15 minutos después → tardía
        return 'salida_tardia';
    }

    /**
     * Obtiene el turno del docente por su ID.
     */
    public static function obtenerTurnoDocente(int $docenteId): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT t.*
            FROM docentes d
            JOIN turnos t ON t.id = d.turno_id
            WHERE d.id = ? AND t.activo = 1
            LIMIT 1
        ");
        $stmt->execute([$docenteId]);
        return $stmt->fetch() ?: null;
    }
}