<?php
declare(strict_types=1);

// Bootstrap: cargar autoload de Composer
require __DIR__ . '/../vendor/autoload.php';

// Cargar config
$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set($config['app']['timezone']);

// Iniciar sesión PHP
\App\Core\Session::start();

// -------- Rutas del panel --------
use App\Core\Router;
use App\Admin\Controllers\AdminAuthController;
use App\Admin\Controllers\DashboardController;
use App\Admin\Controllers\DocenteController;
use App\Admin\Controllers\ReporteController;

use App\Admin\Controllers\PerfilController;
use App\Admin\Controllers\LogController;
use App\Admin\Controllers\JustificacionController;

$router = new Router();

// Auth del panel
$router->get ('/admin/login',     fn() => (new AdminAuthController())->showLogin());
$router->post('/admin/login',     fn() => (new AdminAuthController())->login());
$router->get ('/admin/logout',    fn() => (new AdminAuthController())->logout());

// Dashboard
$router->get ('/admin/dashboard', fn() => (new DashboardController())->index());
$router->get ('/admin',           fn() => (new DashboardController())->index());

// Docentes (CRUD)
$router->get ('/admin/docentes',                  fn() => (new DocenteController())->index());
$router->get ('/admin/docentes/crear',            fn() => (new DocenteController())->mostrarCrear());
$router->post('/admin/docentes/crear',            fn() => (new DocenteController())->crear());
$router->get ('/admin/docentes/editar/{id}',      fn($id) => (new DocenteController())->mostrarEditar($id));
$router->post('/admin/docentes/editar/{id}',      fn($id) => (new DocenteController())->editar($id));
$router->post('/admin/docentes/toggle/{id}',      fn($id) => (new DocenteController())->toggle($id));

// Reportes
$router->get ('/admin/reportes',              fn() => (new ReporteController())->index());
$router->get ('/admin/reportes/exportar',     fn() => (new ReporteController())->exportar());
$router->get ('/admin/reportes/exportar-pdf', fn() => (new ReporteController())->exportarPdf());

// Perfil
$router->get ('/admin/perfil',                   fn() => (new PerfilController())->index());
$router->post('/admin/perfil/cambiar-password',  fn() => (new PerfilController())->cambiarPassword());

// Logs de actividad
$router->get ('/admin/logs', fn() => (new LogController())->index());

// Justificaciones
$router->get ('/admin/justificaciones',              fn() => (new JustificacionController())->index());
$router->get ('/admin/justificaciones/crear',        fn() => (new JustificacionController())->mostrarCrear());
$router->post('/admin/justificaciones/crear',        fn() => (new JustificacionController())->crear());
$router->get ('/admin/justificaciones/ver/{id}',     fn($id) => (new JustificacionController())->ver($id));
$router->post('/admin/justificaciones/responder/{id}', fn($id) => (new JustificacionController())->responder($id));

// -------- Dispatch --------
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);