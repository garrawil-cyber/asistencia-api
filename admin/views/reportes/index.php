<?php
use App\Core\DateHelper;

$titulo = 'Reportes de Asistencia';
$paginaActiva = 'reportes';
require __DIR__ . '/../layouts/header.php';

// ¿Hay filtros activos?
$hayFiltrosActivos = ($desde !== date('Y-m-01'))
                  || ($hasta !== date('Y-m-d'))
                  || ($docenteId > 0)
                  || ($estado !== '')
                  || (isset($turnoId) && $turnoId > 0);
?>

<!-- ============ FILTROS COLAPSABLES ============ -->
<div class="panel mb-4">
    <div class="panel-header">
        <h2 class="panel-title">
            <i class="bi bi-funnel-fill text-primary"></i>
            Reportes de Asistencia
        </h2>
        <div class="d-flex gap-2">
            <a href="/asistencia-api/admin/reportes/exportar?desde=<?= $desde ?>&hasta=<?= $hasta ?>&docente_id=<?= $docenteId ?>&estado=<?= $estado ?>&turno_id=<?= $turnoId ?? 0 ?>"
               class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="/asistencia-api/admin/reportes/exportar-pdf?desde=<?= $desde ?>&hasta=<?= $hasta ?>&docente_id=<?= $docenteId ?>&estado=<?= $estado ?>&turno_id=<?= $turnoId ?? 0 ?>"
               class="btn btn-danger btn-sm" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
    </div>

    <button class="filters-toggle" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#filtrosReporte"
            aria-expanded="<?= $hayFiltrosActivos ? 'true' : 'false' ?>">
        <i class="bi bi-sliders"></i>
        <span>Filtros del reporte</span>
        <?php if ($hayFiltrosActivos): ?>
            <span class="badge bg-primary ms-auto">Activos</span>
        <?php endif; ?>
        <i class="bi bi-chevron-down chevron"></i>
    </button>

    <div class="collapse <?= $hayFiltrosActivos ? 'show' : '' ?>" id="filtrosReporte">
        <form method="GET" action="/asistencia-api/admin/reportes" class="filters-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label"><i class="bi bi-calendar-event"></i> Desde</label>
                    <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($desde) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="bi bi-calendar-event"></i> Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($hasta) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-person"></i> Docente</label>
                    <select name="docente_id" class="form-select">
                        <option value="0">Todos los docentes</option>
                        <?php foreach ($docentes as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= $docenteId === (int)$d['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['apellidos'] . ', ' . $d['nombres']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- NUEVO FILTRO: TURNO -->
                <div class="col-md-2">
                    <label class="form-label"><i class="bi bi-clock"></i> Turno</label>
                    <select name="turno_id" class="form-select">
                        <option value="0">Todos</option>
                        <?php foreach ($turnos as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= (isset($turnoId) && $turnoId == $t['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-flag"></i> Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="presente"     <?= $estado === 'presente' ? 'selected' : '' ?>>Presente</option>
                        <option value="tarde"        <?= $estado === 'tarde' ? 'selected' : '' ?>>Tarde</option>
                        <option value="ausente"      <?= $estado === 'ausente' ? 'selected' : '' ?>>Ausente</option>
                        <option value="justificado"  <?= $estado === 'justificado' ? 'selected' : '' ?>>Justificado</option>
                        <option value="falta_salida" <?= $estado === 'falta_salida' ? 'selected' : '' ?>>Falta salida</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i> Filtrar</button>
                    <a href="/asistencia-api/admin/reportes" class="btn btn-outline-secondary" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                </div>
            </div>
            <div class="filters-info mt-3">
                <i class="bi bi-info-circle"></i>
                Mostrando asistencias del <strong><?= date('d/m/Y', strtotime($desde)) ?></strong>
                al <strong><?= date('d/m/Y', strtotime($hasta)) ?></strong>
                <?= $docenteId > 0 ? '· filtrado por docente' : '' ?>
                <?= $estado !== '' ? '· estado: ' . ucfirst($estado) : '' ?>
            </div>
        </form>
    </div>
</div>

<!-- ============ RESUMEN KPI ============ -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="kpi-card kpi-primary">
            <div class="kpi-icon"><i class="bi bi-calendar-check"></i></div>
            <div class="kpi-label">Total registros</div>
            <div class="kpi-value"><?= $totalRegistros ?></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="kpi-card kpi-success">
            <div class="kpi-icon"><i class="bi bi-check2-circle"></i></div>
            <div class="kpi-label">Presentes</div>
            <div class="kpi-value"><?= $resumen['presente'] ?></div>
            <div class="kpi-trend"><i class="bi bi-arrow-up"></i> <?= $porcentajePresente ?>% del total</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="kpi-card kpi-warning">
            <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
            <div class="kpi-label">Tardanzas</div>
            <div class="kpi-value"><?= $resumen['tarde'] ?></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="kpi-card kpi-info">
            <div class="kpi-icon"><i class="bi bi-exclamation-circle"></i></div>
            <div class="kpi-label">Faltas de salida</div>
            <div class="kpi-value"><?= $resumen['falta_salida'] ?></div>
        </div>
    </div>
</div>

<!-- ============ TABLA ============ -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">
            <i class="bi bi-table text-primary"></i>
            Detalle de asistencias
            <span class="badge bg-primary ms-2"><?= $total ?></span>
        </h2>
    </div>

    <div class="table-responsive">
        <?php if (empty($asistencias)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>Sin registros</h4>
                <p>No hay asistencias en el rango seleccionado.</p>
            </div>
        <?php else: ?>
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Docente</th>
                        <th class="text-center">Turno</th> <!-- NUEVO -->
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Tardanza</th>
                        <th>Método</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asistencias as $a): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= date('d/m/Y', strtotime($a['fecha'])) ?></div>
                                <small class="text-muted"><?= DateHelper::diaSemana($a['fecha']) ?></small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle">
                                        <?= strtoupper(substr($a['nombres'], 0, 1) . substr($a['apellidos'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($a['nombres'] . ' ' . $a['apellidos']) ?></div>
                                        <div class="text-muted small"><code><?= htmlspecialchars($a['dni']) ?></code></div>
                                    </div>
                                </div>
                            </td>
                            <!-- NUEVA CELDA: TURNO -->
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">
                                    <?= htmlspecialchars($a['turno_nombre'] ?? '—') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($a['hora_entrada']): ?>
                                    <i class="bi bi-box-arrow-in-right text-success"></i> <?= date('H:i', strtotime($a['hora_entrada'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($a['hora_salida']): ?>
                                    <i class="bi bi-box-arrow-right text-primary"></i> <?= date('H:i', strtotime($a['hora_salida'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($a['minutos_tardanza'] > 0): ?>
                                    <span class="text-warning fw-semibold"><i class="bi bi-clock"></i> <?= $a['minutos_tardanza'] ?> min</span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><i class="bi bi-qr-code"></i> <?= strtoupper($a['metodo_marcacion']) ?></span>
                            </td>
                            <td class="text-center">
                                <?php
                                $badges = [
                                    'presente'     => 'bg-success-subtle text-success',
                                    'tarde'        => 'bg-warning-subtle text-warning',
                                    'ausente'      => 'bg-danger-subtle text-danger',
                                    'justificado'  => 'bg-info-subtle text-info',
                                    'falta_salida' => 'bg-secondary-subtle text-secondary'
                                ];
                                $cssClass = $badges[$a['estado']] ?? 'bg-secondary-subtle text-secondary';
                                ?>
                                <span class="badge <?= $cssClass ?>">
                                    <?= ucfirst(str_replace('_', ' ', $a['estado'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Paginación -->
    <?php if ($paginas > 1): ?>
        <div class="panel-footer">
            <div class="pagination-info">
                Mostrando <?= count($asistencias) ?> de <?= $total ?> registros
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php
                    $qs = http_build_query([
                        'desde' => $desde, 'hasta' => $hasta,
                        'docente_id' => $docenteId, 'estado' => $estado,
                        'turno_id' => $turnoId ?? 0 // NUEVO
                    ]);
                    ?>
                    <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina - 1 ?>&<?= $qs ?>"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <?php for ($p = max(1, $pagina - 2); $p <= min($paginas, $pagina + 2); $p++): ?>
                        <li class="page-item <?= $p === $pagina ? 'active' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $p ?>&<?= $qs ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $pagina >= $paginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina + 1 ?>&<?= $qs ?>"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>