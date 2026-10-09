<?php
$titulo = 'Gestión de Docentes';
$paginaActiva = 'docentes';
require __DIR__ . '/../layouts/header.php';
?>

<!-- Flash messages -->
<?php if (!empty($flashExito)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($flashExito) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($flashError) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Panel principal -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">
            <i class="bi bi-people-fill text-primary"></i>
            Docentes
            <span class="badge bg-primary ms-2"><?= $total ?></span>
        </h2>
        <a href="/asistencia-api/admin/docentes/crear" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuevo Docente
        </a>
    </div>

    <!-- ============ FILTROS COLAPSABLES ============ -->
    <div class="filters-section">
        <button class="filters-toggle" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#filtros"
                aria-expanded="<?= ($busqueda || $estado) ? 'true' : 'false' ?>">
            <i class="bi bi-funnel-fill"></i>
            <span>Filtros de búsqueda</span>
            <?php if ($busqueda || $estado): ?>
                <span class="badge bg-primary ms-auto">Activos</span>
            <?php endif; ?>
            <i class="bi bi-chevron-down chevron"></i>
        </button>

        <div class="collapse <?= ($busqueda || $estado) ? 'show' : '' ?>" id="filtros">
            <form method="GET" action="/asistencia-api/admin/docentes" class="filters-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-search"></i> Buscar
                        </label>
                        <input type="text"
                               name="q"
                               class="form-control"
                               placeholder="Nombre, apellido, DNI o usuario..."
                               value="<?= htmlspecialchars($busqueda) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-toggle-on"></i> Estado
                        </label>
                        <select name="estado" class="form-select">
                            <option value="" <?= $estado === '' ? 'selected' : '' ?>>Todos</option>
                            <option value="activos" <?= $estado === 'activos' ? 'selected' : '' ?>>Activos</option>
                            <option value="inactivos" <?= $estado === 'inactivos' ? 'selected' : '' ?>>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <a href="/asistencia-api/admin/docentes" class="btn btn-outline-secondary" title="Limpiar filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ============ TABLA ============ -->
    <div class="table-responsive">
        <?php if (empty($docentes)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>Sin resultados</h4>
                <p>No se encontraron docentes con los filtros aplicados.</p>
                <a href="/asistencia-api/admin/docentes/crear" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Crear el primero
                </a>
            </div>
        <?php else: ?>
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Docente</th>
                        <th>DNI</th>
                        <th>Contacto</th>
                        <th>Especialidad</th>
                        <th class="text-center" style="width: 100px;">Estado</th>
                        <th class="text-end" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($docentes as $i => $d): ?>
                        <tr class="<?= !$d['activo'] ? 'row-inactive' : '' ?>">
                            <td class="text-muted small">
                                <?= $offset + $i + 1 ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle">
                                        <?= strtoupper(substr($d['nombres'], 0, 1) . substr($d['apellidos'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($d['nombres'] . ' ' . $d['apellidos']) ?>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bi bi-at"></i> <?= htmlspecialchars($d['username'] ?? '—') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><code><?= htmlspecialchars($d['dni']) ?></code></td>
                            <td class="small">
                                <?php if ($d['email']): ?>
                                    <div><i class="bi bi-envelope text-muted"></i> <?= htmlspecialchars($d['email']) ?></div>
                                <?php endif; ?>
                                <?php if ($d['telefono']): ?>
                                    <div><i class="bi bi-phone text-muted"></i> <?= htmlspecialchars($d['telefono']) ?></div>
                                <?php endif; ?>
                                <?php if (!$d['email'] && !$d['telefono']): ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="small"><?= htmlspecialchars($d['especialidad'] ?? '—') ?></td>
                            <td class="text-center">
                                <?php if ($d['activo']): ?>
                                    <span class="badge bg-success-subtle text-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="/asistencia-api/admin/docentes/editar/<?= $d['id'] ?>"
                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-<?= $d['activo'] ? 'warning' : 'success' ?>"
                                        data-action="toggle-docente"
                                        data-id="<?= $d['id'] ?>"
                                        data-nombre="<?= htmlspecialchars($d['nombres'] . ' ' . $d['apellidos']) ?>"
                                        data-activo="<?= $d['activo'] ? '1' : '0' ?>"
                                        title="<?= $d['activo'] ? 'Desactivar' : 'Activar' ?>">
                                    <i class="bi bi-<?= $d['activo'] ? 'pause' : 'play' ?>-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- ============ PAGINACIÓN ============ -->
    <?php if ($paginas > 1): ?>
        <div class="panel-footer">
            <div class="pagination-info">
                Mostrando <?= count($docentes) ?> de <?= $total ?> docentes
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php
                    $qs = http_build_query(['q' => $busqueda, 'estado' => $estado]);
                    ?>
                    <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina - 1 ?>&<?= $qs ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php for ($p = max(1, $pagina - 2); $p <= min($paginas, $pagina + 2); $p++): ?>
                        <li class="page-item <?= $p === $pagina ? 'active' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $p ?>&<?= $qs ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $pagina >= $paginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina + 1 ?>&<?= $qs ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>