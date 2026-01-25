<?php
/**
 * Модель ответа с температурными данными
 * 
 * @package App\Models
 */

namespace App\Models;

use DateTimeInterface;
use DateTimeImmutable;

class TemperatureResponse
{
    /**
     * Локация (комната)
     * 
     * @var string
     */
    private string $location;
    
    /**
     * Идентификатор сенсора
     * 
     * @var string
     */
    private string $sensorId;
    
    /**
     * Значение температуры
     * 
     * @var float
     */
    private float $temperature;
    
    /**
     * Единица измерения
     * 
     * @var string
     */
    private string $unit;
    
    /**
     * Временная метка измерения
     * 
     * @var DateTimeInterface
     */
    private DateTimeInterface $timestamp;

    /**
     * Конструктор класса TemperatureResponse
     * 
     * @param string $location Локация (комната)
     * @param string $sensorId Идентификатор сенсора
     * @param float $temperature Значение температуры
     * @param string $unit Единица измерения (по умолчанию '°C')
     * @param DateTimeInterface|null $timestamp Временная метка (по умолчанию текущее время)
     */
    public function __construct(
        string $location,
        string $sensorId,
        float $temperature,
        string $unit = '°C',
        ?DateTimeInterface $timestamp = null
    ) {
        $this->location = $location;
        $this->sensorId = $sensorId;
        $this->temperature = $temperature;
        $this->unit = $unit;
        $this->timestamp = $timestamp ?: new DateTimeImmutable();
    }

    /**
     * Преобразование объекта в массив для JSON ответа
     * 
     * @return array Структурированные данные ответа
     */
    public function toArray(): array
    {
        return [
            'location' => $this->location,
            'sensorId' => $this->sensorId,
            'temperature' => $this->temperature,
            'unit' => $this->unit,
            'timestamp' => $this->timestamp->format('c')
        ];
    }

    /**
     * Получить локацию
     * 
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Получить идентификатор сенсора
     * 
     * @return string
     */
    public function getSensorId(): string
    {
        return $this->sensorId;
    }

    /**
     * Получить значение температуры
     * 
     * @return float
     */
    public function getTemperature(): float
    {
        return $this->temperature;
    }

    /**
     * Получить единицу измерения
     * 
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * Получить временную метку
     * 
     * @return DateTimeInterface
     */
    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }
}