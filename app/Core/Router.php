<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function get(string $path, callable $h): void  { $this->add('GET', $path, $h); }
    public function post(string $path, callable $h): void { $this->add('POST', $path, $h); }
    public function put(string $path, callable $h): void  { $this->add('PUT', $path, $h); }
    public function delete(string $path, callable $h): void { $this->add('DELETE', $path, $h); }

    public function dispatch(string $method, string $uri): void
    {
        // 1) Quitar query string
        $uri = parse_url($uri, PHP_URL_PATH);

        // 2) Normalizar: quitar el prefijo del directorio raíz del proyecto
        //    Ej: /asistencia-api/api/v1/qr/actual  →  /api/v1/qr/actual
        $basePath = $this->detectarBasePath();
        if ($basePath !== '' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        // 3) Quitar barra final (excepto en "/")
        $uri = rtrim($uri, '/') ?: '/';

        $rutasDisponibles = [];

        foreach ($this->routes as $route) {
            $rutasDisponibles[] = $route['method'] . ' ' . $route['path'];

            $pattern = preg_replace('#\{([a-z_]+)\}#', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func_array($route['handler'], $params);
                return;
            }
        }

        // Diagnóstico
        Response::json([
            'success' => false,
            'message' => 'Ruta no encontrada',
            'debug'   => [
                'metodo_recibido'    => $method,
                'uri_original'       => $_SERVER['REQUEST_URI'] ?? '?',
                'uri_normalizada'    => $uri,
                'base_path_detectado'=> $basePath,
                'rutas_disponibles'  => $rutasDisponibles
            ]
        ], 404);
    }

/**
 * Detecta el prefijo del proyecto basándose en SCRIPT_NAME.
 *
 * Soporta múltiples puntos de entrada del proyecto:
 *   /asistencia-api/public/index.php   →  /asistencia-api
 *   /asistencia-api/admin/index.php    →  /asistencia-api
 *   /asistencia-api/api/index.php      →  /asistencia-api  (futuro)
 *   /index.php                         →  ""                (raíz)
 */
private function detectarBasePath(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = dirname($scriptName);

    // Puntos de entrada conocidos del proyecto
    $entryPoints = ['/public', '/admin', '/api'];

    foreach ($entryPoints as $entry) {
        if (str_ends_with($dir, $entry)) {
            return substr($dir, 0, -strlen($entry));
        }
    }

    // Si no coincide con ningún punto de entrada, el dir es la raíz
    return ($dir === '/' || $dir === '') ? '' : $dir;
}

}