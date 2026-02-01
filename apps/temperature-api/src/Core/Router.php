<?php
/**
 * Простой маршрутизатор для приложения
 * 
 * @package App\Core
 */

namespace App\Core;

class Router
{
    /**
     * Маршруты приложения
     * 
     * @var array
     */
    private array $routes = [];

    /**
     * Добавить маршрут
     * 
     * @param string $method HTTP метод
     * @param string $path Путь маршрута
     * @param callable $handler Обработчик маршрута
     * @return self
     */
    public function add(string $method, string $path, callable $handler): self
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
        
        return $this;
    }

    /**
     * Обработать входящий запрос
     * 
     * @param string $requestMethod HTTP метод запроса
     * @param string $requestPath Путь запроса
     * @param array $queryParams Параметры запроса
     * @return Response|null Ответ или null если маршрут не найден
     */
    public function dispatch(string $requestMethod, string $requestPath, array $queryParams = []): ?Response
    {
        $requestMethod = strtoupper($requestMethod);
        
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $requestMethod, $requestPath)) {
                return call_user_func($route['handler'], $queryParams);
            }
        }
        
        return null;
    }

    /**
     * Проверить соответствие маршрута запросу
     * 
     * @param array $route Маршрут
     * @param string $requestMethod Метод запроса
     * @param string $requestPath Путь запроса
     * @return bool
     */
    private function matchRoute(array $route, string $requestMethod, string $requestPath): bool
    {
        // Проверка метода
        if ($route['method'] !== $requestMethod) {
            return false;
        }
        
        // Точное соответствие пути
        if ($route['path'] === $requestPath) {
            return true;
        }
        
        // Проверка на наличие параметров (например, /temperature/:id)
        $pattern = preg_replace('/\/:([^\/]+)/', '/([^\/]+)', $route['path']);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';
        
        return preg_match($pattern, $requestPath) === 1;
    }

    /**
     * Получить все зарегистрированные маршруты
     * 
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}