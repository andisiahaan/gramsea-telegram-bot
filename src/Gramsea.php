<?php

declare(strict_types=1);

namespace AndiSiahaan\GramseaTelegramBot;

use AndiSiahaan\GramseaTelegramBot\Exception\ApiException;

class Gramsea
{
    protected string $baseUrl;

    public function __construct(string $botToken)
    {
        if (trim($botToken) === '') {
            throw new \InvalidArgumentException('Bot token cannot be empty.');
        }

        $this->baseUrl = "https://api.telegram.org/bot{$botToken}/";
    }

    public function __call(string $method, array $arguments): array
    {
        $parameters = $arguments[0] ?? [];
        return $this->callMethod($method, $parameters);
    }

    public function callMethod(string $method, array $parameters = []): array
    {
        $response = Curl::request($this->baseUrl . $method, $parameters, 'POST');

        if (isset($response['ok']) && $response['ok'] === true) {
            return $response;
        }

        $message = $response['description'] ?? 'Telegram API error';
        $httpCode = $response['http_code'] ?? 0;

        throw new ApiException($message, $response, $httpCode);
    }

    public function getMe(): array
    {
        return $this->callMethod('getMe');
    }
}
