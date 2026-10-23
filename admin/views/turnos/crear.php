<?php
$titulo = 'Nuevo Turno';
$paginaActiva = 'turnos';
require __DIR__ . '/../layouts/header.php';

function err($errores, $campo) {
    return $errores[$campo] ?? null;
}
function old($old, $campo, $default = '') {
    return htmlspecialchars($old[$campo] ?? $default);
}
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-clock-fill text-primary"></i>
                    Nuevo Turno
                </h2>
                <a href="/asistencia-api/admin/turnos" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <form method="POST" action="/asistencia-api/admin/turnos/crear"
                  id="formTurno" class="panel-body" novalidate>

                <?php if (!empty($errores['general'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errores['general']) ?></div>
                <?php endif; ?>

                <!-- ============ DATOS DEL TURNO ============ -->
                <h6 class="form-section-title">
                    <i class="bi bi-info-circle"></i> Datos del turno
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" name="codigo" id="codigo"
                               class="form-control <?= err($errores,'codigo') ? 'is-invalid' : '' ?>"
                               maxlength="20"
                               placeholder="manana, tarde, noche"
                               value="<?= old($old, 'codigo') ?>" required>
                        <div class="invalid-feedback"><?= err($errores,'codigo') ?></div>
                        <small class="form-text text-muted">Letras minúsculas, números y _</small>
                    </div>

                    <div class="col-md-9">
                        <label class="form-label">Nombre del turno <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre"
                               class="form-control <?= err($errores,'nombre') ? 'is-invalid' : '' ?>"
                               maxlength="50"
                               placeholder="Ej: Turno Mañana"
                               value="<?= old($old, 'nombre') ?>" required>
                        <div class="invalid-feedback"><?= err($errores,'nombre') ?></div>
                    </div>
                </div>

                <!-- ============ HORARIO OFICIAL ============ -->
                <h6 class="form-section-title">
                    <i class="bi bi-clock"></i> Horario oficial
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Hora de entrada <span class="text-danger">*</span></label>
                        <input type="time" name="hora_entrada" id="hora_entrada"
                               class="form-control <?= err($errores,'hora_entrada') ? 'is-invalid' : '' ?>"
                               value="<?= old($old, 'hora_entrada', '07:00') ?>" required>
                        <div class="invalid-feedback"><?= err($errores,'hora_entrada') ?></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Hora de salida <span class="text-danger">*</span></label>
                        <input type="time" name="hora_salida" id="hora_salida"
                               class="form-control <?= err($errores,'hora_salida') ? 'is-invalid' : '' ?>"
                               value="<?= old($old, 'hora_salida', '13:00') ?>" required>
                        <div class="invalid-feedback"><?= err($errores,'hora_salida') ?></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tolerancia (min)</label>
                        <input type="number" name="tolerancia_minutos" id="tolerancia_minutos"
                               class="form-control <?= err($errores,'tolerancia_minutos') ? 'is-invalid' : '' ?>"
                               min="0" max="60"
                               value="<?= old($old, 'tolerancia_minutos', 5) ?>">
                        <div class="invalid-feedback"><?= err($errores,'tolerancia_minutos') ?></div>
                        <small class="form-text text-muted">Minutos de gracia para tardanzas</small>
                    </div>
                </div>

                <!-- ============ VENTANAS DE MARCADO ============ -->
                <h6 class="form-section-title">
                    <i class="bi bi-shield-check"></i> Ventanas de marcación
                </h6>

                <div class="alert alert-info d-flex align-items-start small mb-3">
                    <i class="bi bi-info-circle me-2 mt-1"></i>
                    <div>
                        Define los rangos de hora en los que el docente puede marcar.
                        <strong>Fuera de estos rangos, la marcación será rechazada.</strong>
                    </div>
                </div>

                <!-- Sub-bloque ENTRADA -->
                <div class="ventana-bloque mb-3">
                    <div class="ventana-titulo">
                        <i class="bi bi-box-arrow-in-down"></i> Entrada
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Permitida desde</label>
                            <input type="time" name="ventana_entrada_inicio" id="ventana_entrada_inicio"
                                   class="form-control <?= err($errores,'ventana_entrada_inicio') ? 'is-invalid' : '' ?>"
                                   value="<?= old($old, 'ventana_entrada_inicio', '05:00') ?>" required>
                            <div class="invalid-feedback"><?= err($errores,'ventana_entrada_inicio') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Permitida hasta</label>
                            <input type="time" name="ventana_entrada_fin" id="ventana_entrada_fin"
                                   class="form-control <?= err($errores,'ventana_entrada_fin') ? 'is-invalid' : '' ?>"
                                   value="<?= old($old, 'ventana_entrada_fin', '09:00') ?>" required>
                            <div class="invalid-feedback"><?= err($errores,'ventana_entrada_fin') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Sub-bloque SALIDA -->
                <div class="ventana-bloque mb-4">
                    <div class="ventana-titulo">
                        <i class="bi bi-box-arrow-up"></i> Salida
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Permitida desde</label>
                            <input type="time" name="ventana_salida_inicio" id="ventana_salida_inicio"
                                   class="form-control <?= err($errores,'ventana_salida_inicio') ? 'is-invalid' : '' ?>"
                                   value="<?= old($old, 'ventana_salida_inicio', '12:00') ?>" required>
                            <div class="invalid-feedback"><?= err($errores,'ventana_salida_inicio') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Permitida hasta</label>
                            <input type="time" name="ventana_salida_fin" id="ventana_salida_fin"
                                   class="form-control <?= err($errores,'ventana_salida_fin') ? 'is-invalid' : '' ?>"
                                   value="<?= old($old, 'ventana_salida_fin', '15:00') ?>" required>
                            <div class="invalid-feedback"><?= err($errores,'ventana_salida_fin') ?></div>
                        </div>
                    </div>
                </div>

                <!-- ============ ESTADO ============ -->
                <h6 class="form-section-title">
                    <i class="bi bi-toggle-on"></i> Estado del turno
                </h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" id="activo"
                                   class="form-check-input" value="1" checked>
                            <label class="form-check-label" for="activo">
                                <strong>Turno activo</strong>
                                <br>
                                <small class="text-muted">Los docentes solo pueden marcar asistencia en turnos activos</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ============ BOTONES ============ -->
                <div class="form-actions">
                    <a href="/asistencia-api/admin/turnos" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Crear Turno
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formTurno');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validaciones JS
        const codigo = document.getElementById('codigo');
        const nombre = document.getElementById('nombre');
        const horaEntrada = document.getElementById('hora_entrada');
        const horaSalida = document.getElementById('hora_salida');
        const tolerancia = document.getElementById('tolerancia_minutos');
        const vEntradaIni = document.getElementById('ventana_entrada_inicio');
        const vEntradaFin = document.getElementById('ventana_entrada_fin');
        const vSalidaIni = document.getElementById('ventana_salida_inicio');
        const vSalidaFin = document.getElementById('ventana_salida_fin');

        let errores = [];
        if (!codigo.value.trim()) errores.push('El código es obligatorio');
        else if (!/^[a-z0-9_]{2,20}$/.test(codigo.value.trim())) errores.push('Código: solo minúsculas, números y _ (2-20)');
        if (!nombre.value.trim()) errores.push('El nombre es obligatorio');
        if (!horaEntrada.value) errores.push('La hora de entrada es obligatoria');
        if (!horaSalida.value) errores.push('La hora de salida es obligatoria');
        if (horaEntrada.value && horaSalida.value && horaEntrada.value >= horaSalida.value) {
            errores.push('La hora de salida debe ser posterior a la de entrada');
        }
        if (!vEntradaIni.value || !vEntradaFin.value) errores.push('Completa la ventana de entrada');
        if (!vSalidaIni.value || !vSalidaFin.value) errores.push('Completa la ventana de salida');

        if (errores.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Revisa el formulario',
                html: '<ul class="text-start mb-0" style="font-size: 0.9rem;">' +
                      errores.map(e => `<li>${e}</li>`).join('') + '</ul>',
                confirmButtonColor: '#ef4444'
            });
            return;
        }

        // Preview
        const preview = `
            <div class="preview-header">
                <div class="preview-avatar">⏰</div>
                <div class="preview-name">${nombre.value}</div>
            </div>
            <div class="preview-grid">
                <div class="preview-item"><i class="bi bi-tag"></i> <strong>Código:</strong> ${codigo.value}</div>
                <div class="preview-item"><i class="bi bi-clock"></i> <strong>Entrada:</strong> ${horaEntrada.value}</div>
                <div class="preview-item"><i class="bi bi-clock"></i> <strong>Salida:</strong> ${horaSalida.value}</div>
                <div class="preview-item"><i class="bi bi-hourglass-split"></i> <strong>Tolerancia:</strong> ${tolerancia.value} min</div>
                <div class="preview-item"><i class="bi bi-box-arrow-in-down"></i> <strong>Vent. entrada:</strong> ${vEntradaIni.value} - ${vEntradaFin.value}</div>
                <div class="preview-item"><i class="bi bi-box-arrow-up"></i> <strong>Vent. salida:</strong> ${vSalidaIni.value} - ${vSalidaFin.value}</div>
            </div>
        `;

        Swal.fire({
            title: '¿Crear nuevo turno?',
            html: `<p class="text-muted mb-3" style="font-size: 0.9rem;">Verifica los datos antes de continuar.</p>${preview}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-check-lg"></i> Sí, crear turno',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2a5298',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            customClass: {
                popup: 'swal-preview-popup',
                htmlContainer: 'swal-preview-content'
            },
            width: 520
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>