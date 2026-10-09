<?php
namespace App\Core;

class Request
{
    public static function body(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }

    public static function input(string $key, $default = null)
    {
        $body = self::body();
        return $body[$key] ?? $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function header(string $name): ?string
    {
        $headers = getallheaders();
        foreach ($headers as $k => $v) {
            if (strcasecmp($k, $name) === 0) return $v;
        }
        return null;
    }

    public static function bearerToken(): ?string
    {
        $auth = self::header('Authorization');
        if ($auth && preg_match('/Bearer\s+(.+)/', $auth, $m)) {
            return $m[1];
        }
        return null;
    }
}
?>