<?php
/**
 * Точка входа в приложение Temperature API
 * 
 * @package App
 */

// Запуск приложения
try {
    // Инициализация приложения
    $config = require __DIR__ . '/../src/bootstrap.php';
    
    // Создание сервисов
    $temperatureService = new App\Services\TemperatureGeneratorService($config);
    
    // Создание контроллеров
    $temperatureController = new App\Controllers\TemperatureController($temperatureService);
    $healthController = new App\Controllers\HealthController($config);
    
    // Инициализация маршрутизатора
    $router = new App\Core\Router();
    
    // Регистрация маршрутов
    $router->add('GET', '/temperature', [$temperatureController, 'getTemperature']);
    $router->add('OPTIONS', '/temperature', [$temperatureController, 'options']);
    
    $router->add('GET', '/health', [$healthController, 'health']);
    $router->add('OPTIONS', '/health', [$healthController, 'options']);
    
    $router->add('GET', '/ready', [$healthController, 'ready']);
    $router->add('OPTIONS', '/ready', [$healthController, 'options']);
    
    // Получение данных запроса
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $queryParams = $_GET;
    
    // Обработка запроса
    $response = $router->dispatch($requestMethod, $requestPath, $queryParams);
    
    // Если маршрут не найден - 404
    if ($response === null) {
        $response = App\Core\Response::json([
            'error' => true,
            'message' => 'Endpoint not found',
            'path' => $requestPath,
            'timestamp' => date('c')
        ], 404);
    }
    
    // Отправка ответа
    $response->send();
    
} catch (Throwable $e) {
    // Обработка непойманных исключений
    http_response_code(500);
    header('Content-Type: application/json');
    
    $errorData = [
        'error' => true,
        'message' => 'Internal server error',
        'timestamp' => date('c')
    ];
    
    // В режиме отладки добавляем детали ошибки
    if (($config['app']['debug'] ?? false) === true) {
        $errorData['debug'] = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace()
        ];
    }
    
    echo json_encode($errorData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}