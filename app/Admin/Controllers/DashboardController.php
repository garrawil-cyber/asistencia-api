<?php
namespace App\Admin\Controllers;

use App\Admin\Middleware\SessionMiddleware;
use App\Core\Database;

class DashboardController
{
    /**
     * Muestra el dashboard principal.
     * GET /admin/dashboard
     */
    public function index(): void
    {
        $user = SessionMiddleware::check();

        $db = Database::getConnection();

        // --- KPI principales ---
        $totalDocentes = (int)$db->query("SELECT COUNT(*) FROM docentes WHERE activo = 1")->fetchColumn();

        $asistenciasHoy = (int)$db->query("
            SELECT COUNT(*) FROM asistencias WHERE fecha = CURDATE()
        ")->fetchColumn();

        $presentesHoy = (int)$db->query("
            SELECT COUNT(*) FROM asistencias a
            JOIN estados_asistencia e ON e.id = a.estado_id
            WHERE a.fecha = CURDATE() AND e.codigo = 'presente'
        ")->fetchColumn();

        $tardanzasHoy = (int)$db->query("
            SELECT COUNT(*) FROM asistencias a
            JOIN estados_asistencia e ON e.id = a.estado_id
            WHERE a.fecha = CURDATE() AND e.codigo = 'tarde'
        ")->fetchColumn();

        // --- Gráfico: asistencias últimos 7 días ---
        $stmt = $db->query("
            SELECT
                DATE(fecha) AS dia,
                COUNT(*) AS total
            FROM asistencias
            WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(fecha)
            ORDER BY dia ASC
        ");
        $grafico7Dias = $stmt->fetchAll();

        // Rellenar días sin datos
        $diasCompletos = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $encontrado = null;
            foreach ($grafico7Dias as $g) {
                if ($g['dia'] === $fecha) { $encontrado = $g; break; }
            }
            $diasCompletos[] = [
                'fecha' => $fecha,
                'label' => date('D d', strtotime($fecha)),
                'total' => $encontrado ? (int)$encontrado['total'] : 0
            ];
        }

        // --- Actividad reciente (últimas 5 marcaciones) ---
        $stmt = $db->query("
            SELECT
                a.hora_entrada,
                a.hora_salida,
                a.minutos_tardanza,
                e.codigo AS estado,
                d.nombres,
                d.apellidos
            FROM asistencias a
            JOIN docentes d ON d.id = a.docente_id
            JOIN estados_asistencia e ON e.id = a.estado_id
            WHERE a.fecha = CURDATE()
            ORDER BY a.hora_entrada DESC
            LIMIT 5
        ");
        $actividadReciente = $stmt->fetchAll();

        // Datos para la vista
        $datos = [
            'user'              => $user,
            'totalDocentes'     => $totalDocentes,
            'asistenciasHoy'    => $asistenciasHoy,
            'presentesHoy'      => $presentesHoy,
            'tardanzasHoy'      => $tardanzasHoy,
            'grafico7Dias'      => $diasCompletos,
            'actividadReciente' => $actividadReciente,
            'paginaActiva'      => 'dashboard'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/dashboard/index.php';
    }
}