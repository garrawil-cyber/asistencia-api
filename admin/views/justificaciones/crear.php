<?php
use App\Core\DateHelper;

$titulo = 'Nueva Justificación';
$paginaActiva = 'justificaciones';
require __DIR__ . '/../layouts/header.php';

function err($errores, $campo) {
    return $errores[$campo] ?? null;
}
function old($old, $campo, $default = '') {
    return htmlspecialchars($old[$campo] ?? $default);
}
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="/asistencia-api/admin/justificaciones" class="text-decoration-none">
                <i class="bi bi-file-earmark-text"></i> Justificaciones
            </a>
        </li>
        <li class="breadcrumb-item active">Nueva</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-file-earmark-plus-fill text-primary"></i>
                    Nueva Justificación
                </h2>
                <a href="/asistencia-api/admin/justificaciones" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <form method="POST"
                  action="/asistencia-api/admin/justificaciones/crear"
                  id="formJustificacion"
                  enctype="multipart/form-data"
                  class="panel-body" novalidate>

                <?php if (!empty($errores['general'])): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <?= htmlspecialchars($errores['general']) ?>
                    </div>
                <?php endif; ?>

                <h6 class="form-section-title">
                    <i class="bi bi-clipboard-x"></i> Asistencia a justificar
                </h6>

                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    <div class="small">
                        Solo se muestran asistencias con estado <strong>ausente</strong>,
                        <strong>falta de salida</strong> o <strong>tarde</strong> que
                        <strong>aún no tienen justificación</strong>.
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Seleccionar asistencia <span class="text-danger">*</span></label>
                        <select name="asistencia_id" id="asistencia_id"
                                class="form-select <?= err($errores,'asistencia_id') ? 'is-invalid' : '' ?>"
                                required>
                            <option value="">— Selecciona una asistencia —</option>
                            <?php foreach ($asistencias as $a): ?>
                                <option value="<?= $a['id'] ?>"
                                        <?= (int)old($old, 'asistencia_id') === (int)$a['id'] ? 'selected' : '' ?>>
                                    <?= date('d/m/Y', strtotime($a['fecha'])) ?>
                                    · <?= DateHelper::diaSemana($a['fecha']) ?>
                                    · <?= htmlspecialchars($a['nombres'] . ' ' . $a['apellidos']) ?>
                                    · <?= ucfirst($a['estado']) ?>
                                    <?= $a['minutos_tardanza'] > 0 ? '(' . $a['minutos_tardanza'] . ' min)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= err($errores,'asistencia_id') ?></div>
                        <?php if (empty($asistencias)): ?>
                            <small class="form-text text-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                No hay asistencias pendientes de justificar.
                            </small>
                        <?php endif; ?>
                    </div>
                </div>

                <h6 class="form-section-title">
                    <i class="bi bi-chat-left-text"></i> Detalle de la justificación
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">
                            Motivo <span class="text-danger">*</span>
                        </label>
                        <textarea name="motivo" id="motivo"
                                  class="form-control <?= err($errores,'motivo') ? 'is-invalid' : '' ?>"
                                  rows="5"
                                  minlength="10"
                                  maxlength="1000"
                                  placeholder="Describe el motivo de la ausencia o tardanza (mínimo 10 caracteres)"
                                  required><?= old($old, 'motivo') ?></textarea>
                        <div class="invalid-feedback"><?= err($errores,'motivo') ?></div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="form-text text-muted">
                                Mínimo 10 caracteres. Sé específico.
                            </small>
                            <small class="form-text text-muted">
                                <span id="contadorMotivo">0</span>/1000
                            </small>
                        </div>
                    </div>
                </div>

                <h6 class="form-section-title">
                    <i class="bi bi-paperclip"></i> Archivo adjunto (opcional)
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Comprobante</label>
                        <input type="file"
                               name="archivo"
                               id="archivo"
                               class="form-control <?= err($errores,'archivo') ? 'is-invalid' : '' ?>"
                               accept=".jpg,.jpeg,.png,.pdf">
                        <div class="invalid-feedback"><?= err($errores,'archivo') ?></div>
                        <small class="form-text text-muted">
                            Formatos: JPG, PNG, PDF. Tamaño máximo: 5 MB.
                        </small>
                        <div id="previewArchivo" class="mt-2" style="display: none;">
                            <span class="badge bg-info">
                                <i class="bi bi-file-earmark"></i>
                                <span id="nombreArchivo"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="/asistencia-api/admin/justificaciones" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Guardar Justificación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador de caracteres del motivo
    const motivo = document.getElementById('motivo');
    const contador = document.getElementById('contadorMotivo');
    if (motivo) {
        motivo.addEventListener('input', function() {
            contador.textContent = this.value.length;
        });
        contador.textContent = motivo.value.length;
    }

    // Preview del archivo
    const archivo = document.getElementById('archivo');
    const preview = document.getElementById('previewArchivo');
    const nombre = document.getElementById('nombreArchivo');
    if (archivo) {
        archivo.addEventListener('change', function() {
            if (this.files.length > 0) {
                nombre.textContent = this.files[0].name;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>