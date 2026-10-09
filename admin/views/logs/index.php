<?php
use App\Core\DateHelper;

$titulo = 'Logs de Actividad';
$paginaActiva = 'logs';
require __DIR__ . '/../layouts/header.php';

// ¿Hay filtros activos? (comparamos con los valores por defecto)
$hayFiltros = ($usuarioId > 0)
           || ($desde !== date('Y-m-d', strtotime('-7 days')))
           || ($hasta !== date('Y-m-d'));

// Diccionario de acciones con iconos, colores y textos
$accionesInfo = [
    'login'           => ['icon' => 'box-arrow-in-right', 'color' => 'success',   'texto' => 'Inició sesión'],
    'logout'          => ['icon' => 'box-arrow-right',    'color' => 'secondary', 'texto' => 'Cerró sesión'],
    'cambio_password' => ['icon' => 'key',                'color' => 'warning',   'texto' => 'Cambió contraseña'],
    'crear_docente'   => ['icon' => 'person-plus',        'color' => 'primary',   'texto' => 'Creó docente'],
    'editar_docente'  => ['icon' => 'pencil',             'color' => 'info',      'texto' => 'Editó docente'],
    'toggle_docente'  => ['icon' => 'toggle-on',          'color' => 'warning',   'texto' => 'Cambió estado docente'],
    'exportar'        => ['icon' => 'download',           'color' => 'success',   'texto' => 'Exportó reporte'],
    'exportar_pdf'    => ['icon' => 'file-pdf',           'color' => 'danger',    'texto' => 'Exportó PDF']
];
?>

<!-- ============ ENCABEZADO ============ -->
<div class="panel mb-4">
    <div class="panel-header">
        <h2 class="panel-title">
            <i class="bi bi-journal-text text-primary"></i>
            Logs de Actividad
            <span class="badge bg-primary ms-2"><?= $total ?></span>
        </h2>
    </div>

    <!-- Filtros colapsables -->
    <button class="filters-toggle" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#filtrosLogs"
            aria-expanded="<?= $hayFiltros ? 'true' : 'false' ?>">
        <i class="bi bi-funnel-fill"></i>
        <span>Filtros de búsqueda</span>
        <?php if ($hayFiltros): ?>
            <span class="badge bg-primary ms-auto">Activos</span>
        <?php endif; ?>
        <i class="bi bi-chevron-down chevron"></i>
    </button>

    <div class="collapse <?= $hayFiltros ? 'show' : '' ?>" id="filtrosLogs">
        <form method="GET" action="/asistencia-api/admin/logs" class="filters-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-person"></i> Usuario</label>
                    <select name="usuario_id" class="form-select">
                        <option value="0">Todos los usuarios</option>
                        <?php foreach ($usuarios as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $usuarioId === (int)$u['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['username']) ?>
                                (<?= ucfirst($u['rol']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label"><i class="bi bi-calendar-event"></i> Desde</label>
                    <input type="date" name="desde" class="form-control"
                           value="<?= htmlspecialchars($desde) ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label"><i class="bi bi-calendar-event"></i> Hasta</label>
                    <input type="date" name="hasta" class="form-control"
                           value="<?= htmlspecialchars($hasta) ?>">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="/asistencia-api/admin/logs" class="btn btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============ TABLA ============ -->
<div class="panel">
    <div class="table-responsive">
        <?php if (empty($logs)): ?>
            <div class="empty-state">
                <i class="bi bi-journal-x"></i>
                <h4>Sin actividad</h4>
                <p>No hay registros en el rango de fechas seleccionado.</p>
            </div>
        <?php else: ?>
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th style="width: 170px;">Fecha y hora</th>
                        <th style="width: 200px;">Usuario</th>
                        <th style="width: 240px;">Acción</th>
                        <th>Detalle</th>
                        <th style="width: 130px;">IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $offset = ($pagina - 1) * 30;
                    foreach ($logs as $i => $l):
                        $info = $accionesInfo[$l['accion']] ?? [
                            'icon' => 'circle',
                            'color' => 'secondary',
                            'texto' => ucfirst(str_replace('_', ' ', $l['accion']))
                        ];
                    ?>
                        <tr>
                            <td class="text-muted small"><?= $offset + $i + 1 ?></td>
                            <td>
                                <div class="fw-semibold small">
                                    <?= date('d/m/Y', strtotime($l['created_at'])) ?>
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i>
                                    <?= date('H:i:s', strtotime($l['created_at'])) ?>
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle avatar-sm">
                                        <?= strtoupper(substr($l['username'] ?? 'X', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">
                                            <?= htmlspecialchars($l['username'] ?? 'Sistema') ?>
                                        </div>
                                        <small class="text-muted">
                                            <?= ucfirst($l['rol'] ?? '—') ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?= $info['color'] ?>-subtle text-<?= $info['color'] ?> border">
                                    <i class="bi bi-<?= $info['icon'] ?>"></i>
                                    <?= $info['texto'] ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= htmlspecialchars($l['detalle'] ?? '—') ?>
                                </small>
                            </td>
                            <td>
                                <code class="small text-muted"><?= htmlspecialchars($l['ip']) ?></code>
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
    count($logs),
    $porPag,
    '/asistencia-api/admin/logs',
    [
        'usuario_id' => $usuarioId,
        'desde' => $desde,
        'hasta' => $hasta
    ]
) ?>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>