<?php
use App\Core\DateHelper;

$titulo = 'Justificaciones';
$paginaActiva = 'justificaciones';
require __DIR__ . '/../layouts/header.php';

// ¿Hay filtros activos?
$hayFiltros = ($estado !== '')
           || ($docenteId > 0)
           || ($desde !== '')
           || ($hasta !== '');
?>

<!-- Flash messages -->
<?php if (!empty($flashExito)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarToast('<?= htmlspecialchars($flashExito, ENT_QUOTES) ?>', 'success');
        });
    </script>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarToast('<?= htmlspecialchars($flashError, ENT_QUOTES) ?>', 'error');
        });
    </script>
<?php endif; ?>

<!-- ============ KPIs ============ -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="kpi-card kpi-warning">
            <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
            <div class="kpi-label">Pendientes</div>
            <div class="kpi-value"><?= $resumen['pendiente'] ?></div>
            <div class="kpi-trend">Requieren revisión</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card kpi-success">
            <div class="kpi-icon"><i class="bi bi-check-circle"></i></div>
            <div class="kpi-label">Aprobadas</div>
            <div class="kpi-value"><?= $resumen['aprobado'] ?></div>
            <div class="kpi-trend">Ausencias justificadas</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card kpi-info">
            <div class="kpi-icon"><i class="bi bi-x-circle"></i></div>
            <div class="kpi-label">Rechazadas</div>
            <div class="kpi-value"><?= $resumen['rechazado'] ?></div>
            <div class="kpi-trend">Sin sustento válido</div>
        </div>
    </div>
</div>

<!-- ============ PANEL PRINCIPAL ============ -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">
            <i class="bi bi-file-earmark-text text-primary"></i>
            Justificaciones
            <span class="badge bg-primary ms-2"><?= $total ?></span>
        </h2>
        <a href="/asistencia-api/admin/justificaciones/crear" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nueva Justificación
        </a>
    </div>

    <!-- ============ FILTROS COLAPSABLES ============ -->
    <button class="filters-toggle" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#filtrosJustif"
            aria-expanded="<?= $hayFiltros ? 'true' : 'false' ?>">
        <i class="bi bi-funnel-fill"></i>
        <span>Filtros de búsqueda</span>
        <?php if ($hayFiltros): ?>
            <span class="badge bg-primary ms-auto">Activos</span>
        <?php endif; ?>
        <i class="bi bi-chevron-down chevron"></i>
    </button>

    <div class="collapse <?= $hayFiltros ? 'show' : '' ?>" id="filtrosJustif">
        <form method="GET" action="/asistencia-api/admin/justificaciones" class="filters-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-flag"></i> Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" <?= $estado === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="aprobado"  <?= $estado === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                        <option value="rechazado" <?= $estado === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
                    </select>
                </div>

                <div class="col-md-4">
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

                <div class="col-md-2">
                    <label class="form-label"><i class="bi bi-calendar-event"></i> Desde</label>
                    <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($desde) ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label"><i class="bi bi-calendar-event"></i> Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($hasta) ?>">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100" title="Filtrar">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                <?php if ($hayFiltros): ?>
                <div class="col-12">
                    <a href="/asistencia-api/admin/justificaciones" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Limpiar filtros
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- ============ TABLA ============ -->
    <div class="table-responsive">
        <?php if (empty($justificaciones)): ?>
            <div class="empty-state">
                <i class="bi bi-file-earmark-x"></i>
                <h4>Sin justificaciones</h4>
                <p>No hay justificaciones con los filtros aplicados.</p>
                <a href="/asistencia-api/admin/justificaciones/crear" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Registrar la primera
                </a>
            </div>
        <?php else: ?>
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Docente</th>
                        <th style="width: 130px;">Fecha falta</th>
                        <th>Motivo</th>
                        <th style="width: 130px;">Adjunto</th>
                        <th style="width: 120px;">Solicitada</th>
                        <th class="text-center" style="width: 110px;">Estado</th>
                        <th class="text-end" style="width: 130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $offset = ($pagina - 1) * $porPag;
                    foreach ($justificaciones as $i => $j):
                        $badges = [
                            'pendiente' => 'bg-warning-subtle text-warning',
                            'aprobado'  => 'bg-success-subtle text-success',
                            'rechazado' => 'bg-danger-subtle text-danger'
                        ];
                        $badgeClass = $badges[$j['estado']] ?? 'bg-secondary-subtle text-secondary';
                    ?>
                        <tr>
                            <td class="text-muted small"><?= $offset + $i + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle avatar-sm">
                                        <?= strtoupper(substr($j['nombres'], 0, 1) . substr($j['apellidos'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">
                                            <?= htmlspecialchars($j['nombres'] . ' ' . $j['apellidos']) ?>
                                        </div>
                                        <div class="text-muted small">
                                            <code><?= htmlspecialchars($j['dni']) ?></code>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold small">
                                    <?= date('d/m/Y', strtotime($j['asistencia_fecha'])) ?>
                                </div>
                                <small class="text-muted">
                                    <?= DateHelper::diaSemana($j['asistencia_fecha']) ?>
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= htmlspecialchars(mb_substr($j['motivo'], 0, 80)) ?>
                                    <?= mb_strlen($j['motivo']) > 80 ? '…' : '' ?>
                                </small>
                            </td>
                            <td>
                                <?php if (!empty($j['archivo_url'])): ?>
                                    <a href="/asistencia-api/public<?= htmlspecialchars($j['archivo_url']) ?>"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-paperclip"></i> Ver
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y', strtotime($j['fecha_solicitud'])) ?><br>
                                    <i class="bi bi-clock"></i> <?= date('H:i', strtotime($j['fecha_solicitud'])) ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($j['estado']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="/asistencia-api/admin/justificaciones/ver/<?= $j['id'] ?>"
                                   class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Paginación -->
    <?= \App\Core\Paginator::render(
        $pagina,
        $paginas,
        $total,
        count($justificaciones),
        $porPag,
        '/asistencia-api/admin/justificaciones',
        [
            'estado' => $estado,
            'docente_id' => $docenteId,
            'desde' => $desde,
            'hasta' => $hasta
        ]
    ) ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>