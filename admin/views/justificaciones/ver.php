<?php
use App\Core\DateHelper;

$titulo = 'Ver Justificación';
$paginaActiva = 'justificaciones';
require __DIR__ . '/../layouts/header.php';
?>

<!-- ============ BREADCRUMB ============ -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="/asistencia-api/admin/justificaciones" class="text-decoration-none">
                <i class="bi bi-file-earmark-text"></i> Justificaciones
            </a>
        </li>
        <li class="breadcrumb-item active">Ver #<?= $justificacion['id'] ?></li>
    </ol>
</nav>

<!-- ============ ESTADO DE LA JUSTIFICACIÓN ============ -->
<?php
$estadosConfig = [
    'pendiente' => [
        'color' => 'warning',
        'icon'  => 'clock-history',
        'texto' => 'Pendiente de revisión'
    ],
    'aprobado' => [
        'color' => 'success',
        'icon'  => 'check-circle-fill',
        'texto' => 'Aprobada'
    ],
    'rechazado' => [
        'color' => 'danger',
        'icon'  => 'x-circle-fill',
        'texto' => 'Rechazada'
    ]
];
$conf = $estadosConfig[$justificacion['estado']] ?? $estadosConfig['pendiente'];
?>

<div class="alert alert-<?= $conf['color'] ?> d-flex align-items-center mb-4">
    <i class="bi bi-<?= $conf['icon'] ?> fs-3 me-3"></i>
    <div class="flex-grow-1">
        <strong>Estado:</strong> <?= $conf['texto'] ?>
        <?php if ($justificacion['estado'] !== 'pendiente' && $justificacion['fecha_respuesta']): ?>
            <br>
            <small>
                Respondida el <?= date('d/m/Y H:i', strtotime($justificacion['fecha_respuesta'])) ?>
                por <strong><?= htmlspecialchars($justificacion['aprobador_username'] ?? '—') ?></strong>
            </small>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- ============ COLUMNA IZQUIERDA: Datos del docente ============ -->
    <div class="col-lg-4">
        <div class="panel">
            <div class="panel-body text-center py-4">
                <div class="profile-avatar mx-auto mb-3">
                    <?= strtoupper(substr($justificacion['nombres'], 0, 1) . substr($justificacion['apellidos'], 0, 1)) ?>
                </div>
                <h4 class="mb-1">
                    <?= htmlspecialchars($justificacion['nombres'] . ' ' . $justificacion['apellidos']) ?>
                </h4>
                <span class="badge bg-primary mb-3">
                    <code><?= htmlspecialchars($justificacion['dni']) ?></code>
                </span>

                <div class="profile-info text-start mt-4">
                    <div class="profile-info-item">
                        <i class="bi bi-book"></i>
                        <div>
                            <small class="text-muted d-block">Especialidad</small>
                            <strong><?= htmlspecialchars($justificacion['especialidad'] ?? '—') ?></strong>
                        </div>
                    </div>

                    <div class="profile-info-item">
                        <i class="bi bi-calendar-event"></i>
                        <div>
                            <small class="text-muted d-block">Fecha de falta</small>
                            <strong><?= DateHelper::fechaLarga($justificacion['asistencia_fecha']) ?></strong>
                        </div>
                    </div>

                    <div class="profile-info-item">
                        <i class="bi bi-flag"></i>
                        <div>
                            <small class="text-muted d-block">Estado de asistencia</small>
                            <strong><?= ucfirst($justificacion['asistencia_estado']) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Datos de la asistencia -->
        <div class="panel mt-4">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-clipboard-check text-primary"></i>
                    Asistencia del día
                </h2>
            </div>
            <div class="panel-body">
                <div class="row g-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Entrada</small>
                        <strong class="text-success">
                            <?= $justificacion['hora_entrada']
                                ? date('H:i', strtotime($justificacion['hora_entrada']))
                                : '—' ?>
                        </strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Salida</small>
                        <strong class="text-primary">
                            <?= $justificacion['hora_salida']
                                ? date('H:i', strtotime($justificacion['hora_salida']))
                                : '—' ?>
                        </strong>
                    </div>
                    <?php if ($justificacion['minutos_tardanza'] > 0): ?>
                        <div class="col-12">
                            <small class="text-muted d-block">Tardanza</small>
                            <strong class="text-warning">
                                ⏱ <?= $justificacion['minutos_tardanza'] ?> min
                            </strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ COLUMNA DERECHA: Motivo + Acciones ============ -->
    <div class="col-lg-8">
        <!-- Motivo -->
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-chat-left-text text-primary"></i>
                    Motivo de la justificación
                </h2>
                <small class="text-muted">
                    Solicitada el <?= date('d/m/Y H:i', strtotime($justificacion['fecha_solicitud'])) ?>
                </small>
            </div>
            <div class="panel-body">
                <p class="mb-0" style="white-space: pre-wrap; line-height: 1.6;">
                    <?= htmlspecialchars($justificacion['motivo']) ?>
                </p>
            </div>
        </div>

        <!-- Archivo adjunto -->
        <?php if (!empty($justificacion['archivo_url'])): ?>
        <div class="panel mt-4">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-paperclip text-primary"></i>
                    Archivo adjunto
                </h2>
            </div>
            <div class="panel-body">
                <?php
                $extension = strtolower(pathinfo($justificacion['archivo_url'], PATHINFO_EXTENSION));
                $esImagen = in_array($extension, ['jpg', 'jpeg', 'png']);
                ?>
                <?php if ($esImagen): ?>
                    <img src="/asistencia-api/public<?= htmlspecialchars($justificacion['archivo_url']) ?>"
                         alt="Comprobante"
                         class="img-fluid rounded"
                         style="max-height: 400px;">
                <?php else: ?>
                    <a href="/asistencia-api/public<?= htmlspecialchars($justificacion['archivo_url']) ?>"
                       target="_blank"
                       class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-pdf"></i>
                        Ver documento PDF
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Acciones (solo si está pendiente) -->
        <?php if ($justificacion['estado'] === 'pendiente'): ?>
        <div class="panel mt-4">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-check2-square text-primary"></i>
                    Decisión
                </h2>
            </div>
            <div class="panel-body">
                <p class="text-muted mb-3">
                    Revisa el motivo y el adjunto. Luego toma una decisión.
                </p>

                <div class="d-flex gap-3">
                    <button type="button"
                            class="btn btn-success btn-lg flex-grow-1"
                            data-action="responder"
                            data-id="<?= $justificacion['id'] ?>"
                            data-accion="aprobado"
                            data-nombre="<?= htmlspecialchars($justificacion['nombres'] . ' ' . $justificacion['apellidos']) ?>">
                        <i class="bi bi-check-circle"></i> Aprobar
                    </button>

                    <button type="button"
                            class="btn btn-danger btn-lg flex-grow-1"
                            data-action="responder"
                            data-id="<?= $justificacion['id'] ?>"
                            data-accion="rechazado"
                            data-nombre="<?= htmlspecialchars($justificacion['nombres'] . ' ' . $justificacion['apellidos']) ?>">
                        <i class="bi bi-x-circle"></i> Rechazar
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>