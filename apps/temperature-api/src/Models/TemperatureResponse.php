<?php
/**
 * Модель ответа с температурными данными
 *
 * @package App\Models
 */

namespace App\Models;

use DateTimeInterface;
use DateTimeImmutable;

// TemperatureResponse.php
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
     * @param float $value Значение температуры
     * @param string $unit Единица измерения (по умолчанию '°C')
     * @param DateTimeInterface|null $timestamp Временная метка (по умолчанию текущее время)
     */
    public function __construct(
        string $location,
        string $sensorId,
        float $value,
        string $unit = '°C',
        string $status = 'active', // По умолчанию
        string $description = 'Room temperature sensor' // По умолчанию
    ) {
        $this->location = $location;
        $this->sensorId = $sensorId;
        $this->value = $value;
        $this->unit = $unit;
        $this->status = $status;
        $this->description = $description;
        $this->timestamp = new DateTimeImmutable();
    }

    /**
     * Преобразование объекта в массив для JSON ответа
     *
     * @return array Структурированные данные ответа
     */
    public function toArray(): array
    {
        return [
            'location'    => $this->location,
            'sensorId'    => $this->sensorId,
            'value'       => $this->value,
            'unit'        => $this->unit,
            'status'      => $this->status,
            'description' => $this->description,
            'timestamp'   => $this->timestamp->format('c')
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
    public function getValue(): float
    {
        return $this->value;
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
