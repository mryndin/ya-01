<?php
/**
 * Класс для формирования HTTP ответов
 * 
 * @package App\Core
 */

namespace App\Core;

class Response
{
    /**
     * Тело ответа
     * 
     * @var string
     */
    private string $content;
    
    /**
     * HTTP статус код
     * 
     * @var int
     */
    private int $statusCode;
    
    /**
     * HTTP заголовки
     * 
     * @var array
     */
    private array $headers;

    /**
     * Конструктор ответа
     * 
     * @param string $content Тело ответа
     * @param int $statusCode HTTP статус код
     * @param array $headers HTTP заголовки
     */
    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = array_merge([
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Access-Control-Allow-Origin' => '*'
        ], $headers);
    }

    /**
     * Создать JSON ответ
     * 
     * @param mixed $data Данные для кодирования в JSON
     * @param int $statusCode HTTP статус код
     * @param array $headers Дополнительные заголовки
     * @return self
     */
    public static function json($data, int $statusCode = 200, array $headers = []): self
    {
        $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $jsonData = json_encode([
                'error' => 'JSON encoding error',
                'message' => json_last_error_msg()
            ]);
            $statusCode = 500;
        }
        
        $headers['Content-Type'] = 'application/json; charset=utf-8';
        
        return new self($jsonData, $statusCode, $headers);
    }

    /**
     * Отправить ответ клиенту
     * 
     * @return void
     */
    public function send(): void
    {
        // Установка HTTP статус кода
        http_response_code($this->statusCode);
        
        // Установка заголовков
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        // Отправка тела ответа
        echo $this->content;
    }

    /**
     * Получить тело ответа
     * 
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Получить статус код
     * 
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Получить заголовки
     * 
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}