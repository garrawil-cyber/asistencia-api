<?php
$titulo = 'Gestión de Backups';
$paginaActiva = 'backups';
require __DIR__ . '/../layouts/header.php';

$hayFiltros = ($busqueda !== '') || ($tipo !== '');
$totalTamano = 0;
foreach ($backups as $b) { $totalTamano += $b['tamano_bytes']; }
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

<!-- KPIs -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="kpi-card kpi-primary">
            <div class="kpi-icon"><i class="bi bi-database"></i></div>
            <div class="kpi-label">Total Backups</div>
            <div class="kpi-value"><?= count($backups) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card kpi-success">
            <div class="kpi-icon"><i class="bi bi-hand-index-thumb"></i></div>
            <div class="kpi-label">Manuales</div>
            <div class="kpi-value"><?= count(array_filter($backups, fn($b) => $b['tipo'] === 'manual')) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card kpi-info">
            <div class="kpi-icon"><i class="bi bi-hdd"></i></div>
            <div class="kpi-label">Espacio Total</div>
            <div class="kpi-value"><?= number_format($totalTamano / 1024 / 1024, 2) ?> MB</div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">
            <i class="bi bi-database-fill-gear text-primary"></i> Respaldos de Base de Datos
        </h2>
        <div class="d-flex gap-2">
            <form method="POST" action="/asistencia-api/admin/backups/crear" class="m-0">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Crear Backup
                </button>
            </form>
        </div>
    </div>

    <!-- FILTROS COLAPSABLES -->
    <button class="filters-toggle" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#filtrosBackups"
            aria-expanded="<?= $hayFiltros ? 'true' : 'false' ?>">
        <i class="bi bi-funnel-fill"></i>
        <span>Filtros de búsqueda</span>
        <?php if ($hayFiltros): ?>
            <span class="badge bg-primary ms-auto">Activos</span>
        <?php endif; ?>
        <i class="bi bi-chevron-down chevron"></i>
    </button>

    <div class="collapse <?= $hayFiltros ? 'show' : '' ?>" id="filtrosBackups">
        <form method="GET" action="/asistencia-api/admin/backups" class="filters-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label"><i class="bi bi-search"></i> Buscar archivo</label>
                    <input type="text" name="q" class="form-control" placeholder="backup_manual..." value="<?= htmlspecialchars($busqueda) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-tag"></i> Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="manual" <?= $tipo === 'manual' ? 'selected' : '' ?>>Manual</option>
                        <option value="automatico" <?= $tipo === 'automatico' ? 'selected' : '' ?>>Automático</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i> Filtrar</button>
                    <a href="/asistencia-api/admin/backups" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                </div>
            </div>
        </form>
    </div>

    <!-- TABLA DE BACKUPS -->
    <div class="table-responsive">
        <?php if (empty($backups)): ?>
            <div class="empty-state">
                <i class="bi bi-database-x"></i>
                <h4>No hay respaldos</h4>
                <p>Crea tu primer backup para proteger tu base de datos.</p>
            </div>
        <?php else: ?>
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Tipo</th>
                        <th>Tamaño</th>
                        <th>Fecha de Creación</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $b): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-primary fs-4"><i class="bi bi-filetype-sql"></i></div>
                                    <div class="fw-semibold small"><?= htmlspecialchars($b['nombre']) ?></div>
                                </div>
                            </td>
                            <td>
                                <?php if ($b['tipo'] === 'manual'): ?>
                                    <span class="badge bg-primary-subtle text-primary"><i class="bi bi-hand-index-thumb"></i> Manual</span>
                                <?php else: ?>
                                    <span class="badge bg-info-subtle text-info"><i class="bi bi-robot"></i> Automático</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="text-muted small"><?= $b['tamano'] ?></span></td>
                            <td><span class="small"><?= $b['fecha_creacion'] ?></span></td>
                            <td class="text-center">
                                <a href="/asistencia-api/admin/backups/descargar/<?= urlencode($b['nombre']) ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Descargar">
                                    <i class="bi bi-download"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Eliminar"
                                        data-action="eliminar-backup"
                                        data-nombre="<?= htmlspecialchars($b['nombre']) ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>