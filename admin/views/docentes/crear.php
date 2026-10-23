<?php
$titulo = 'Nuevo Docente';
$paginaActiva = 'docentes';
require __DIR__ . '/../layouts/header.php';

// Helper para mostrar errores
function err($errores, $campo) {
    return $errores[$campo] ?? null;
}
function old($old, $campo, $default = '') {
    return htmlspecialchars($old[$campo] ?? $default);
}
?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-person-plus-fill text-primary"></i>
                    Nuevo Docente
                </h2>
                <a href="/asistencia-api/admin/docentes" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <form method="POST" action="/asistencia-api/admin/docentes/crear"
                  id="formDocente" class="panel-body" novalidate>

                <?php if (!empty($errores['general'])): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <?= htmlspecialchars($errores['general']) ?>
                    </div>
                <?php endif; ?>

                <h6 class="form-section-title">
                    <i class="bi bi-person-badge"></i> Datos personales
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">DNI <span class="text-danger">*</span></label>
                        <input type="text" name="dni" id="dni"
                               class="form-control <?= err($errores,'dni') ? 'is-invalid' : '' ?>"
                               maxlength="8" inputmode="numeric"
                               placeholder="12345678"
                               value="<?= old($old, 'dni') ?>" required>
                        <div class="invalid-feedback"><?= err($errores,'dni') ?></div>
                        <small class="form-text text-muted">8 dígitos numéricos</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" name="nombres" id="nombres"
                               class="form-control <?= err($errores,'nombres') ? 'is-invalid' : '' ?>"
                               maxlength="100" placeholder="Juan Carlos"
                               value="<?= old($old, 'nombres') ?>" required>
                        <div class="invalid-feedback"><?= err($errores,'nombres') ?></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" name="apellidos" id="apellidos"
                               class="form-control <?= err($errores,'apellidos') ? 'is-invalid' : '' ?>"
                               maxlength="100" placeholder="Pérez Gómez"
                               value="<?= old($old, 'apellidos') ?>" required>
                        <div class="invalid-feedback"><?= err($errores,'apellidos') ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email"
                               class="form-control <?= err($errores,'email') ? 'is-invalid' : '' ?>"
                               maxlength="120" placeholder="juan.perez@colegio.edu.pe"
                               value="<?= old($old, 'email') ?>">
                        <div class="invalid-feedback"><?= err($errores,'email') ?></div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" id="telefono"
                               class="form-control <?= err($errores,'telefono') ? 'is-invalid' : '' ?>"
                               maxlength="15" inputmode="numeric"
                               placeholder="987654321"
                               value="<?= old($old, 'telefono') ?>">
                        <div class="invalid-feedback"><?= err($errores,'telefono') ?></div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Especialidad</label>
                        <input type="text" name="especialidad" id="especialidad"
                               class="form-control"
                               maxlength="100" placeholder="Matemática"
                               value="<?= old($old, 'especialidad') ?>">
                    </div>

                    <!-- SELECTOR DE TURNO -->
                    <div class="col-md-3">
                        <label class="form-label">Turno <span class="text-danger">*</span></label>
                        <select name="turno_id" class="form-select <?= err($errores,'turno_id') ? 'is-invalid' : '' ?>" required>
                            <option value="">Seleccione un turno...</option>
                            <?php foreach ($turnos as $t): ?>
                                <option value="<?= $t['id'] ?>" 
                                    <?= ($old['turno_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['nombre']) ?> 
                                    (<?= substr($t['hora_entrada'], 0, 5) ?> - <?= substr($t['hora_salida'], 0, 5) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= err($errores,'turno_id') ?></div>
                    </div>
                </div>

                <h6 class="form-section-title">
                    <i class="bi bi-shield-lock"></i> Credenciales de acceso
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Usuario <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">@</span>
                            <input type="text" name="username" id="username"
                                   class="form-control <?= err($errores,'username') ? 'is-invalid' : '' ?>"
                                   maxlength="50" placeholder="jperez"
                                   value="<?= old($old, 'username') ?>" required>
                            <div class="invalid-feedback"><?= err($errores,'username') ?></div>
                        </div>
                        <small class="form-text text-muted">Sin espacios. Letras, números, puntos y _</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <input type="password" name="password" id="password"
                                   class="form-control <?= err($errores,'password') ? 'is-invalid' : '' ?>"
                                   minlength="6" placeholder="Mínimo 6 caracteres" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                            <div class="invalid-feedback"><?= err($errores,'password') ?></div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="/asistencia-api/admin/docentes" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Guardar Docente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>