<?php
/**
 * Точка входа в приложение Temperature API
 */

// Запрещаем вывод ошибок в поток, чтобы не ломать структуру JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    $config = require __DIR__ . '/../src/bootstrap.php';

    $temperatureService = new App\Services\TemperatureGeneratorService($config);
    $temperatureController = new App\Controllers\TemperatureController($temperatureService);
    $healthController = new App\Controllers\HealthController($config);

    $router = new App\Core\Router();

    // РЕГИСТРАЦИЯ МАРШРУТОВ
    $router->add('GET', '/api/v1/sensors/temperature/:location', [$temperatureController, 'getTemperature']);
    $router->add('GET', '/api/v1/sensors/temperature', [$temperatureController, 'getTemperature']);
    $router->add('GET', '/temperature', [$temperatureController, 'getTemperature']);
    $router->add('OPTIONS', '/temperature', [$temperatureController, 'options']);

    $router->add('GET', '/health', [$healthController, 'health']);
    $router->add('OPTIONS', '/health', [$healthController, 'options']);

    $router->add('GET', '/ready', [$healthController, 'ready']);
    $router->add('OPTIONS', '/ready', [$healthController, 'options']);

    // Получение данных запроса
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $rawUri = $_SERVER['REQUEST_URI'] ?? '/';

    // Чистим путь от query-строки для роутера
    $requestPath = rtrim(parse_url($rawUri, PHP_URL_PATH), '/');
    if (empty($requestPath)) $requestPath = '/';

    // Собираем параметры из $_GET
    $queryParams = $_GET;

    /**
     * ЛОГИКА ОБРАБОТКИ ПРОБЕЛОВ
     * Если Go прислал "...?location=Living Room" (с пробелом),
     * PHP может не положить это в $_GET корректно.
     */
    if (strpos($rawUri, 'location=') !== false) {
        // Вырезаем всё, что идет после 'location='
        $parts = explode('location=', $rawUri);
        if (isset($parts[1])) {
            // Декодируем на случай, если там смесь пробелов и %20
            $queryParams['location'] = urldecode($parts[1]);
        }
    }

    // Если локация была в пути (например, /temperature/Living Room)
    if (preg_match('#sensors/temperature/([^/?]+)#', $rawUri, $matches)) {
        $queryParams['location'] = urldecode($matches[1]);
    }

    // ДИСПЕТЧЕРИЗАЦИЯ
    $response = $router->dispatch($requestMethod, $requestPath, $queryParams);

    // ФОЛБЭК: Если роутер не нашел маршрут из-за пробелов в пути, вызываем контроллер напрямую
    if ($response === null && (str_contains($requestPath, 'temperature') || isset($queryParams['location']))) {
        $response = $temperatureController->getTemperature($queryParams);
    }

    // Если всё равно пусто — 404
    if ($response === null) {
        $response = App\Core\Response::json([
            'error' => true,
            'message' => 'Endpoint not found',
            'debug_uri' => $rawUri
        ], 404);
    }

    // ОТПРАВКА ОТВЕТА
    $response->send();

} catch (Throwable $e) {
    // В случае фатальной ошибки возвращаем JSON, чтобы Go не получил пустой EOF
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(500);
    }
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
