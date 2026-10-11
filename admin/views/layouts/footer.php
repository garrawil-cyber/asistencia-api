</main>

        <footer class="main-footer">
            <span>© <?= date('Y') ?> · Sistema de Asistencia Docente</span>
            <span class="text-muted">v1.0</span>
        </footer>

    </div>    <!-- /main-wrapper -->

</div><!-- /app-layout -->

<!-- Overlay para móvil -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<?php
// Por si el footer se incluye sin haber pasado por header.php
$assetV = $assetV ?? fn(string $rel) => @filemtime($_SERVER['DOCUMENT_ROOT'] . $rel) ?: time();
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="/asistencia-api/admin/assets/js/admin.js?v=<?= $assetV('/asistencia-api/admin/assets/js/admin.js') ?>"></script>
</body>
</html>