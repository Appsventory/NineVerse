<?php

namespace App\Core;

class Request
{
    protected static ?array $jsonCache = null;

    protected static function sanitize(mixed $data): mixed
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }

        return is_string($data) ? htmlspecialchars($data, ENT_QUOTES, 'UTF-8') : $data;
    }

    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $pos = strpos($uri, '?');
        return $pos === false ? $uri : substr($uri, 0, $pos);
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        $data = self::all();
        return $data[$key] ?? $default;
    }

    public static function post(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? self::sanitize($_POST[$key]) : $default;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? self::sanitize($_GET[$key]) : $default;
    }

    public static function all(): array
    {
        $contentType = self::contentType();

        if (str_starts_with($contentType, 'application/json')) {
            return self::json();
        }

        return self::sanitize($_REQUEST);
    }

    public static function only(array $keys): array
    {
        $data = self::all();
        return array_intersect_key($data, array_flip($keys));
    }

    public static function has(string $key): bool
    {
        return isset(self::all()[$key]);
    }

    public static function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public static function is(string $method): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === strtoupper($method);
    }

    public static function contentType(): string
    {
        return $_SERVER['CONTENT_TYPE'] ?? '';
    }

    public static function json(): array
    {
        if (self::$jsonCache !== null) {
            return self::$jsonCache;
        }

        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw, true);

        self::$jsonCache = is_array($decoded) ? self::sanitize($decoded) : [];
        return self::$jsonCache;
    }

    public static function raw(): string
    {
        return file_get_contents('php://input');
    }
}
