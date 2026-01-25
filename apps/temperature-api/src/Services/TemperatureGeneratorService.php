<?php
/**
 * Сервис генерации температурных данных
 * 
 * @package App\Services
 */

namespace App\Services;

use App\Models\TemperatureResponse;

class TemperatureGeneratorService
{
    /**
     * Базовые температуры для различных помещений
     * 
     * @var array
     */
    private array $baseTemperatures;
    
    /**
     * Соответствие ID сенсоров локациям
     * 
     * @var array
     */
    private array $sensorIdToLocation;
    
    /**
     * Соответствие локаций ID сенсоров
     * 
     * @var array
     */
    private array $locationToSensorId;
    
    /**
     * Диапазон вариации температуры (± градусы)
     * 
     * @var float
     */
    private float $variationRange;
    
    /**
     * Точность округления (количество знаков после запятой)
     * 
     * @var int
     */
    private int $precision;

    /**
     * Конструктор сервиса
     * 
     * @param array $config Конфигурация из app.php
     */
    public function __construct(array $config)
    {
        $this->baseTemperatures = $config['temperature']['base_temperatures'];
        $this->sensorIdToLocation = $config['locations'];
        $this->locationToSensorId = $config['sensors'];
        $this->variationRange = $config['temperature']['variation_range'];
        $this->precision = $config['temperature']['precision'];
    }

    /**
     * Сгенерировать температурные данные на основе параметров запроса
     * 
     * @param string|null $location Локация (приоритет 1)
     * @param string|null $sensorId Идентификатор сенсора (приоритет 2)
     * @return TemperatureResponse Объект ответа с температурой
     */
    public function generateTemperature(?string $location, ?string $sensorId): TemperatureResponse
    {
        // Определяем локацию и sensorId на основе входных параметров
        [$finalLocation, $finalSensorId] = $this->resolveParameters($location, $sensorId);
        
        // Генерируем температуру для определенной локации
        $temperature = $this->calculateTemperature($finalLocation);
        
        // Создаем и возвращаем объект ответа
        return new TemperatureResponse(
            $finalLocation,
            $finalSensorId,
            $temperature
        );
    }

    /**
     * Определить окончательные значения локации и sensorId
     * 
     * @param string|null $location Исходная локация
     * @param string|null $sensorId Исходный sensorId
     * @return array Массив [локация, sensorId]
     */
    private function resolveParameters(?string $location, ?string $sensorId): array
    {
        // Случай 1: Локация предоставлена, sensorId - нет
        if (!empty($location) && empty($sensorId)) {
            $sensorId = $this->locationToSensorId[$location] ?? '0';
            return [$location, $sensorId];
        }
        
        // Случай 2: sensorId предоставлен, локация - нет
        if (empty($location) && !empty($sensorId)) {
            $location = $this->sensorIdToLocation[$sensorId] ?? 'Unknown';
            return [$location, $sensorId];
        }
        
        // Случай 3: Оба параметра предоставлены - используем как есть
        if (!empty($location) && !empty($sensorId)) {
            return [$location, $sensorId];
        }
        
        // Случай 4: Ни один параметр не предоставлен - значения по умолчанию
        return ['Unknown', '0'];
    }

    /**
     * Рассчитать температуру для локации
     * 
     * @param string $location Локация
     * @return float Температура с округлением
     */
    private function calculateTemperature(string $location): float
    {
        // Получаем базовую температуру для локации
        $baseTemp = $this->baseTemperatures[$location] ?? $this->baseTemperatures['Unknown'];
        
        // Генерируем случайное отклонение в пределах диапазона
        $randomDeviation = (mt_rand() / mt_getrandmax() * 2 - 1) * $this->variationRange;
        
        // Рассчитываем итоговую температуру
        $temperature = $baseTemp + $randomDeviation;
        
        // Округляем до указанной точности
        return round($temperature, $this->precision);
    }

    /**
     * Получить список всех доступных локаций
     * 
     * @return array Массив доступных локаций
     */
    public function getAvailableLocations(): array
    {
        return array_keys($this->locationToSensorId);
    }

    /**
     * Получить список всех доступных sensorId
     * 
     * @return array Массив доступных sensorId
     */
    public function getAvailableSensorIds(): array
    {
        return array_keys($this->sensorIdToLocation);
    }
}