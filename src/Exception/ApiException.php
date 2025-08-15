<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot\Exception;

class ApiException extends \RuntimeException
{
    private array $response;
    private int $httpCode;

    public function __construct(string $message, array $response = [], int $httpCode = 0)
    {
        parent::__construct($message, 0);
        $this->response = $response;
        $this->httpCode = $httpCode;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
