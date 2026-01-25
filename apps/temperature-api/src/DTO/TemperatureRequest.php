<?php
/**
 * Data Transfer Object для запроса температуры
 * 
 * @package App\DTO
 */

namespace App\DTO;

class TemperatureRequest
{
    /**
     * Локация (комната)
     * 
     * @var string|null
     */
    private ?string $location;
    
    /**
     * Идентификатор сенсора
     * 
     * @var string|null
     */
    private ?string $sensorId;

    /**
     * Конструктор DTO
     * 
     * @param string|null $location Локация
     * @param string|null $sensorId Идентификатор сенсора
     */
    public function __construct(?string $location = null, ?string $sensorId = null)
    {
        $this->location = $location;
        $this->sensorId = $sensorId;
    }

    /**
     * Создать DTO из массива GET параметров
     * 
     * @param array $queryParams Параметры запроса
     * @return self
     */
    public static function fromArray(array $queryParams): self
    {
        $location = $queryParams['location'] ?? $queryParams['loc'] ?? null;
        $sensorId = $queryParams['sensorId'] ?? $queryParams['sensor_id'] ?? $queryParams['sensor'] ?? null;
        
        return new self($location, $sensorId);
    }

    /**
     * Получить локацию
     * 
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * Получить идентификатор сенсора
     * 
     * @return string|null
     */
    public function getSensorId(): ?string
    {
        return $this->sensorId;
    }

    /**
     * Проверить, есть ли хотя бы один параметр
     * 
     * @return bool
     */
    public function hasParameters(): bool
    {
        return !empty($this->location) || !empty($this->sensorId);
    }
}