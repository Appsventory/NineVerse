<?php

namespace App\Core;

class Router
{
    protected static $routes = [];

    public static function get($uri, $action)
    {
        return self::addRoute('GET', $uri, $action);
    }

    public static function post($uri, $action)
    {
        return self::addRoute('POST', $uri, $action);
    }

    public static function put($uri, $action)
    {
        return self::addRoute('PUT', $uri, $action);
    }

    public static function delete($uri, $action)
    {
        return self::addRoute('DELETE', $uri, $action);
    }

    public static function any($uri, $action)
    {
        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method) {
            self::addRoute($method, $uri, $action);
        }
        return new RouteEntry('ANY', $uri, $action); // dummy return for chaining
    }

    protected static function addRoute($method, $uri, $action)
    {
        $route = new RouteEntry($method, $uri, $action);
        self::$routes[$method][$uri] = $route;
        return $route;
    }

    public static function dispatch()
    {
        $uri = trim($_GET['url'] ?? '/', '/');

        // Override method untuk form HTML yang kirim POST + _method
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        } else {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        foreach (self::$routes[$method] ?? [] as $route => $routeEntry) {
            // Ubah {parameter} jadi regex
            $pattern = preg_replace('/\{[^\/]+\}/', '([^\/]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                // === Jalankan Middleware ===
                foreach ($routeEntry->middleware ?? [] as $middlewareEntry) {
                    $middlewareClass = "App\\Middleware\\";
                    $methodName = 'handle';
                    $params = [];

                    if (str_contains($middlewareEntry, '@')) {
                        // Format: Middleware@method:param1&param2
                        [$className, $rest] = explode('@', $middlewareEntry, 2);
                        $middlewareClass .= $className;

                        if (str_contains($rest, ':')) {
                            [$methodName, $paramString] = explode(':', $rest, 2);
                            $params = explode('&', $paramString);
                        } else {
                            $methodName = $rest;
                        }

                        // Panggil static method
                        if (!class_exists($middlewareClass)) throw new \Exception("Middleware class {$middlewareClass} not found.");
                        if (!method_exists($middlewareClass, $methodName)) throw new \Exception("Method {$methodName} not found in {$middlewareClass}.");
                        call_user_func_array([$middlewareClass, $methodName], $params);
                    } elseif (str_contains($middlewareEntry, '#')) {
                        // Format: Middleware#param1&param2 → call handle(param1, param2)
                        [$className, $paramString] = explode('#', $middlewareEntry, 2);
                        $middlewareClass .= $className;
                        $params = explode('&', $paramString);

                        if (!class_exists($middlewareClass)) throw new \Exception("Middleware class {$middlewareClass} not found.");
                        $instance = new $middlewareClass();
                        if (!method_exists($instance, 'handle')) throw new \Exception("handle() not found in {$middlewareClass}.");
                        call_user_func_array([$instance, 'handle'], $params);
                    } else {
                        // Default: Middleware → call handle()
                        $middlewareClass .= $middlewareEntry;
                        if (!class_exists($middlewareClass)) throw new \Exception("Middleware class {$middlewareClass} not found.");
                        $instance = new $middlewareClass();
                        if (!method_exists($instance, 'handle')) throw new \Exception("handle() not found in {$middlewareClass}.");
                        $instance->handle();
                    }
                }


                // === Jalankan Controller ===
                $action = $routeEntry->action;
                if (is_array($action)) {
                    [$controller, $methodName] = $action;
                } else {
                    [$controller, $methodName] = explode('@', $action);
                    $controller = "App\\Controllers\\{$controller}";
                }

                if (!class_exists($controller)) throw new \Exception("Controller {$controller} not found.");
                $controllerInstance = new $controller();
                if (!method_exists($controllerInstance, $methodName)) throw new \Exception("Method {$methodName} not found in {$controller}.");
                call_user_func_array([$controllerInstance, $methodName], $matches);
                return;
            }
        }

        // Jika tidak ditemukan
        http_response_code(404);
        if (class_exists('\App\Controllers\ErrorController')) {
            (new \App\Controllers\ErrorController())->index();
        } else {
            echo '404 Not Found';
        }
        exit;
    }
}
