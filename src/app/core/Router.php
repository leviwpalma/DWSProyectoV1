<?php

namespace App\Core;

class Router
{
    private array $rutes = [];

    public function get(string $rute, callable|array $action): void 
    {
        $this->rutes['GET'][$rute] = $action;
    }

    public function post(string $rute, callable|array $action): void
    {
        $this->rutes['POST'][$rute] = $action;
    }

    public function despachar(string $method, string $URL): void
    {
        $URL = parse_url($URL, PHP_URL_PATH);
        $URL = rtrim($URL, '/') ?: '/';

        foreach ($this->rutes[$method] ?? [] as $patron => $action){
            $regex = preg_replace('#\{[a-z_]+\}#', '([0-9]+)', $patron);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $URL, $matches)) {
                array_shift($matches);

                [$class, $controller] = $action;
                $control = new $class();
                $control->$controller(...$matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 - Ruta no encontrada: $URL";
    }
}