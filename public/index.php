<?php
declare(strict_types=1);

// ============================================
// 1. Zona horaria ANTES de cualquier uso de DateTime
// ============================================
$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set($config['app']['timezone']);

// ============================================
// 2. Autoload de Composer
// ============================================
require __DIR__ . '/../vendor/autoload.php';

// ============================================
// 3. CORS
// ============================================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

// ============================================
// 4. Manejo global de errores → siempre JSON
// ============================================
set_exception_handler(function (\Throwable $e) use ($config) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');

    $respuesta = [
        'success' => false,
        'message' => 'Error interno del servidor'
    ];

    // En modo debug mostramos detalles; en producción NO
    if (!empty($config['app']['debug'])) {
        $respuesta['debug'] = [
            'tipo'    => get_class($e),
            'mensaje' => $e->getMessage(),
            'archivo' => $e->getFile(),
            'linea'   => $e->getLine()
        ];
    }

    // Loguear el error siempre
    error_log(sprintf(
        '[API ERROR] %s: %s en %s:%d',
        get_class($e),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));

    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
});

// ============================================
// 5. Router
// ============================================
use App\Core\Router;
use App\Core\Response;
use App\Controllers\AuthController;
use App\Controllers\AsistenciaController;
use App\Controllers\QrController;

$router = new Router();

// -------- Health check (útil para saber que la API responde) --------
$router->get('/', function () {
    Response::success([
        'api'     => 'Control de Asistencia Docente',
        'version' => '1.0.0',
        'estado'  => 'operativo'
    ], 'API activa');
});

// -------- Auth --------
$router->post('/api/v1/auth/login',  fn() => (new AuthController())->login());
$router->post('/api/v1/auth/logout', fn() => (new AuthController())->logout());
$router->get ('/api/v1/auth/me',     fn() => (new AuthController())->me());

// -------- Asistencia --------
$router->post('/api/v1/asistencia/marcar',    fn() => (new AsistenciaController())->marcar());
$router->get ('/api/v1/asistencia/hoy',       fn() => (new AsistenciaController())->hoy());
$router->get ('/api/v1/asistencia/historial', fn() => (new AsistenciaController())->historial());

// -------- QR --------
$router->post('/api/v1/qr/generar', fn() => (new QrController())->generar());
$router->get ('/api/v1/qr/actual',  fn() => (new QrController())->actual());
$router->get ('/api/v1/qr/imagen',  fn() => (new QrController())->imagen());

// -------- Dispatch --------
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);