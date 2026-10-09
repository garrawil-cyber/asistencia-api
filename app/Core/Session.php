<?php
namespace App\Core;

class Session
{
    /**
     * Inicia la sesión si no está iniciada.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,   // No accesible desde JS → más seguro
                'cookie_samesite' => 'Lax',  // Protección CSRF básica
                'use_strict_mode' => true    // Evita session fixation
            ]);
        }
    }

    /**
     * Guarda un valor en la sesión.
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Obtiene un valor de la sesión.
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Verifica si existe una clave.
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Elimina una clave de la sesión.
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Destruye la sesión completa (logout).
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Regenera el ID de sesión (útil después del login).
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }
}