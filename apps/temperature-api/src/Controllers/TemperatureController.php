<?php
/**
 * Контроллер для обработки запросов температуры
 * 
 * @package App\Controllers
 */

namespace App\Controllers;

use App\Services\TemperatureGeneratorService;
use App\DTO\TemperatureRequest;
use App\Core\Response;

class TemperatureController
{
    /**
     * Сервис генерации температуры
     * 
     * @var TemperatureGeneratorService
     */
    private TemperatureGeneratorService $temperatureService;

    /**
     * Конструктор контроллера
     * 
     * @param TemperatureGeneratorService $temperatureService
     */
    public function __construct(TemperatureGeneratorService $temperatureService)
    {
        $this->temperatureService = $temperatureService;
    }

    /**
     * Обработчик GET запроса /temperature
     * 
     * @param array $queryParams Параметры запроса
     * @return Response HTTP ответ в формате JSON
     */
    public function getTemperature(array $queryParams): Response
    {
        try {
            // Создаем DTO из параметров запроса
            $request = TemperatureRequest::fromArray($queryParams);
            
            // Генерируем температурные данные
            $temperatureResponse = $this->temperatureService->generateTemperature(
                $request->getLocation(),
                $request->getSensorId()
            );
            
            // Возвращаем успешный ответ
            return Response::json($temperatureResponse->toArray(), 200);
            
        } catch (\Exception $e) {
            // Логируем ошибку (в production можно использовать Monolog)
            error_log("Temperature API Error: " . $e->getMessage());
            
            // Возвращаем ошибку сервера
            return Response::json([
                'error' => 'Internal server error',
                'message' => 'Unable to generate temperature data'
            ], 500);
        }
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