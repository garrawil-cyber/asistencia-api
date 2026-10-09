<?php
namespace App\Core;

class Config
{
    private static ?array $data = null;

    public static function get(string $key = null, $default = null)
    {
        if (self::$data === null) {
            self::$data = require __DIR__ . '/../../config/config.php';
        }
        if ($key === null) return self::$data;

        $parts = explode('.', $key);
        $value = self::$data;
        foreach ($parts as $p) {
            if (!isset($value[$p])) return $default;
            $value = $value[$p];
        }
        return $value;
    }
}