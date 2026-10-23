<?php
$titulo = 'Gestión de Usuarios';
$paginaActiva = 'usuarios';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row g-4">
    <!-- Columna Izquierda: Lista de usuarios -->
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="bi bi-people-fill text-primary"></i> Usuarios Administrativos</h2>
                <span class="badge bg-primary"><?= count($usuarios) ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr class="<?= !$u['activo'] ? 'row-inactive' : '' ?>">
                                <td class="fw-semibold"><?= htmlspecialchars($u['nombre_completo'] ?? '—') ?></td>
                                <td><code><?= htmlspecialchars($u['username']) ?></code></td>
                                <td>
                                    <span class="badge bg-<?= $u['rol_nombre'] === 'admin' ? 'primary' : 'info' ?>">
                                        <?= ucfirst($u['rol_nombre']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($u['activo']): ?>
                                        <span class="badge bg-success-subtle text-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $u['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php if ($u['id'] != $user['id']): ?>
                                        <button class="btn btn-sm btn-outline-<?= $u['activo'] ? 'warning' : 'success' ?>" 
                                                data-action="toggle-usuario" 
                                                data-id="<?= $u['id'] ?>"
                                                data-activo="<?= $u['activo'] ?>"
                                               data-nombre="<?= htmlspecialchars($u['nombre_completo'] ?? $u['username']) ?>"
                                            <i class="bi bi-<?= $u['activo'] ? 'pause' : 'play' ?>-fill"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            
                            <!-- Modal de Edición (uno por usuario) -->
                            <div class="modal fade" id="modalEditar<?= $u['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="/asistencia-api/admin/usuarios/editar/<?= $u['id'] ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar Usuario</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nombre Completo</label>
                                                    <input type="text" name="nombre_completo" class="form-control" value="<?= htmlspecialchars($u['nombre_completo'] ?? '') ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Rol</label>
                                                    <select name="rol_id" class="form-select">
                                                        <option value="1" <?= $u['rol_nombre'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                                        <option value="2" <?= $u['rol_nombre'] === 'coordinador' ? 'selected' : '' ?>>Coordinador</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nueva Contraseña (opcional)</label>
                                                    <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Formulario de Nuevo Usuario -->
    <div class="col-lg-4">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="bi bi-person-plus text-primary"></i> Nuevo Usuario</h2>
            </div>
            <form method="POST" action="/asistencia-api/admin/usuarios/crear" class="panel-body">
                <?php if (!empty($errores['general'])): ?>
                    <div class="alert alert-danger small"><?= $errores['general'] ?></div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_completo" class="form-control <?= isset($errores['nombre_completo']) ? 'is-invalid' : '' ?>" placeholder="Ej: María López" value="<?= htmlspecialchars($old['nombre_completo'] ?? '') ?>">
                    <div class="invalid-feedback"><?= $errores['nombre_completo'] ?? '' ?></div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Usuario <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control <?= isset($errores['username']) ? 'is-invalid' : '' ?>" placeholder="mlopez" value="<?= htmlspecialchars($old['username'] ?? '') ?>">
                    <div class="invalid-feedback"><?= $errores['username'] ?? '' ?></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control <?= isset($errores['password']) ? 'is-invalid' : '' ?>" placeholder="Mínimo 6 caracteres">
                    <div class="invalid-feedback"><?= $errores['password'] ?? '' ?></div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Rol <span class="text-danger">*</span></label>
                    <select name="rol_id" class="form-select">
                        <option value="2">Coordinador</option>
                        <option value="1">Administrador</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-lg"></i> Crear Usuario
                </button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>