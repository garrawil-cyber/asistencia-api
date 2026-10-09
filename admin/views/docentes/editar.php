<?php
$titulo = 'Editar Docente';
$paginaActiva = 'docentes';
require __DIR__ . '/../layouts/header.php';

function err($errores, $campo) {
    return $errores[$campo] ?? null;
}
function old($doc, $campo, $default = '') {
    return htmlspecialchars($doc[$campo] ?? $default);
}
?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-pencil-square text-primary"></i>
                    Editar Docente
                </h2>
                <a href="/asistencia-api/admin/docentes" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <form method="POST"
                  action="/asistencia-api/admin/docentes/editar/<?= $docente['id'] ?>"
                  id="formDocente" class="panel-body" novalidate>

                <?php if (!empty($errores['general'])): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <?= htmlspecialchars($errores['general']) ?>
                    </div>
                <?php endif; ?>

                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                        Usuario: <strong><?= htmlspecialchars($docente['username']) ?></strong>
                        · <span class="text-muted">El usuario y contraseña no se pueden modificar desde aquí.</span>
                    </div>
                </div>

                <h6 class="form-section-title">
                    <i class="bi bi-person-badge"></i> Datos personales
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">DNI <span class="text-danger">*</span></label>
                        <input type="text" name="dni" id="dni"
                               class="form-control <?= err($errores,'dni') ? 'is-invalid' : '' ?>"
                               maxlength="8" inputmode="numeric"
                               value="<?= old($docente, 'dni') ?>" required>
                        <div class="invalid-feedback"><?= err($errores,'dni') ?></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" name="nombres" id="nombres"
                               class="form-control <?= err($errores,'nombres') ? 'is-invalid' : '' ?>"
                               value="<?= old($docente, 'nombres') ?>" required>
                        <div class="invalid-feedback"><?= err($errores,'nombres') ?></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" name="apellidos" id="apellidos"
                               class="form-control <?= err($errores,'apellidos') ? 'is-invalid' : '' ?>"
                               value="<?= old($docente, 'apellidos') ?>" required>
                        <div class="invalid-feedback"><?= err($errores,'apellidos') ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email"
                               class="form-control <?= err($errores,'email') ? 'is-invalid' : '' ?>"
                               value="<?= old($docente, 'email') ?>">
                        <div class="invalid-feedback"><?= err($errores,'email') ?></div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" id="telefono"
                               class="form-control <?= err($errores,'telefono') ? 'is-invalid' : '' ?>"
                               maxlength="15" inputmode="numeric"
                               value="<?= old($docente, 'telefono') ?>">
                        <div class="invalid-feedback"><?= err($errores,'telefono') ?></div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Especialidad</label>
                        <input type="text" name="especialidad"
                               class="form-control"
                               value="<?= old($docente, 'especialidad') ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="/asistencia-api/admin/docentes" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>