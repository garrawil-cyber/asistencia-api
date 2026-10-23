<?php
namespace App\Admin\Controllers;

use App\Admin\Middleware\SessionMiddleware;
use App\Core\Request;
use App\Core\Session;
use App\Core\DateHelper;
use App\Core\Database;
use App\Models\Asistencia;
use App\Models\Docente;
use App\Models\Usuario;

class ReporteController
{
    public function index(): void
    {
        $user = SessionMiddleware::check();

        $desde     = Request::input('desde', date('Y-m-01'));
        $hasta     = Request::input('hasta', date('Y-m-d'));
        $docenteId = (int)Request::input('docente_id', 0);
        $turnoId   = (int)Request::input('turno_id', 0); // NUEVO
        $estado    = Request::input('estado', '');
        $pagina    = max(1, (int)Request::input('pagina', 1));
        $porPag    = 20;
        $offset    = ($pagina - 1) * $porPag;

        if (!$this->esFechaValida($desde)) $desde = date('Y-m-01');
        if (!$this->esFechaValida($hasta)) $hasta = date('Y-m-d');
        if ($desde > $hasta) [$desde, $hasta] = [$hasta, $desde];

        $asistencias = Asistencia::listarConFiltros($desde, $hasta, $docenteId, $estado, $porPag, $offset, $turnoId);
        $total       = Asistencia::contarConFiltros($desde, $hasta, $docenteId, $estado, $turnoId);
        $paginas     = (int)ceil($total / $porPag);
        $resumen     = Asistencia::resumenPorEstado($desde, $hasta, $docenteId, $turnoId);
        $docentes    = Docente::listar(500, 0, '', 'activos');

        // Obtener turnos para el select
        $db = Database::getConnection();
        $turnos = $db->query("SELECT id, nombre FROM turnos WHERE activo = 1")->fetchAll();

        $totalRegistros = array_sum($resumen);
        $porcentajePresente = $totalRegistros > 0 ? round(($resumen['presente'] / $totalRegistros) * 100, 1) : 0;

        $datos = [
            'user' => $user, 'desde' => $desde, 'hasta' => $hasta,
            'docenteId' => $docenteId, 'turnoId' => $turnoId, 'estado' => $estado,
            'asistencias' => $asistencias, 'total' => $total, 'pagina' => $pagina,
            'paginas' => $paginas, 'resumen' => $resumen, 'docentes' => $docentes,
            'turnos' => $turnos,
            'totalRegistros' => $totalRegistros, 'porcentajePresente' => $porcentajePresente,
            'paginaActiva' => 'reportes'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/reportes/index.php';
    }

    public function exportar(): void
    {
        SessionMiddleware::check();

        $desde     = Request::input('desde', date('Y-m-01'));
        $hasta     = Request::input('hasta', date('Y-m-d'));
        $docenteId = (int)Request::input('docente_id', 0);
        $turnoId   = (int)Request::input('turno_id', 0);
        $estado    = Request::input('estado', '');

        if (!$this->esFechaValida($desde)) $desde = date('Y-m-01');
        if (!$this->esFechaValida($hasta)) $hasta = date('Y-m-d');
        
        Usuario::registrarLog(
            (int)Session::get('usuario_id'),
            'exportar',
            'Exportó reporte del ' . $desde . ' al ' . $hasta
        );

        $datos   = Asistencia::exportarConFiltros($desde, $hasta, $docenteId, $estado, $turnoId);
        $resumen = Asistencia::resumenPorEstado($desde, $hasta, $docenteId, $turnoId);

        $nombreDocente = 'Todos los docentes';
        if ($docenteId > 0) {
            $doc = Docente::buscarPorId($docenteId);
            if ($doc) $nombreDocente = $doc['nombres'] . ' ' . $doc['apellidos'];
        }

        $nombre = 'reporte_asistencias_' . $desde . '_al_' . $hasta . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF";
        ?>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        h1 { color: #1e3c72; font-size: 20pt; margin: 0 0 4px 0; }
        h2 { color: #6b7280; font-size: 11pt; font-weight: normal; margin: 0 0 20px 0; }
        .info-box { background: #e8eef9; padding: 10px 14px; border-left: 4px solid #2a5298; margin-bottom: 20px; font-size: 10pt; }
        .resumen { width: auto; margin-bottom: 20px; border-collapse: collapse; }
        .resumen td { background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 16px; text-align: center; font-size: 10pt; }
        .resumen .num { font-size: 16pt; font-weight: bold; color: #2a5298; display: block; }
        .resumen .num-verde { color: #065f46; }
        .resumen .num-amarillo { color: #92400e; }
        .resumen .num-rojo { color: #991b1b; }
        .resumen .num-gris { color: #374151; }

        table.datos { border-collapse: collapse; width: 100%; margin-top: 10px; }
        table.datos th { background: #2a5298; color: white; padding: 10px 8px; text-align: left; font-size: 10pt; text-transform: uppercase; border: 1px solid #1e3c72; }
        table.datos td { padding: 8px; border: 1px solid #d1d5db; font-size: 10pt; }
        table.datos tr:nth-child(even) td { background: #f9fafb; }

        .estado-presente { background: #d1fae5; color: #065f46; font-weight: bold; text-align: center; }
        .estado-tarde { background: #fef3c7; color: #92400e; font-weight: bold; text-align: center; }
        .estado-ausente { background: #fee2e2; color: #991b1b; font-weight: bold; text-align: center; }
        .estado-justificado { background: #dbeafe; color: #1e40af; font-weight: bold; text-align: center; }
        .estado-falta_salida { background: #e5e7eb; color: #374151; font-weight: bold; text-align: center; }

        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>

    <h1>📋 Reporte de Asistencias</h1>
    <h2>Sistema de Control de Asistencia Docente</h2>

    <div class="info-box">
        <strong>Período:</strong> <?= date('d/m/Y', strtotime($desde)) ?> al <?= date('d/m/Y', strtotime($hasta)) ?>
        &nbsp;&nbsp;|&nbsp;&nbsp; <strong>Docente:</strong> <?= htmlspecialchars($nombreDocente) ?>
        &nbsp;&nbsp;|&nbsp;&nbsp; <strong>Generado:</strong> <?= date('d/m/Y H:i') ?>
    </div>

    <table class="resumen">
        <tr>
            <td><strong>Total</strong><span class="num"><?= array_sum($resumen) ?></span></td>
            <td><strong>Presentes</strong><span class="num num-verde"><?= $resumen['presente'] ?></span></td>
            <td><strong>Tardanzas</strong><span class="num num-amarillo"><?= $resumen['tarde'] ?></span></td>
            <td><strong>Ausentes</strong><span class="num num-rojo"><?= $resumen['ausente'] ?></span></td>
            <td><strong>Faltas salida</strong><span class="num num-gris"><?= $resumen['falta_salida'] ?></span></td>
        </tr>
    </table>

    <table class="datos">
        <thead>
            <tr>
                <th style="width: 90px;">Fecha</th>
                <th style="width: 90px;">Día</th>
                <th>Docente</th>
                <th style="width: 90px;">Turno</th> <!-- NUEVO -->
                <th style="width: 80px;">DNI</th>
                <th style="width: 70px;">Entrada</th>
                <th style="width: 70px;">Salida</th>
                <th style="width: 80px;">Tardanza</th>
                <th style="width: 90px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($datos)): ?>
                <tr><td colspan="9" style="text-align:center; padding: 20px; color: #6b7280;">Sin registros en el período seleccionado</td></tr>
            <?php else: ?>
                <?php foreach ($datos as $d): ?>
                    <tr>
                        <td class="center"><?= date('d/m/Y', strtotime($d['fecha'])) ?></td>
                        <td class="center"><?= DateHelper::diaSemana($d['fecha']) ?></td>
                        <td><?= htmlspecialchars($d['nombres'] . ' ' . $d['apellidos']) ?></td>
                        <td class="center"><?= htmlspecialchars($d['turno_nombre'] ?? '—') ?></td> <!-- NUEVO -->
                        <td class="center"><?= htmlspecialchars($d['dni']) ?></td>
                        <td class="center"><?= $d['hora_entrada'] ? date('H:i', strtotime($d['hora_entrada'])) : '—' ?></td>
                        <td class="center"><?= $d['hora_salida'] ? date('H:i', strtotime($d['hora_salida'])) : '—' ?></td>
                        <td class="right"><?= $d['minutos_tardanza'] > 0 ? $d['minutos_tardanza'] . ' min' : '—' ?></td>
                        <td class="estado-<?= $d['estado'] ?>"><?= strtoupper(str_replace('_', ' ', $d['estado'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
        <?php
        exit;
    }

    public function exportarPdf(): void
    {
        SessionMiddleware::check();

        $desde     = Request::input('desde', date('Y-m-01'));
        $hasta     = Request::input('hasta', date('Y-m-d'));
        $docenteId = (int)Request::input('docente_id', 0);
        $turnoId   = (int)Request::input('turno_id', 0);
        $estado    = Request::input('estado', '');

        if (!$this->esFechaValida($desde)) $desde = date('Y-m-01');
        if (!$this->esFechaValida($hasta)) $hasta = date('Y-m-d');

        Usuario::registrarLog(
            (int)Session::get('usuario_id'),
            'exportar',
            'Exportó reporte del ' . $desde . ' al ' . $hasta
        );

        $datos   = Asistencia::exportarConFiltros($desde, $hasta, $docenteId, $estado, $turnoId);
        $resumen = Asistencia::resumenPorEstado($desde, $hasta, $docenteId, $turnoId);

        $nombreDocente = 'Todos los docentes';
        if ($docenteId > 0) {
            $doc = Docente::buscarPorId($docenteId);
            if ($doc) $nombreDocente = $doc['nombres'] . ' ' . $doc['apellidos'];
        }

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <style>
                @page { margin: 18mm 15mm 22mm 15mm; }
                body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #1f2937; }
                h1 { color: #1e3c72; font-size: 20pt; margin: 0 0 4px; }
                .subtitulo { color: #6b7280; font-size: 10pt; margin-bottom: 16px; }
                .info-box { background: #e8eef9; border-left: 4px solid #2a5298; padding: 10px 14px; margin-bottom: 16px; font-size: 9pt; }
                .info-box strong { color: #1e3c72; }

                .resumen { width: 100%; margin-bottom: 16px; border-collapse: collapse; }
                .resumen td { background: #f9fafb; border: 1px solid #e5e7eb; padding: 8px 6px; text-align: center; font-size: 9pt; }
                .resumen .num { font-size: 14pt; font-weight: bold; color: #2a5298; display: block; margin-top: 2px; }
                .resumen .num-verde { color: #065f46; }
                .resumen .num-amarillo { color: #92400e; }
                .resumen .num-rojo { color: #991b1b; }
                .resumen .num-gris { color: #374151; }

                table.datos { width: 100%; border-collapse: collapse; }
                table.datos th { background: #2a5298; color: white; padding: 7px 5px; font-size: 7.5pt; text-transform: uppercase; border: 1px solid #1e3c72; text-align: left; }
                table.datos td { padding: 6px 5px; font-size: 7.5pt; border: 1px solid #e5e7eb; }
                table.datos tr:nth-child(even) td { background: #f9fafb; }

                .badge-presente { background: #d1fae5; color: #065f46; padding: 2px 5px; border-radius: 3px; font-weight: bold; font-size: 6.5pt; }
                .badge-tarde { background: #fef3c7; color: #92400e; padding: 2px 5px; border-radius: 3px; font-weight: bold; font-size: 6.5pt; }
                .badge-ausente { background: #fee2e2; color: #991b1b; padding: 2px 5px; border-radius: 3px; font-weight: bold; font-size: 6.5pt; }
                .badge-justificado { background: #dbeafe; color: #1e40af; padding: 2px 5px; border-radius: 3px; font-weight: bold; font-size: 6.5pt; }
                .badge-falta_salida { background: #e5e7eb; color: #374151; padding: 2px 5px; border-radius: 3px; font-weight: bold; font-size: 6.5pt; }

                .center { text-align: center; }
                .footer { position: fixed; bottom: -18mm; left: 0; right: 0; height: 15mm; text-align: center; font-size: 8pt; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 6px; }
                .pagina:after { content: counter(page); }
            </style>
        </head>
        <body>

            <h1>📋 Reporte de Asistencias</h1>
            <div class="subtitulo">Sistema de Control de Asistencia Docente</div>

            <div class="info-box">
                <strong>Período:</strong> <?= date('d/m/Y', strtotime($desde)) ?> al <?= date('d/m/Y', strtotime($hasta)) ?>
                &nbsp;&nbsp;|&nbsp;&nbsp; <strong>Docente:</strong> <?= htmlspecialchars($nombreDocente) ?>
                &nbsp;&nbsp;|&nbsp;&nbsp; <strong>Generado:</strong> <?= date('d/m/Y H:i') ?>
            </div>

            <table class="resumen">
                <tr>
                    <td><strong>Total</strong><span class="num"><?= array_sum($resumen) ?></span></td>
                    <td><strong>Presentes</strong><span class="num num-verde"><?= $resumen['presente'] ?></span></td>
                    <td><strong>Tardanzas</strong><span class="num num-amarillo"><?= $resumen['tarde'] ?></span></td>
                    <td><strong>Ausentes</strong><span class="num num-rojo"><?= $resumen['ausente'] ?></span></td>
                    <td><strong>Faltas salida</strong><span class="num num-gris"><?= $resumen['falta_salida'] ?></span></td>
                </tr>
            </table>

            <table class="datos">
                <thead>
                    <tr>
                        <th style="width: 52px;">Fecha</th>
                        <th>Docente</th>
                        <th style="width: 55px;">Turno</th> <!-- NUEVO -->
                        <th style="width: 52px;">DNI</th>
                        <th style="width: 42px;">Entrada</th>
                        <th style="width: 42px;">Salida</th>
                        <th style="width: 45px;">Tard.</th>
                        <th style="width: 65px;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($datos)): ?>
                        <tr><td colspan="8" style="text-align:center; padding: 20px; color: #6b7280;">Sin registros en el período seleccionado</td></tr>
                    <?php else: ?>
                        <?php foreach ($datos as $d): ?>
                            <tr>
                                <td class="center"><?= date('d/m/Y', strtotime($d['fecha'])) ?></td>
                                <td><?= htmlspecialchars($d['nombres'] . ' ' . $d['apellidos']) ?></td>
                                <td class="center"><?= htmlspecialchars($d['turno_nombre'] ?? '—') ?></td> <!-- NUEVO -->
                                <td class="center"><?= htmlspecialchars($d['dni']) ?></td>
                                <td class="center"><?= $d['hora_entrada'] ? date('H:i', strtotime($d['hora_entrada'])) : '—' ?></td>
                                <td class="center"><?= $d['hora_salida'] ? date('H:i', strtotime($d['hora_salida'])) : '—' ?></td>
                                <td class="center"><?= $d['minutos_tardanza'] > 0 ? $d['minutos_tardanza'] : '—' ?></td>
                                <td class="center"><span class="badge-<?= $d['estado'] ?>"><?= strtoupper(str_replace('_', ' ', $d['estado'])) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="footer">Panel Admin · Sistema de Asistencia Docente · <?= date('Y') ?> · Página <span class="pagina"></span></div>

        </body>
        </html>
        <?php
        $html = ob_get_clean();

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nombreArchivo = 'reporte_asistencias_' . $desde . '_al_' . $hasta . '.pdf';
        $dompdf->stream($nombreArchivo, ['Attachment' => true]);
        exit;
    }

    private function esFechaValida(string $fecha): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $fecha);
        return $d && $d->format('Y-m-d') === $fecha;
    }
}