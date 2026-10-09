<?php
namespace App\Core;

class Paginator
{
    /**
     * Renderiza el footer de paginación completo.
     *
     * @param int    $pagina      Página actual (1-indexed)
     * @param int    $paginas     Total de páginas
     * @param int    $total       Total de registros
     * @param int    $mostrando   Registros mostrados en la página actual
     * @param int    $porPagina   Registros por página
     * @param string $baseUrl     URL base sin query string (ej: /admin/logs)
     * @param array  $queryParams Parámetros de filtros para conservar
     */
    public static function render(
        int $pagina,
        int $paginas,
        int $total,
        int $mostrando,
        int $porPagina,
        string $baseUrl,
        array $queryParams = []
    ): string {
        if ($paginas <= 1 && $total <= $porPagina) {
            // No hay nada que paginar, solo mostrar el contador simple
            return '';
        }

        // Parámetros base sin 'pagina' ni 'por_pagina'
        $qsBase = $queryParams;
        unset($qsBase['pagina'], $qsBase['por_pagina']);
        $qsString = http_build_query($qsBase);
        $qsSuffix = $qsString ? '&' . $qsString : '';

        // Helpers
        $url = function (int $p, int $pp) use ($baseUrl, $qsSuffix) {
            return $baseUrl . '?pagina=' . $p . '&por_pagina=' . $pp . $qsSuffix;
        };

        // Calcular rango
        $desde = ($pagina - 1) * $porPagina + 1;
        $hasta = $desde + $mostrando - 1;

        ob_start();
        ?>
        <div class="panel-footer pagination-footer">
            <div class="pagination-info">
                Mostrando <strong><?= $desde ?>-<?= $hasta ?></strong>
                de <strong><?= $total ?></strong> registros
            </div>

            <div class="pagination-controls">

                <!-- Selector de registros por página -->
                <div class="pagination-per-page">
                    <label>Mostrar:</label>
                    <select class="form-select form-select-sm"
                            onchange="cambiarPorPagina(this.value)">
                        <?php foreach ([10, 15, 25, 50, 100] as $pp): ?>
                            <option value="<?= $pp ?>" <?= $porPagina === $pp ? 'selected' : '' ?>>
                                <?= $pp ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Botones de navegación -->
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <!-- Primera página -->
                        <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $pagina > 1 ? $url(1, $porPagina) : '#' ?>"
                               title="Primera página">
                                <i class="bi bi-chevron-double-left"></i>
                            </a>
                        </li>

                        <!-- Anterior -->
                        <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $pagina > 1 ? $url($pagina - 1, $porPagina) : '#' ?>"
                               title="Anterior">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <!-- Números de página -->
                        <?php
                        $inicio = max(1, $pagina - 2);
                        $fin    = min($paginas, $pagina + 2);

                        // Si estamos cerca del inicio, mostrar más adelante
                        if ($inicio <= 3) $fin = min($paginas, 5);

                        // Si estamos cerca del final, mostrar más atrás
                        if ($fin >= $paginas - 2) $inicio = max(1, $paginas - 4);

                        // Primera página si no está visible
                        if ($inicio > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $url(1, $porPagina) ?>">1</a>
                            </li>
                            <?php if ($inicio > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">…</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($p = $inicio; $p <= $fin; $p++): ?>
                            <li class="page-item <?= $p === $pagina ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $url($p, $porPagina) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Última página si no está visible -->
                        <?php if ($fin < $paginas): ?>
                            <?php if ($fin < $paginas - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">…</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $url($paginas, $porPagina) ?>"><?= $paginas ?></a>
                            </li>
                        <?php endif; ?>

                        <!-- Siguiente -->
                        <li class="page-item <?= $pagina >= $paginas ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $pagina < $paginas ? $url($pagina + 1, $porPagina) : '#' ?>"
                               title="Siguiente">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>

                        <!-- Última página -->
                        <li class="page-item <?= $pagina >= $paginas ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $pagina < $paginas ? $url($paginas, $porPagina) : '#' ?>"
                               title="Última página">
                                <i class="bi bi-chevron-double-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Script para el selector de registros por página -->
        <script>
        function cambiarPorPagina(nuevoValor) {
            const url = new URL(window.location.href);
            url.searchParams.set('por_pagina', nuevoValor);
            url.searchParams.set('pagina', '1');
            window.location.href = url.toString();
        }
        </script>
        <?php
        return ob_get_clean();
    }
}