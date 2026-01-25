<?php
/**
 * Конфигурация приложения Temperature API
 * 
 * @package App\Config
 */

return [
    'app' => [
        'name' => 'Temperature API',
        'version' => '1.0.0',
        'environment' => getenv('APP_ENV') ?: 'production',
        'debug' => getenv('APP_DEBUG') === 'true'
    ],
    'server' => [
        'host' => '0.0.0.0',
        'port' => 8081,
        'timezone' => 'Europe/Moscow'
    ],
    'temperature' => [
        'default_unit' => '°C',
        'precision' => 1,
        'base_temperatures' => [
            'Living Room' => 22.0,
            'Bedroom' => 20.0,
            'Kitchen' => 23.0,
            'Bathroom' => 24.0,
            'Unknown' => 21.0
        ],
        'variation_range' => 2.0 // ±2 градуса
    ],
    'locations' => [
        '1' => 'Living Room',
        '2' => 'Bedroom',
        '3' => 'Kitchen'
    ],
    'sensors' => [
        'Living Room' => '1',
        'Bedroom' => '2',
        'Kitchen' => '3'
    ]
];