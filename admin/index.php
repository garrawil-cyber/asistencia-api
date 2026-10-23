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
use App\Admin\Controllers\TurnoController;  

use App\Admin\Controllers\BackupController;
use App\Admin\Controllers\UsuarioController;

$router = new Router();

// Backups
$router->get ('/admin/backups',              fn() => (new BackupController())->index());
$router->post('/admin/backups/crear',        fn() => (new BackupController())->crear());
$router->get ('/admin/backups/descargar/{nombre}', fn($nombre) => (new BackupController())->descargar($nombre));
$router->post('/admin/backups/eliminar/{nombre}',  fn($nombre) => (new BackupController())->eliminar($nombre));



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

// Turnos
$router->get ('/admin/turnos',              fn() => (new TurnoController())->index());
$router->get ('/admin/turnos/crear',        fn() => (new TurnoController())->mostrarCrear());
$router->post('/admin/turnos/crear',        fn() => (new TurnoController())->crear());
$router->get ('/admin/turnos/editar/{id}',  fn($id) => (new TurnoController())->mostrarEditar($id));
$router->post('/admin/turnos/editar/{id}',  fn($id) => (new TurnoController())->editar($id));
$router->post('/admin/turnos/toggle/{id}',  fn($id) => (new TurnoController())->toggle($id));

// Usuarios
$router->get ('/admin/usuarios',           fn() => (new UsuarioController())->index());
$router->post('/admin/usuarios/crear',     fn() => (new UsuarioController())->crear());
$router->post('/admin/usuarios/editar/{id}', fn($id) => (new UsuarioController())->editar($id));
$router->post('/admin/usuarios/toggle/{id}', fn($id) => (new UsuarioController())->toggle($id));

// -------- Dispatch --------
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);