<?php
/**
 * Контроллер для health-check эндпоинтов
 * 
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Core\Response;

class HealthController
{
    /**
     * Конфигурация приложения
     * 
     * @var array
     */
    private array $config;

    /**
     * Конструктор контроллера
     * 
     * @param array $config Конфигурация приложения
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Health check эндпоинт
     * 
     * @return Response HTTP ответ со статусом сервиса
     */
    public function health(): Response
    {
        $healthData = [
            'status' => 'healthy',
            'service' => $this->config['app']['name'],
            'version' => $this->config['app']['version'],
            'timestamp' => date('c'),
            'environment' => $this->config['app']['environment'],
            'endpoints' => [
                'temperature' => '/temperature',
                'health' => '/health',
                'ready' => '/ready'
            ]
        ];
        
        return Response::json($healthData, 200);
    }

    /**
     * Readiness check эндпоинт
     * 
     * @return Response HTTP ответ о готовности сервиса
     */
    public function ready(): Response
    {
        $readinessData = [
            'status' => 'ready',
            'service' => $this->config['app']['name'],
            'timestamp' => date('c')
        ];
        
        return Response::json($readinessData, 200);
    }

    /**
     * Обработчик OPTIONS запроса (для CORS)
     * 
     * @return Response
     */
    public function options(): Response
    {
        return new Response('', 200, [
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type'
        ]);
    }
}