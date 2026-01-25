<?php
/**
 * Базовое исключение API
 * 
 * @package App\Exceptions
 */

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    /**
     * HTTP статус код для исключения
     * 
     * @var int
     */
    protected int $httpStatusCode;

    /**
     * Дополнительные данные для ответа
     * 
     * @var array
     */
    protected array $additionalData;

    /**
     * Конструктор исключения API
     * 
     * @param string $message Сообщение об ошибке
     * @param int $httpStatusCode HTTP статус код
     * @param int $code Код ошибки
     * @param Exception|null $previous Предыдущее исключение
     * @param array $additionalData Дополнительные данные
     */
    public function __construct(
        string $message = "",
        int $httpStatusCode = 500,
        int $code = 0,
        ?Exception $previous = null,
        array $additionalData = []
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->httpStatusCode = $httpStatusCode;
        $this->additionalData = $additionalData;
    }

    /**
     * Получить HTTP статус код
     * 
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * Получить дополнительные данные
     * 
     * @return array
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    /**
     * Создать ответ из исключения
     * 
     * @return array Структура ответа
     */
    public function toResponse(): array
    {
        $response = [
            'error' => true,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'timestamp' => date('c')
        ];
        
        if (!empty($this->additionalData)) {
            $response['data'] = $this->additionalData;
        }
        
        return $response;
    }
}