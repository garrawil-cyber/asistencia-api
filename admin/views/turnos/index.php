<?php
$titulo = 'Gestión de Turnos';
$paginaActiva = 'turnos';
require __DIR__ . '/../layouts/header.php';

$hayFiltros = ($busqueda !== '') || ($estado !== '');
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

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">
            <i class="bi bi-clock text-primary"></i>
            Turnos
            <span class="badge bg-primary ms-2"><?= count($turnos) ?></span>
        </h2>
        <a href="/asistencia-api/admin/turnos/crear" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuevo Turno
        </a>
    </div>

    <!-- ============ FILTROS COLAPSABLES ============ -->
    <button class="filters-toggle" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#filtrosTurnos"
            aria-expanded="<?= $hayFiltros ? 'true' : 'false' ?>">
        <i class="bi bi-funnel-fill"></i>
        <span>Filtros de búsqueda</span>
        <?php if ($hayFiltros): ?>
            <span class="badge bg-primary ms-auto">Activos</span>
        <?php endif; ?>
        <i class="bi bi-chevron-down chevron"></i>
    </button>

    <div class="collapse <?= $hayFiltros ? 'show' : '' ?>" id="filtrosTurnos">
        <form method="GET" action="/asistencia-api/admin/turnos" class="filters-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-search"></i> Buscar
                    </label>
                    <input type="text" name="q" class="form-control"
                           placeholder="Código o nombre del turno..."
                           value="<?= htmlspecialchars($busqueda) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="bi bi-toggle-on"></i> Estado
                    </label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activos" <?= $estado === 'activos' ? 'selected' : '' ?>>Activos</option>
                        <option value="inactivos" <?= $estado === 'inactivos' ? 'selected' : '' ?>>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <a href="/asistencia-api/admin/turnos" class="btn btn-outline-secondary" title="Limpiar">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- ============ TABLA ============ -->
    <div class="table-responsive">
        <?php if (empty($turnos)): ?>
            <div class="empty-state">
                <i class="bi bi-clock"></i>
                <h4>Sin resultados</h4>
                <p>No hay turnos con los filtros aplicados.</p>
                <a href="/asistencia-api/admin/turnos/crear" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Crear el primero
                </a>
            </div>
        <?php else: ?>
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Horario</th>
                        <th>Tolerancia</th>
                        <th class="text-center">Docentes</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turnos as $i => $t): ?>
                        <tr class="<?= !$t['activo'] ? 'row-inactive' : '' ?>">
                            <td class="text-muted small"><?= $i + 1 ?></td>
                            <td><code><?= htmlspecialchars($t['codigo']) ?></code></td>
                            <td><strong><?= htmlspecialchars($t['nombre']) ?></strong></td>
                            <td>
                                <span class="text-primary fw-semibold">
                                    <?= substr($t['hora_entrada'], 0, 5) ?>
                                </span>
                                <i class="bi bi-arrow-right"></i>
                                <span class="text-primary fw-semibold">
                                    <?= substr($t['hora_salida'], 0, 5) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    ⏱ <?= $t['tolerancia_minutos'] ?> min
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill">
                                    <?= $t['total_docentes'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($t['activo']): ?>
                                    <span class="badge bg-success-subtle text-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="/asistencia-api/admin/turnos/editar/<?= $t['id'] ?>"
                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-<?= $t['activo'] ? 'warning' : 'success' ?>"
                                        data-action="toggle-turno"
                                        data-id="<?= $t['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($t['nombre']) ?>"
                                        data-activo="<?= $t['activo'] ? '1' : '0' ?>"
                                        data-docentes="<?= $t['total_docentes'] ?>"
                                        title="<?= $t['activo'] ? 'Desactivar' : 'Activar' ?>">
                                    <i class="bi bi-<?= $t['activo'] ? 'pause' : 'play' ?>-fill"></i>
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