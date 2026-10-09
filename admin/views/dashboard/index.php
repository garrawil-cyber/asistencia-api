<?php
$titulo = 'Dashboard';
$paginaActiva = 'dashboard';
require __DIR__ . '/../layouts/header.php';
?>

<!-- ============ KPI CARDS ============ -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="kpi-card kpi-primary">
            <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
            <div class="kpi-label">Docentes Activos</div>
            <div class="kpi-value"><?= $totalDocentes ?></div>
            <div class="kpi-trend">
                <i class="bi bi-check-circle-fill"></i> Registrados en el sistema
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="kpi-card kpi-success">
            <div class="kpi-icon"><i class="bi bi-calendar-check"></i></div>
            <div class="kpi-label">Asistencias Hoy</div>
            <div class="kpi-value"><?= $asistenciasHoy ?></div>
            <div class="kpi-trend">
                <i class="bi bi-clock"></i> Marcaciones del día
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="kpi-card kpi-info">
            <div class="kpi-icon"><i class="bi bi-check2-circle"></i></div>
            <div class="kpi-label">Presentes</div>
            <div class="kpi-value"><?= $presentesHoy ?></div>
            <div class="kpi-trend">
                <i class="bi bi-arrow-up"></i> Llegaron a tiempo
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="kpi-card kpi-warning">
            <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
            <div class="kpi-label">Tardanzas</div>
            <div class="kpi-value"><?= $tardanzasHoy ?></div>
            <div class="kpi-trend">
                <i class="bi bi-exclamation-triangle"></i> Llegaron tarde
            </div>
        </div>
    </div>
</div>

<!-- ============ GRÁFICO + ACTIVIDAD ============ -->
<div class="row g-4">

    <!-- Gráfico -->
    <div class="col-lg-7">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-graph-up-arrow text-primary"></i>
                    Asistencias últimos 7 días
                </h2>
            </div>
            <div class="panel-body">
                <canvas id="graficoAsistencias" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Actividad reciente -->
    <div class="col-lg-5">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-activity text-primary"></i>
                    Actividad reciente
                </h2>
            </div>
            <div class="panel-body p-3">
                <?php if (empty($actividadReciente)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-2 small">Sin marcaciones hoy</p>
                    </div>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($actividadReciente as $a): ?>
                            <div class="activity-item">
                                <div class="activity-avatar">
                                    <?= strtoupper(substr($a['nombres'], 0, 1) . substr($a['apellidos'], 0, 1)) ?>
                                </div>
                                <div class="activity-info">
                                    <div class="activity-name">
                                        <?= htmlspecialchars($a['nombres'] . ' ' . $a['apellidos']) ?>
                                    </div>
                                    <div class="activity-time">
                                        <i class="bi bi-clock"></i>
                                        <?= date('H:i', strtotime($a['hora_entrada'])) ?>
                                        <?php if ($a['hora_salida']): ?>
                                            · Salida <?= date('H:i', strtotime($a['hora_salida'])) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="activity-badge badge-<?= $a['estado'] ?>">
                                    <?= $a['estado'] ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Script del gráfico -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('graficoAsistencias');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($grafico7Dias, 'label')) ?>,
            datasets: [{
                label: 'Asistencias',
                data: <?= json_encode(array_column($grafico7Dias, 'total')) ?>,
                backgroundColor: 'rgba(42, 82, 152, 0.7)',
                borderColor: 'rgba(42, 82, 152, 1)',
                borderWidth: 0,
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e3c72',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#6b7280' },
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    ticks: { color: '#6b7280' },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>