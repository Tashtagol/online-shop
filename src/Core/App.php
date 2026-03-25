<?php

namespace Core;

use Request\Request;
use Service\Logger\LoggerServiceInterface;

class App
{
    private LoggerServiceInterface $loggerService;
    private array $routes = [];
    private Container $container;

    public function __construct(LoggerServiceInterface $loggerService, Container $container)
    {
        $this->loggerService = $loggerService;
        $this->container = $container;
    }

    public function run()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $pattern => $methods) {

            if (preg_match("#^$pattern$#", $requestUri, $matches)) {

                array_shift($matches);
                $params = array_values($matches);

                $route = $methods[$requestMethod] ?? null;

                if (!$route) {
                    http_response_code(405);
                    echo "Method Not Allowed";
                    return;
                }

                $className = $route['class'];
                $methodName = $route['method'];
                $requestClass = $route['request'] ?? Request::class;

                $controller = $this->container->get($className);

                /**
                 * =========================
                 * CLEAN VARIANT 3 LOGIC
                 * =========================
                 */

                if ($requestMethod === 'POST') {

                    // создаём Request только для POST
                    $request = new $requestClass($requestMethod, $_POST);

                    call_user_func_array(
                        [$controller, $methodName],
                        array_merge($params, [$request])
                    );

                } else {

                    // GET → без Request вообще
                    call_user_func_array(
                        [$controller, $methodName],
                        $params
                    );
                }

                return;
            }
        }

        http_response_code(404);
        require_once './../View/404.php';
    }

    public function addRoute(
        string $requestUri,
        string $requestMethod,
        string $className,
        string $methodName,
        string $requestClass = Request::class
    ): void {

        $pattern = preg_replace('/\{(\w+)\}/', '(\w+)', $requestUri);

        if (!isset($this->routes[$pattern])) {
            $this->routes[$pattern] = [];
        }

        $this->routes[$pattern][$requestMethod] = [
            'class' => $className,
            'method' => $methodName,
            'request' => $requestClass
        ];
    }
}