<?php
namespace Core;

use PSpell\Config;
use Request\Request;
use Service\Logger\LoggerServiceInterface;

class App
{
    private LoggerServiceInterface $loggerService;
    private array $routes = [];
    private Container $container;

    public function __construct(LoggerServiceInterface $loggerService,Container $container)
    {
        $this->loggerService = $loggerService;
        $this->container = $container;
    }

    public function run()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Паттерн для поиска динамических параметров в маршруте
        foreach ($this->routes as $pattern => $methods) {
            if (preg_match("#^$pattern$#", $requestUri, $matches)) {
                // Если совпал маршрут, передаем параметры в метод контроллера
                array_shift($matches); // Убираем сам URL
                $params = array_values($matches); // Параметры из URL

                // Получаем маршрут и его метод
                $route = $methods[$requestMethod] ?? null;
                if ($route) {
                    $className = $route['class'];
                    $methodName = $route['method'];
                    $requestClass = $route['request'] ?? \Request\Request::class;

                    // Создаем объект запроса
                    $request = new $requestClass($requestMethod, $requestUri, $_POST);
                    // Получаем контроллер
                    $controller = $this->container->get($className);
                    // Передаем параметры в метод контроллера
                    call_user_func_array([$controller, $methodName], array_merge($params, [$request]));

                    return; // Завершаем выполнение, так как маршрут найден и обработан
                }
            }
        }

        // Если маршрут не найден, возвращаем 404
        http_response_code(404);
        require_once './../View/404.php';
    }

    public function addRoute(string $requestUri, string $requestMethod, string $className, string $methodName, string $requestClass = Request::class): void
    {
        // Паттерн для динамических маршрутов
        $pattern = preg_replace('/\{(\w+)\}/', '(\w+)', $requestUri);

        if (!isset($this->routes[$pattern])) {
            $this->routes[$pattern] = [];
        }

        if (!isset($this->routes[$pattern][$requestMethod])) {
            $this->routes[$pattern][$requestMethod] = [
                'class' => $className,
                'method' => $methodName,
                'request' => $requestClass
            ];
        } else {
            echo "$requestMethod уже зарегистрирован для маршрута $requestUri" . "<br>";
        }
    }
}
