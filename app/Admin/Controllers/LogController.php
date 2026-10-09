<?php
namespace App\Admin\Controllers;

use App\Admin\Middleware\SessionMiddleware;
use App\Core\Request;
use App\Models\Usuario;

class LogController
{
    /**
     * GET /admin/logs
     */
    public function index(): void
    {
        $user = SessionMiddleware::check();

        $usuarioId = (int)Request::input('usuario_id', 0);
        $desde     = Request::input('desde', date('Y-m-d', strtotime('-7 days')));
        $hasta     = Request::input('hasta', date('Y-m-d'));
        $pagina    = max(1, (int)Request::input('pagina', 1));
        $porPag    = (int)Request::input('por_pagina', 30);
        $porPag    = in_array($porPag, [10, 15, 25, 50, 100]) ? $porPag : 30;
        $offset    = ($pagina - 1) * $porPag;

        if ($desde > $hasta) [$desde, $hasta] = [$hasta, $desde];

        $logs     = Usuario::listarLogs($porPag, $offset, $usuarioId, $desde, $hasta);
        $total    = Usuario::contarLogs($usuarioId, $desde, $hasta);
        $paginas  = (int)ceil($total / $porPag);
        $usuarios = Usuario::listarTodos();

        $datos = [
            'user'         => $user,
            'logs'         => $logs,
            'total'        => $total,
            'pagina'       => $pagina,
            'paginas'      => $paginas,
            'porPag'       => $porPag,
            'usuarioId'    => $usuarioId,
            'desde'        => $desde,
            'hasta'        => $hasta,
            'usuarios'     => $usuarios,
            'paginaActiva' => 'logs'
        ];

        extract($datos);
        require __DIR__ . '/../../../admin/views/logs/index.php';
    }
}