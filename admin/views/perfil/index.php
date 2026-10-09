<?php
$titulo = 'Mi Perfil';
$paginaActiva = 'perfil';
require __DIR__ . '/../layouts/header.php';

function err($errores, $campo) {
    return $errores[$campo] ?? null;
}
?>

<div class="row g-4">

    <!-- ===== Columna izquierda: Datos del usuario ===== -->
    <div class="col-lg-4">
        <div class="panel">
            <div class="panel-body text-center py-4">
                <div class="profile-avatar mx-auto mb-3">
                    <?= strtoupper(substr($perfil['username'], 0, 1)) ?>
                </div>
                <h4 class="mb-1"><?= htmlspecialchars($perfil['username']) ?></h4>
                <span class="badge bg-primary mb-3"><?= ucfirst($perfil['rol']) ?></span>

                <div class="profile-info text-start mt-4">
                    <div class="profile-info-item">
                        <i class="bi bi-person-badge"></i>
                        <div>
                            <small class="text-muted d-block">Usuario ID</small>
                            <strong>#<?= $perfil['id'] ?></strong>
                        </div>
                    </div>

                    <div class="profile-info-item">
                        <i class="bi bi-shield-lock"></i>
                        <div>
                            <small class="text-muted d-block">Rol en el sistema</small>
                            <strong><?= ucfirst($perfil['rol']) ?></strong>
                        </div>
                    </div>

                    <div class="profile-info-item">
                        <i class="bi bi-clock-history"></i>
                        <div>
                            <small class="text-muted d-block">Último ingreso</small>
                            <strong>
                                <?= $perfil['ultimo_login']
                                    ? date('d/m/Y H:i', strtotime($perfil['ultimo_login']))
                                    : 'Primera vez' ?>
                            </strong>
                        </div>
                    </div>

                    <div class="profile-info-item">
                        <i class="bi bi-calendar-plus"></i>
                        <div>
                            <small class="text-muted d-block">Miembro desde</small>
                            <strong><?= date('d/m/Y', strtotime($perfil['created_at'])) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Columna derecha: Cambiar contraseña ===== -->
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-key-fill text-primary"></i>
                    Cambiar contraseña
                </h2>
            </div>

            <form method="POST" action="/asistencia-api/admin/perfil/cambiar-password"
                  id="formPassword" class="panel-body" novalidate>

                <div class="alert alert-warning d-flex align-items-center">
                    <i class="bi bi-shield-exclamation me-2 fs-5"></i>
                    <div>
                        <strong>Seguridad:</strong> usa al menos 6 caracteres. Combina letras, números y símbolos para mayor seguridad.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-lock"></i> Contraseña actual <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" name="password_actual" id="password_actual"
                               class="form-control <?= err($errores,'password_actual') ? 'is-invalid' : '' ?>"
                               placeholder="Tu contraseña actual" required>
                        <button class="btn btn-outline-secondary toggle-pass" type="button"
                                data-target="password_actual">
                            <i class="bi bi-eye"></i>
                        </button>
                        <div class="invalid-feedback"><?= err($errores,'password_actual') ?></div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-key"></i> Nueva contraseña <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" name="password_nueva" id="password_nueva"
                                   class="form-control <?= err($errores,'password_nueva') ? 'is-invalid' : '' ?>"
                                   minlength="6" placeholder="Mínimo 6 caracteres" required>
                            <button class="btn btn-outline-secondary toggle-pass" type="button"
                                    data-target="password_nueva">
                                <i class="bi bi-eye"></i>
                            </button>
                            <div class="invalid-feedback"><?= err($errores,'password_nueva') ?></div>
                        </div>
                        <div class="password-strength mt-2" id="strengthBar" style="display:none;">
                            <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                            <small class="text-muted" id="strengthText"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-key-fill"></i> Confirmar contraseña <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" name="password_confirmar" id="password_confirmar"
                                   class="form-control <?= err($errores,'password_confirmar') ? 'is-invalid' : '' ?>"
                                   placeholder="Repite la nueva contraseña" required>
                            <button class="btn btn-outline-secondary toggle-pass" type="button"
                                    data-target="password_confirmar">
                                <i class="bi bi-eye"></i>
                            </button>
                            <div class="invalid-feedback"><?= err($errores,'password_confirmar') ?></div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="/asistencia-api/admin/dashboard" class="btn btn-outline-secondary">
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>