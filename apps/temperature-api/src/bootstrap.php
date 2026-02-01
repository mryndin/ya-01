<?php
/**
 * Файл инициализации приложения
 * 
 * @package App
 */

// Установка временной зоны
date_default_timezone_set('Europe/Moscow');

// Обработка ошибок
error_reporting(E_ALL);
ini_set('display_errors', '0'); // В production отключаем вывод ошибок

// Автозагрузка классов через Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Загрузка конфигурации
$config = require __DIR__ . '/../config/app.php';

// Регистрация обработчика фатальных ошибок
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => 'Internal server error',
            'timestamp' => date('c')
        ]);
    }
});

// Возвращаем конфигурацию для использования в приложении
return $config;