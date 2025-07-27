<?php

namespace App\Core;

class CsrfToken
{
    public static function generate(): string
    {
        Session::start();
        $token = bin2hex(random_bytes(32));
        $_SESSION['_csrf_token'] = $token;
        return $token;
    }

    public static function get(): ?string
    {
        Session::start();
        return $_SESSION['_csrf_token'] ?? null;
    }

    public static function validate(): void
    {
        Session::start();
        $token = $_POST['_token'] ?? '';
        $sessionToken = $_SESSION['_csrf_token'] ?? null;

        if (!$sessionToken || !hash_equals($sessionToken, $token)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        // Optional: regenerate token after validation
        unset($_SESSION['_csrf_token']);
    }

    public static function input(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="_token" value="' . $token . '">';
    }
}
