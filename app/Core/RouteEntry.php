<?php

namespace App\Core;

class RouteEntry
{
    public $method;
    public $uri;
    public $action;
    public $middleware = [];

    public function __construct($method, $uri, $action)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
    }

    public function middleware($name)
    {
        $this->middleware[] = $name;
        return $this;
    }
}
