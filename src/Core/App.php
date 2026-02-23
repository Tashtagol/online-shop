<?php
namespace Core;

class App
{
    private array $routes = [];

    public function run()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Проверка существования маршрута
        if (!isset($this->routes[$requestUri])) {
            http_response_code(404);
            require_once './../View/404.php';
            return;
        }

        // Проверка поддержки метода
        if (!isset($this->routes[$requestUri][$requestMethod])) {
            echo "$requestMethod не поддерживается адресом $requestUri";
            return;
        }

        $route = $this->routes[$requestUri][$requestMethod];
        $className =  $route['class'];
        $methodName = $route['method'];

        $controller = new $className();

        // Если POST — передаём данные
        if ($requestMethod === 'POST') {
            $controller->$methodName($_POST);
        } else {
            $controller->$methodName();
        }
    }

    public function addRoute(string $requestUri, string $requestMethod,string $className,string $methodName): void
    {
        if(!isset($this->routes[$requestUri][$requestMethod]))
        {
            $this->routes[$requestUri][$requestMethod]['class'] = $className;
            $this->routes[$requestUri][$requestMethod]['method'] = $methodName;
        } else {
            echo "$requestMethod уже зарегестрирован для $requestUri" . "<br>";
        }
    }
}
