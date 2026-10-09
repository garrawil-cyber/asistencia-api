<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\QrToken;
use App\Middleware\AuthMiddleware;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

class QrController
{
    /**
     * POST /api/v1/qr/generar
     * Genera un nuevo token QR (lo llama el kiosko).
     * Requiere API Key del kiosko.
     */
    public function generar(): void
    {
        AuthMiddleware::checkKiosko();   // FIX #1

        $db       = Database::getConnection();
        $duracion = (int)$db->query(
            "SELECT valor FROM configuracion WHERE clave='qr_duracion_segundos'"
        )->fetchColumn() ?: 60;

        $qr = QrToken::generar($duracion);

        $urlImagen = "/api/v1/qr/imagen?token=" . urlencode($qr['token']);

        Response::success([
            'token'      => $qr['token'],
            'contenido'  => $qr['token'],
            'imagen_url' => $urlImagen,
            'expira_en'  => $qr['expira_en'],
            'segundos'   => $duracion
        ], 'QR generado');
    }

    /**
     * GET /api/v1/qr/imagen?token=xxx
     * Devuelve el PNG del QR. Público (el kiosko lo muestra en <img>).
     * FIX #5: valida formato del token (hex de 64 chars).
     */
    public function imagen(): void
    {
        $token = Request::input('token', '');

        if (!$token || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            http_response_code(400);
            exit('Token inválido');
        }

        $qrCode = new QrCode(
            data: $token,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 400,
            margin: 20,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        header('Content-Type: ' . $result->getMimeType());
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        echo $result->getString();
        exit;
    }

    /**
     * GET /api/v1/qr/actual
     * Devuelve el QR vigente. Si no hay o expira pronto, genera uno nuevo.
     * Requiere API Key del kiosko.  FIX #1
     */
    public function actual(): void
    {
        AuthMiddleware::checkKiosko();   // FIX #1

        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT token, expira_en,
                   TIMESTAMPDIFF(SECOND, NOW(), expira_en) AS segundos_restantes
            FROM qr_tokens
            WHERE expira_en > NOW() AND usado = 0
            ORDER BY id DESC
            LIMIT 1
        ");
        $qr = $stmt->fetch();

        // FIX #6: solo generar si NO hay QR vigente.
        // Dejamos que el QR anterior siga sirviendo hasta que expire,
        // evitando el thrashing de generar uno nuevo cada segundo.
        if (!$qr) {
            $duracion = (int)$db->query(
                "SELECT valor FROM configuracion WHERE clave='qr_duracion_segundos'"
            )->fetchColumn() ?: 60;

            $nuevo = QrToken::generar($duracion);

            Response::success([
                'token'      => $nuevo['token'],
                'imagen_url' => "/api/v1/qr/imagen?token=" . urlencode($nuevo['token']),
                'expira_en'  => $nuevo['expira_en'],
                'segundos'   => $duracion,
                'nuevo'      => true
            ]);
        }

        Response::success([
            'token'      => $qr['token'],
            'imagen_url' => "/api/v1/qr/imagen?token=" . urlencode($qr['token']),
            'expira_en'  => $qr['expira_en'],
            'segundos'   => (int)$qr['segundos_restantes'],
            'nuevo'      => false
        ]);
    }
}