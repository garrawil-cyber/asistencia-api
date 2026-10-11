<?php
use App\Core\Session;
Session::start();
$usuario = Session::get('username', 'Invitado');
$rol     = Session::get('rol', '');
$titulo  = $titulo ?? 'Panel Admin';
$paginaActiva = $paginaActiva ?? 'dashboard';

// Cache busting: devuelve la fecha de modificación del archivo (o time() si no se encuentra)
// para que el navegador móvil siempre descargue la última versión de CSS/JS.
$assetV = fn(string $rel) => @filemtime($_SERVER['DOCUMENT_ROOT'] . $rel) ?: time();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> · Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="/asistencia-api/admin/assets/css/admin.css?v=<?= $assetV('/asistencia-api/admin/assets/css/admin.css') ?>" rel="stylesheet">
    <!-- Toastify para notificaciones -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</head>
<body>

<div class="app-layout">

    <!-- ============ SIDEBAR ============ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-mortarboard-fill"></i>
            <div>
                <div class="brand-title">Asistencia</div>
                <div class="brand-sub">Panel Admin</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="/asistencia-api/admin/dashboard"
               class="nav-item <?= $paginaActiva === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="/asistencia-api/admin/docentes"
               class="nav-item <?= $paginaActiva === 'docentes' ? 'active' : '' ?>">
                <i class="bi bi-people"></i>
                <span>Docentes</span>
            </a>
            <a href="/asistencia-api/admin/justificaciones"
               class="nav-item <?= $paginaActiva === 'justificaciones' ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text"></i>
                <span>Justificaciones</span>
            </a>
            <a href="/asistencia-api/admin/reportes"
               class="nav-item <?= $paginaActiva === 'reportes' ? 'active' : '' ?>">
                <i class="bi bi-graph-up"></i>
                <span>Reportes</span>
            </a>
            <a href="/asistencia-api/admin/perfil"
                class="nav-item <?= $paginaActiva === 'perfil' ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i>
                <span>Mi Perfil</span>
            </a>
            <a href="/asistencia-api/admin/logs"
                class="nav-item <?= $paginaActiva === 'logs' ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i>
                <span>Logs de Actividad</span>
            </a>

        </nav>

        <div class="sidebar-footer">
            <a href="/asistencia-api/admin/logout" class="nav-item text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
        </div>
    </aside>

    <!-- ============ CONTENIDO ============ -->
    <div class="main-wrapper">

<!-- Topbar -->
<header class="topbar">
    <div class="topbar-left">
        <button class="btn-hamburger" id="btnHamburger" type="button" aria-label="Abrir menú">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-title">
            <h1 class="page-title"><?= htmlspecialchars($titulo) ?></h1>
            <div class="page-date">
                <i class="bi bi-calendar3"></i>
                <?= \App\Core\DateHelper::fechaLarga(date('Y-m-d')) ?>
            </div>
        </div>
    </div>
    <div class="topbar-right">
        <div class="user-badge">
            <div class="user-avatar">
                <?= strtoupper(substr($usuario, 0, 1)) ?>
            </div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($usuario) ?></div>
                <div class="user-role"><?= htmlspecialchars($rol) ?></div>
            </div>
        </div>
    </div>
</header>


        <!-- Contenido de la página -->
        <main class="main-content">