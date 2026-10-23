<?php
namespace App\Admin\Controllers;

use App\Admin\Middleware\SessionMiddleware;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Models\Usuario;

class BackupController
{
    private string $backupDir;
    private string $dbHost;
    private string $dbName;
    private string $dbUser;
    private string $dbPass;
    private string $mysqldumpPath = 'C:\wamp64\bin\mysql\mysql9.1.0\bin\mysqldump.exe'; // ⚠️ Ajusta tu versión
    private string $mysqlPath = 'C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe'; // ⚠️ Ajusta tu versión

    public function __construct()
    {
        $config = require __DIR__ . '/../../../config/config.php';
        $this->dbHost = $config['db']['host'];
        $this->dbName = $config['db']['name'];
        $this->dbUser = $config['db']['user'];
        $this->dbPass = $config['db']['pass'];
        
        $this->backupDir = __DIR__ . '/../../../backups/';
        
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0777, true);
        }
    }

    public function index(): void
    {
        $user = SessionMiddleware::check();

        // Filtros
        $busqueda = trim(Request::input('q', ''));
        $tipo     = Request::input('tipo', ''); // 'manual' o 'automatico'

        // Obtener archivos
        $archivos = glob($this->backupDir . '*.sql');
        $backups = [];

        foreach ($archivos as $archivo) {
            $nombre = basename($archivo);
            
            // Extraer info del nombre: backup_manual_2026-10-23_11-32-55.sql
            $partes = explode('_', $nombre);
            $tipoArchivo = $partes[1] ?? 'desconocido';
            
            // Extraer la fecha y hora del nombre del archivo
            $fechaStr = $partes[2] . ' ' . str_replace('-', ':', $partes[3]); 
            // $fechaStr = "2026-10-23 11:32:55"
            
            // Reemplazar el '.sql' del final
            $fechaStr = str_replace('.sql', '', $fechaStr);
            
            $fechaFormateada = date('d/m/Y H:i:s', strtotime($fechaStr));

            // Aplicar filtros
            if ($busqueda !== '' && stripos($nombre, $busqueda) === false) continue;
            if ($tipo !== '' && $tipoArchivo !== $tipo) continue;

            $backups[] = [
                'nombre' => $nombre,
                'tipo' => $tipoArchivo,
                'tamano_bytes' => filesize($archivo),
                'tamano' => $this->formatBytes(filesize($archivo)),
                'fecha_creacion' => $fechaFormateada,
                'archivo_path' => $archivo
            ];
        }

        // Ordenar por fecha descendente
        usort($backups, fn($a, $b) => strcmp($b['nombre'], $a['nombre']));

        $flashExito = Session::get('flash_exito');
        $flashError = Session::get('flash_error');
        Session::remove('flash_exito');
        Session::remove('flash_error');

        extract([
            'user' => $user,
            'backups' => $backups,
            'busqueda' => $busqueda,
            'tipo' => $tipo,
            'flashExito' => $flashExito,
            'flashError' => $flashError,
            'paginaActiva' => 'backups'
        ]);
        
        require __DIR__ . '/../../../admin/views/backups/index.php';
    }

    public function crear(): void
    {
        SessionMiddleware::check();
        $fecha = date('Y-m-d_H-i-s');
        $nombreArchivo = "backup_manual_{$fecha}.sql";
        $rutaCompleta = $this->backupDir . $nombreArchivo;

        // Comando mysqldump
        $cmd = sprintf(
            '"%s" --host=%s --user=%s %s %s > "%s" 2>&1',
            $this->mysqldumpPath,
            $this->dbHost,
            $this->dbUser,
            empty($this->dbPass) ? '' : '--password=' . $this->dbPass,
            $this->dbName,
            $rutaCompleta
        );

        exec($cmd, $output, $returnVar);

        if ($returnVar === 0 && file_exists($rutaCompleta)) {
            Usuario::registrarLog((int)Session::get('usuario_id'), 'crear_backup', 'Creó backup: ' . $nombreArchivo);
            Session::set('flash_exito', 'Backup creado exitosamente.');
        } else {
            Session::set('flash_error', 'Error al crear el backup. Verifica la ruta de mysqldump.');
        }

        header('Location: /asistencia-api/admin/backups');
        exit;
    }

    public function descargar(string $nombre): void
    {
        SessionMiddleware::check();
        $ruta = $this->backupDir . basename($nombre);

        if (file_exists($ruta)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($ruta) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($ruta));
            readfile($ruta);
            exit;
        }
        Session::set('flash_error', 'Archivo no encontrado.');
        header('Location: /asistencia-api/admin/backups');
    }

    public function eliminar(string $nombre): void
    {
        SessionMiddleware::check();
        $ruta = $this->backupDir . basename($nombre);

        if (file_exists($ruta)) {
            unlink($ruta);
            Usuario::registrarLog((int)Session::get('usuario_id'), 'eliminar_backup', 'Eliminó backup: ' . $nombre);
            Session::set('flash_exito', 'Backup eliminado.');
        } else {
            Session::set('flash_error', 'El archivo no existe.');
        }
        header('Location: /asistencia-api/admin/backups');
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}