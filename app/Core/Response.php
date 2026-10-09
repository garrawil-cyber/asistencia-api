<?php
namespace App\Core;

class Response
{
    public static function json($data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function success($data = null, string $msg = 'OK'): void
    {
        self::json(['success' => true, 'message' => $msg, 'data' => $data]);
    }

    public static function error(string $msg, int $code = 400, $extra = null): void
    {
        $body = ['success' => false, 'message' => $msg];
        if ($extra) $body['errors'] = $extra;
        self::json($body, $code);
    }
}
?>